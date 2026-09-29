<?php

namespace App\Services\Auth;

use App\Exceptions\OidcExchangeException;
use App\Models\AuthAttempt;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TelegramOidcService — OIDC authorization-code exchange with PKCE.
 *
 * Per DFM-FDS-1.4.md §218 (LOCKED):
 *   "exchanges the code server-side, validates Telegram signature/JWKS
 *    and issuer/audience/expiry/nonce, then upserts users by verified subject."
 *
 * Flow:
 *   1. exchangeCode()      — POST token_url with code + verifier
 *   2. validateIdToken()   — JWKS + issuer + audience + expiry + nonce
 *   3. upsertUser()        — find/create by telegram_subject
 *   4. markReauth()        — update recently_authenticated_at
 */
class TelegramOidcService
{
    /** JWKS cache TTL (seconds). */
    public const JWKS_TTL = 3600;

    /** Clock skew tolerance (seconds). */
    public const CLOCK_SKEW = 60;

    public function __construct(
        private readonly AuthAttemptService $attemptService,
    ) {}

    /**
     * Exchange authorization code + PKCE verifier for ID token.
     *
     * @return array{id_token: string, access_token: ?string, raw: array}
     *
     * @throws OidcExchangeException
     */
    public function exchangeCode(string $code, string $codeVerifier): array
    {
        $tokenUrl = config('services.telegram.oidc.token_url');
        $clientId = config('services.telegram.client_id');
        $clientSecret = config('services.telegram.client_secret');
        $redirectUri = config('services.telegram.redirect_uri');

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post($tokenUrl, [
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'code_verifier' => $codeVerifier,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                ]);
        } catch (\Throwable $e) {
            Log::warning('OIDC token endpoint unreachable', ['error' => $e->getMessage()]);
            throw new OidcExchangeException(OidcExchangeException::REASON_CODE_EXCHANGE_FAILED);
        }

        if (!$response->successful()) {
            Log::warning('OIDC token endpoint returned error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            throw new OidcExchangeException(OidcExchangeException::REASON_CODE_EXCHANGE_FAILED);
        }

        $body = $response->json();

        if (empty($body['id_token'])) {
            throw new OidcExchangeException(OidcExchangeException::REASON_INVALID_TOKEN, 'No id_token in response.');
        }

        return [
            'id_token'     => $body['id_token'],
            'access_token' => $body['access_token'] ?? null,
            'raw'          => $body,
        ];
    }

    /**
     * Validate ID token: JWKS signature + issuer + audience + expiry + nonce.
     *
     * @return array Decoded claims
     *
     * @throws OidcExchangeException
     */
    public function validateIdToken(string $idToken, AuthAttempt $attempt): array
    {
        // ── 1. Fetch JWKS (cached) ──
        $jwksUrl = config('services.telegram.oidc.jwks_url');
        $jwks = Cache::remember('telegram_jwks', self::JWKS_TTL, function () use ($jwksUrl) {
            try {
                $response = Http::timeout(10)->get($jwksUrl);
                if (!$response->successful()) {
                    return null;
                }
                return $response->json();
            } catch (\Throwable $e) {
                Log::warning('JWKS fetch failed', ['error' => $e->getMessage()]);
                return null;
            }
        });

        if (!$jwks || empty($jwks['keys'])) {
            throw new OidcExchangeException(OidcExchangeException::REASON_JWKS_UNAVAILABLE);
        }

        // ── 2. Verify signature ──
        JWT::$leeway = self::CLOCK_SKEW;

        try {
            $keys = JWK::parseKeySet($jwks);
            $decoded = JWT::decode($idToken, $keys);
            $claims = (array) $decoded;
        } catch (\Throwable $e) {
            Log::warning('JWT decode failed', ['error' => $e->getMessage()]);
            throw new OidcExchangeException(OidcExchangeException::REASON_INVALID_TOKEN);
        }

        // ── 3. Validate issuer ──
        $expectedIssuer = config('services.telegram.oidc.issuer');
        if (($claims['iss'] ?? null) !== $expectedIssuer) {
            throw new OidcExchangeException(OidcExchangeException::REASON_ISSUER_MISMATCH);
        }

        // ── 4. Validate audience (must include client_id) ──
        $expectedAud = config('services.telegram.client_id');
        $aud = $claims['aud'] ?? null;
        $audList = is_array($aud) ? $aud : [$aud];

        if (!in_array($expectedAud, $audList, true)) {
            throw new OidcExchangeException(OidcExchangeException::REASON_AUDIENCE_MISMATCH);
        }

        // ── 5. Validate expiry ──
        $exp = $claims['exp'] ?? null;
        if ($exp === null || $exp + self::CLOCK_SKEW < time()) {
            throw new OidcExchangeException(OidcExchangeException::REASON_EXPIRED);
        }

        // ── 6. Validate nonce (must match the attempt's stored nonce) ──
        $tokenNonce = $claims['nonce'] ?? null;
        if ($tokenNonce === null || !hash_equals($attempt->nonce_hash, hash('sha256', $tokenNonce))) {
            throw new OidcExchangeException(OidcExchangeException::REASON_NONCE_MISMATCH);
        }

        // ── 7. Require subject ──
        if (empty($claims['sub'])) {
            throw new OidcExchangeException(OidcExchangeException::REASON_MISSING_SUBJECT);
        }

        return $claims;
    }

    /**
     * Find or create user by verified Telegram subject.
     *
     * @throws OidcExchangeException
     */
    public function upsertUser(array $claims): User
    {
        $subject = $claims['sub'];

        $user = User::where('telegram_subject', $subject)->first();

        $userData = [
            'full_name'         => $claims['name'] ?? $claims['preferred_username'] ?? $subject,
            'profile_photo_url' => $claims['picture'] ?? null,
            'phone_number'      => $claims['phone_number'] ?? null,
            'last_login_at'     => now(),
        ];

        if ($user) {
            $user->fill($userData);
            $user->version = $user->version + 1;
            $user->save();
            return $user;
        }

        return User::create(array_merge($userData, [
            'telegram_subject' => $subject,
            'status'           => 'ACTIVE',
            'version'          => 1,
        ]));
    }

    /**
     * Mark user as freshly authenticated (5-min window per Auth Contract §3).
     */
    public function markReauth(User $user): void
    {
        $user->recently_authenticated_at = now();
        $user->save();
    }

    /**
     * Full flow: exchange → validate → upsert → mark reauth.
     *
     * @return array{user: User, claims: array}
     *
     * @throws OidcExchangeException
     */
    public function completeLogin(string $code, AuthAttempt $attempt): array
    {
        $verifier = $this->attemptService->getPkceVerifier($attempt);

        $token = $this->exchangeCode($code, $verifier);
        $claims = $this->validateIdToken($token['id_token'], $attempt);
        $user = $this->upsertUser($claims);
        $this->markReauth($user);

        return ['user' => $user, 'claims' => $claims];
    }
}

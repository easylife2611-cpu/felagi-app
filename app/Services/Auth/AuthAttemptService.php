<?php

namespace App\Services\Auth;

use App\Models\AuthAttempt;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * AuthAttemptService — PKCE (RFC 7636) OIDC authorization-code flow.
 *
 * Per DFM §218 (LOCKED):
 *   "Telegram login uses an OIDC authorization-code flow with PKCE,
 *    state and nonce... validates signature/JWKS and
 *    issuer/audience/expiry/nonce... redirects only a single-use,
 *    short-lived handoff code."
 *
 * WP-27b: handoff code carries HMAC-signed user_id so the app-token
 * exchange is race-free (D-091 fix). No schema change — user_id is
 * embedded in the signed payload, not stored on auth_attempts.
 */
class AuthAttemptService
{
    /** Auth attempt TTL (minutes). */
    public const TTL_MINUTES = 15;

    /** Handoff code TTL (seconds). */
    public const HANDOFF_TTL_SECONDS = 60;

    /** PKCE verifier length (RFC 7636: 43-128 chars). */
    public const PKCE_VERIFIER_LENGTH = 64;

    // ─── Public API ───

    public function create(string $returnUriAllowlisted): array
    {
        $state     = Str::random(64);
        $nonce     = Str::random(64);
        $verifier  = $this->generatePkceVerifier();
        $challenge = $this->generatePkceChallenge($verifier);

        $attempt = AuthAttempt::create([
            'state_hash'              => hash('sha256', $state),
            'nonce_hash'              => hash('sha256', $nonce),
            'pkce_verifier_encrypted' => $verifier,
            'handoff_hash'            => null,
            'return_uri_allowlisted'  => $returnUriAllowlisted,
            'expires_at'              => now()->addMinutes(self::TTL_MINUTES),
            'consumed_at'             => null,
            'created_at'              => now(),
        ]);

        return [
            'attempt'               => $attempt,
            'state'                 => $state,
            'nonce'                 => $nonce,
            'code_challenge'        => $challenge,
            'code_challenge_method' => 'S256',
        ];
    }

    public function findByState(string $state): ?AuthAttempt
    {
        return AuthAttempt::query()
            ->where('state_hash', hash('sha256', $state))
            ->active()
            ->first();
    }

    public function validateNonce(AuthAttempt $attempt, string $nonce): bool
    {
        return hash_equals($attempt->nonce_hash, hash('sha256', $nonce));
    }

    public function getPkceVerifier(AuthAttempt $attempt): string
    {
        return (string) $attempt->pkce_verifier_encrypted;
    }

    /**
     * Generate HMAC-signed handoff code.
     *
     * Payload structure (base64url JSON):
     *   a = attempt_id
     *   e = expires_at (unix timestamp)
     *   u = user_id (UUID) — optional, added after successful OIDC login
     *
     * Signature: HMAC-SHA256 over base64url payload, hex-encoded.
     * Final format: <base64url_payload>.<hex_signature>
     *
     * The handoff_hash stored in DB is SHA-256 of the FULL signed code,
     * so single-use enforcement remains intact.
     */
    public function generateHandoff(AuthAttempt $attempt, ?User $user = null): string
    {
        $payload = [
            'a' => $attempt->id,
            'e' => time() + self::HANDOFF_TTL_SECONDS,
        ];

        if ($user) {
            $payload['u'] = $user->id;
        }

        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $b64  = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
        $sig  = hash_hmac('sha256', $b64, $this->signingKey());

        $handoff = $b64 . '.' . $sig;

        $attempt->handoff_hash = hash('sha256', $handoff);
        $attempt->save();

        return $handoff;
    }

    /**
     * Verify + consume handoff code (single-use).
     *
     * Returns:
     *   ['attempt' => AuthAttempt, 'user' => ?User]
     * or null on failure (invalid signature / expired / already consumed).
     */
    public function consumeByHandoff(string $handoff): ?array
    {
        // ── 1. Split + verify HMAC ──
        $parts = explode('.', $handoff, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$b64, $sig] = $parts;

        $expectedSig = hash_hmac('sha256', $b64, $this->signingKey());
        if (!hash_equals($expectedSig, $sig)) {
            return null;
        }

        // ── 2. Decode payload ──
        $padded = str_pad($b64, (int) ceil(strlen($b64) / 4) * 4, '=', STR_PAD_RIGHT);
        $json = base64_decode(strtr($padded, '-_', '+/'), true);
        if ($json === false) {
            return null;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            return null;
        }

        // ── 3. Check payload expiry ──
        if (($payload['e'] ?? 0) < time()) {
            return null;
        }

        // ── 4. Find unconsumed attempt (single-use) ──
        $attempt = AuthAttempt::query()
            ->where('handoff_hash', hash('sha256', $handoff))
            ->whereNull('consumed_at')
            ->first();

        if (!$attempt) {
            return null;
        }

        // ── 5. Resolve embedded user (if any) ──
        $user = null;
        if (!empty($payload['u'])) {
            $user = User::find($payload['u']);
        }

        $attempt->markConsumed();

        return ['attempt' => $attempt, 'user' => $user];
    }

    public function cleanupExpired(): int
    {
        return AuthAttempt::query()
            ->where('expires_at', '<', now())
            ->whereNull('consumed_at')
            ->delete();
    }

    // ─── Helpers ───

    protected function signingKey(): string
    {
        return config('app.key');
    }

    protected function generatePkceVerifier(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $verifier = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < self::PKCE_VERIFIER_LENGTH; $i++) {
            $verifier .= $alphabet[random_int(0, $max)];
        }

        return $verifier;
    }

    protected function generatePkceChallenge(string $verifier): string
    {
        $hash = hash('sha256', $verifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }
}

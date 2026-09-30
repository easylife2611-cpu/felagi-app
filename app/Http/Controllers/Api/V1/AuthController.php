<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OidcExchangeException;
use App\Models\AuthAttempt;
use App\Models\User;
use App\Services\Auth\AuthAttemptService;
use App\Services\Auth\TelegramOidcService;
use App\Services\Auth\TelegramWidgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    public function __construct(
        private readonly AuthAttemptService $attemptService,
        private readonly TelegramOidcService $oidcService,
        private readonly TelegramWidgetService $widgetService,
    ) {}

    /**
     * POST /api/v1/auth/telegram/start
     * Initiate Telegram OIDC flow with PKCE (DFM §218).
     */
    public function telegramStart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'return_uri' => ['required', 'url', 'max:500'],
            'scope'      => ['nullable', 'string', 'max:200'],
        ]);

        $allowedHosts = config('auth.telegram_allowed_hosts', []);
        $returnHost = parse_url($validated['return_uri'], PHP_URL_HOST);
        if (!empty($allowedHosts) && !in_array($returnHost, $allowedHosts, true)) {
            return $this->error('URI_NOT_ALLOWED', 'Return URI not allowed.', 422);
        }

        $created = $this->attemptService->create($validated['return_uri']);

        $authUrl = config('services.telegram.oidc.authorization_url') . '?' . http_build_query([
            'client_id'             => config('services.telegram.client_id', ''),
            'bot_id'                => config('services.telegram.bot_id', config('services.telegram.client_id', '')),
            'origin'                => parse_url(config('app.url'), PHP_URL_HOST) ?: 'zagcreativity.com',
            'redirect_uri'          => config('services.telegram.redirect_uri', ''),
            'response_type'         => 'code',
            'scope'                 => $validated['scope'] ?? 'openid profile',
            'state'                 => $created['state'],
            'nonce'                 => $created['nonce'],
            'code_challenge'        => $created['code_challenge'],
            'code_challenge_method' => $created['code_challenge_method'],
        ]);

        return $this->success([
            'auth_url'   => $authUrl,
            'attempt_id' => $created['attempt']->id,
            'expires_at' => $created['attempt']->expires_at->toIso8601String(),
        ], 'Auth attempt created.');
    }

    /**
     * GET /api/v1/auth/telegram/callback
     *
     * Telegram redirects here with ?code=X&state=Y after user consent.
     *
     * Server-side:
     *   1. Find auth_attempt by state (must be active)
     *   2. Exchange code + PKCE verifier for ID token
     *   3. Validate ID token (JWKS + issuer + audience + expiry + nonce)
     *   4. Upsert user by verified subject
     *   5. Mark reauth
     *   6. Generate single-use handoff code
     *   7. Redirect to app's return_uri?handoff_code=Z
     *
     * Per DFM §218: "the backend redirects only a single-use, short-lived
     * handoff code to the app, and Flutter exchanges it for app tokens."
     */
    public function telegramCallback(Request $request): JsonResponse|RedirectResponse
    {
        $state = $request->query('state');
        $code = $request->query('code');
        $error = $request->query('error');

        // Handle OAuth error from provider
        if ($error) {
            return $this->error(
                'OIDC_PROVIDER_ERROR',
                'Telegram returned an error: ' . $error,
                400,
            );
        }

        if (!$state || !$code) {
            return $this->error('INVALID_CALLBACK', 'Missing state or code.', 400);
        }

        try {
            $attempt = $this->attemptService->findByState($state);
            if (!$attempt) {
                throw new OidcExchangeException(OidcExchangeException::REASON_ATTEMPT_NOT_FOUND);
            }

            // Exchange + validate + upsert (atomic in service)
            ['user' => $user, 'claims' => $claims] = $this->oidcService->completeLogin($code, $attempt);

            // Generate handoff code (single-use)
            $handoff = $this->attemptService->generateHandoff($attempt, $user);

            // Determine redirect target
            $returnUri = $attempt->return_uri_allowlisted;
            $separator = str_contains($returnUri, '?') ? '&' : '?';
            $redirectUrl = $returnUri . $separator . 'handoff_code=' . urlencode($handoff);

            // If API consumer (Accept: application/json) → JSON; else redirect
            if ($request->wantsJson()) {
                return $this->success([
                    'handoff_code' => $handoff,
                    'redirect_url' => $redirectUrl,
                    'user'         => [
                        'id'       => $user->id,
                        'full_name'=> $user->full_name,
                        'status'   => $user->status,
                    ],
                ], 'OIDC callback processed.');
            }

            return redirect()->away($redirectUrl);
        } catch (OidcExchangeException $e) {
            return $this->error('OIDC_EXCHANGE_FAILED', $e->getMessage(), 401, $e->toArray());
        }
    }

    /**
     * POST /api/v1/auth/telegram/exchange
     *
     * Flutter exchanges the single-use handoff code for app tokens.
     *
     * Per DFM §218: "Flutter exchanges it for app tokens."
     * Never pass ID token / refresh token / long-lived token in a deep link.
     */
    public function telegramExchange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'handoff_code' => ['required', 'string', 'max:200'],
            'device_name'  => ['nullable', 'string', 'max:120'],
        ]);

        try {
            // WP-27b: HMAC-signed handoff carries user_id — race-free (D-091 fix)
            $result = $this->attemptService->consumeByHandoff($validated['handoff_code']);

            if (!$result) {
                throw new OidcExchangeException(OidcExchangeException::REASON_HANDOFF_INVALID);
            }

            ['attempt' => $attempt, 'user' => $user] = $result;

            if (!$user) {
                // Handoff signed without user binding (pre-WP-27b compatibility)
                throw new OidcExchangeException(OidcExchangeException::REASON_ATTEMPT_NOT_FOUND);
            }

            // Issue Sanctum token (single-use handoff → long-lived app token)
            $tokenName = $validated['device_name'] ?? 'app-handoff';
            $token = $user->createToken($tokenName);

            return $this->success([
                'access_token' => $token->plainTextToken,
                'token_type'   => 'Bearer',
                'user'         => [
                    'id'          => $user->id,
                    'full_name'   => $user->full_name,
                    'status'      => $user->status,
                    'rating_score'=> $user->rating_score,
                    'rating_count'=> $user->rating_count,
                ],
            ], 'Authentication complete.');
        } catch (OidcExchangeException $e) {
            return $this->error('OIDC_EXCHANGE_FAILED', $e->getMessage(), 401, $e->toArray());
        }
    }

    /**
     * POST /api/v1/auth/telegram/widget/start
     *
     * Widget flow: creates auth attempt, returns widget config.
     * Frontend injects Telegram Widget script with callback_url.
     */
    public function telegramWidgetStart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'return_uri' => ['required', 'url', 'max:500'],
        ]);

        $allowedHosts = config('auth.telegram_allowed_hosts', []);
        $returnHost = parse_url($validated['return_uri'], PHP_URL_HOST);
        if (!empty($allowedHosts) && !in_array($returnHost, $allowedHosts, true)) {
            return $this->error('URI_NOT_ALLOWED', 'Return URI not allowed.', 422);
        }

        $created = $this->attemptService->create($validated['return_uri']);

        $callbackUrl = url('/api/v1/auth/telegram/widget/callback')
            . '?state=' . urlencode($created['state']);

        return $this->success([
            'bot_username' => config('services.telegram.bot_username', 'FelagiMarketBot'),
            'callback_url' => $callbackUrl,
            'attempt_id'   => $created['attempt']->id,
            'expires_at'   => $created['attempt']->expires_at->toIso8601String(),
        ], 'Widget auth attempt created.');
    }

    /**
     * GET /api/v1/auth/telegram/widget/callback
     *
     * Telegram Widget redirects user here with:
     *   ?id=X&first_name=Y&auth_date=Z&hash=H&state=S
     *
     * 1. Verify HMAC-SHA256 hash
     * 2. Find auth attempt by state
     * 3. Upsert user by telegram_subject
     * 4. Generate single-use handoff code (reuses WP-27b)
     * 5. Redirect to return_uri?handoff_code=X
     */
    public function telegramWidgetCallback(Request $request): JsonResponse|RedirectResponse
    {
        $state = $request->query('state');
        if (!$state) {
            return $this->error('INVALID_CALLBACK', 'Missing state.', 400);
        }

        try {
            $verified = $this->widgetService->verify($request->query());
        } catch (OidcExchangeException $e) {
            return $this->error('WIDGET_VERIFICATION_FAILED', $e->getMessage(), 401, $e->toArray());
        }

        $attempt = $this->attemptService->findByState($state);
        if (!$attempt) {
            return $this->error('ATTEMPT_NOT_FOUND', 'Auth attempt not found.', 401);
        }

        $result = $this->widgetService->upsertUser($verified);
        $user = $result['user'];

        $handoff = $this->attemptService->generateHandoff($attempt, $user);

        $returnUri = $attempt->return_uri_allowlisted;
        $separator = str_contains($returnUri, '?') ? '&' : '?';
        $redirectUrl = $returnUri . $separator . 'handoff_code=' . urlencode($handoff);

        if ($request->wantsJson()) {
            return $this->success([
                'handoff_code' => $handoff,
                'redirect_url' => $redirectUrl,
                'user'         => [
                    'id'        => $user->id,
                    'full_name' => $user->full_name,
                    'status'    => $user->status,
                ],
            ], 'Widget callback processed.');
        }

        return redirect()->away($redirectUrl);
    }

    /**
     * POST /api/v1/auth/refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => ['required', 'string', 'max:500'],
        ]);

        return $this->error('TOKEN_REVOKED', 'Invalid or revoked token.', 401);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(null, 204);
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('UNAUTHENTICATED', 'No active session.', 401);
        }

        return $this->success([
            'id'                 => $user->id,
            'full_name'          => $user->full_name,
            'telegram_subject'   => $user->telegram_subject,
            'profile_photo_url'  => $user->profile_photo_url,
            'status'             => $user->status,
            'rating_score'       => $user->rating_score,
            'rating_count'       => $user->rating_count,
            'roles'              => $user->roles()->whereNull('revoked_at')->pluck('role'),
        ], 'Profile retrieved.');
    }

    /**
     * PATCH /api/v1/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('UNAUTHENTICATED', 'No active session.', 401);
        }

        $validated = $request->validate([
            'full_name'         => ['sometimes', 'string', 'min:1', 'max:150'],
            'phone_number'      => ['sometimes', 'nullable', 'string', 'max:30'],
            'profile_photo_url' => ['sometimes', 'nullable', 'url', 'max:1000'],
        ]);

        $user->fill($validated);
        $user->version = $user->version + 1;
        $user->save();

        return $this->success($user->fresh(), 'Profile updated.');
    }

    /**
     * POST /api/v1/profile/photo
     * Upload / replace the authenticated user's profile photo.
     */
    public function uploadProfilePhoto(\App\Http\Requests\Profile\UpdatePhotoRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
        }

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'profile-photos/' . $user->id . '-' . time() . '.' . strtolower($ext);

        $stored = $file->storeAs('profile-photos', basename($filename), 'public');
        if (!$stored) {
            return $this->error('UPLOAD_FAILED', 'Could not store photo.', 500);
        }

        $user->profile_photo_path = $stored;
        $user->profile_photo_url = \Illuminate\Support\Facades\Storage::disk('public')->url($stored);
        $user->save();

        return $this->success([
            'profile_photo_path' => $user->profile_photo_path,
            'profile_photo_url' => $user->profile_photo_url,
        ], 'Photo uploaded.');
    }

}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Services\Auth\AuthAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    public function __construct(
        private readonly AuthAttemptService $attemptService,
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

        // Allowlist return_uri (deployment config)
        $allowedHosts = config('auth.telegram_allowed_hosts', []);
        $returnHost = parse_url($validated['return_uri'], PHP_URL_HOST);
        if (!empty($allowedHosts) && !in_array($returnHost, $allowedHosts, true)) {
            return $this->error('URI_NOT_ALLOWED', 'Return URI not allowed.', 422);
        }

        // WP-05a: Create auth attempt with PKCE
        $created = $this->attemptService->create($validated['return_uri']);

        // Build OAuth URL with PKCE challenge
        $authUrl = 'https://oauth.telegram.org/auth?' . http_build_query([
            'client_id'             => config('services.telegram.client_id', ''),
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
     * POST /api/v1/auth/telegram/exchange
     * Exchange handoff code for app tokens.
     *
     * Full OIDC exchange → WP-27 (requires Telegram credentials).
     */
    public function telegramExchange(Request $request): JsonResponse
    {
        $request->validate([
            'handoff_code' => ['required', 'string', 'max:200'],
        ]);

        return $this->error(
            'NOT_IMPLEMENTED',
            'Telegram OIDC exchange requires provider credentials (WP-27).',
            501
        );
    }

    /**
     * POST /api/v1/auth/refresh
     * Rotate refresh token.
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
     * Revoke current session.
     */
    public function logout(Request $request): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * GET /api/v1/auth/me
     * Return current user profile.
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
     * Update current user profile.
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
}

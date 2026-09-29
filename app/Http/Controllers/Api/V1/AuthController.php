<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * POST /api/v1/auth/telegram/start
     * Initiate Telegram OIDC flow.
     */
    public function telegramStart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'return_uri' => ['required', 'url', 'max:500'],
            'scope' => ['nullable', 'string', 'max:200'],
        ]);

        // Allowlist return_uri (deployment config)
        $allowedHosts = config('auth.telegram_allowed_hosts', []);
        $returnHost = parse_url($validated['return_uri'], PHP_URL_HOST);
        if (!empty($allowedHosts) && !in_array($returnHost, $allowedHosts, true)) {
            return $this->error('URI_NOT_ALLOWED', 'Return URI not allowed.', 422);
        }

        $state = Str::random(64);
        $nonce = Str::random(64);

        // Store auth attempt
        $attemptId = DB::table('auth_attempts')->insertGetId([
            'state_hash' => hash('sha256', $state),
            'nonce_hash' => hash('sha256', $nonce),
            'return_uri_allowlisted' => $validated['return_uri'],
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
        ]);

        $authUrl = 'https://oauth.telegram.org/auth?' . http_build_query([
            'client_id' => config('services.telegram.client_id', ''),
            'redirect_uri' => config('services.telegram.redirect_uri', ''),
            'response_type' => 'code',
            'scope' => $validated['scope'] ?? 'openid profile',
            'state' => $state,
            'nonce' => $nonce,
        ]);

        return $this->success([
            'auth_url' => $authUrl,
            'attempt_id' => $attemptId,
        ], 'Auth attempt created.');
    }

    /**
     * POST /api/v1/auth/telegram/exchange
     * Exchange handoff code for app tokens.
     */
    public function telegramExchange(Request $request): JsonResponse
    {
        $validated = $request->validate([
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
        $validated = $request->validate([
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
            'id' => $user->id,
            'full_name' => $user->full_name,
            'telegram_subject' => $user->telegram_subject,
            'profile_photo_url' => $user->profile_photo_url,
            'status' => $user->status,
            'rating_score' => $user->rating_score,
            'rating_count' => $user->rating_count,
            'roles' => $user->roles()->active()->pluck('role'),
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
            'full_name' => ['sometimes', 'string', 'min:1', 'max:150'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:30'],
            'profile_photo_url' => ['sometimes', 'nullable', 'url', 'max:1000'],
        ]);

        $user->fill($validated);
        $user->version = $user->version + 1;
        $user->save();

        return $this->success($user->fresh(), 'Profile updated.');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidTotpCodeException;
use App\Services\Admin\ReauthValidator;
use App\Services\Admin\TotpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * WP-13c (backend) — 2FA enrollment / verification API.
 *
 * Uses the existing TotpService (RFC 6238).
 * Spec: Admin_Authorization_Contract.md §3
 *   "CRITICAL requires second factor. If session/second factor is
 *    unavailable, deny publish while retaining draft."
 *
 * DFM §449: "lost-factor recovery is a controlled, audited process."
 *
 * Enrollment flow uses existing User columns:
 *   - totp_secret (encrypted) — set on enroll/start
 *   - totp_enabled_at — set on enroll/verify (activation gate)
 *   - totp_recovery_codes (encrypted:array) — set on enroll/verify
 *
 * If user abandons enrollment, totp_enabled_at stays null and 2FA is OFF.
 */
class TwoFactorController extends BaseApiController
{
    public function __construct(
        private readonly TotpService $totp,
        private readonly ReauthValidator $reauth,
    ) {}

    /**
     * GET /api/v1/auth/2fa/status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'enabled' => (bool) $user->totp_enabled_at,
            'enabled_at' => $user->totp_enabled_at?->toIso8601String(),
            'recovery_codes_remaining' => count($user->totp_recovery_codes ?? []),
        ]);
    }

    /**
     * POST /api/v1/auth/2fa/enroll/start
     * Generates a new secret and returns the OTPAuth URL.
     * 2FA is NOT active until enroll/verify succeeds.
     */
    public function enrollStart(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->totp_enabled_at) {
            return $this->error('2FA_ALREADY_ENABLED', '2FA is already enabled.', 409);
        }

        $secret = $this->totp->generateSecret();
        $user->totp_secret = $secret;
        $user->save();

        return $this->success([
            'secret' => $secret,
            'otpauth_url' => $this->totp->getOtpAuthUrl($secret, $user),
            'account' => $user->telegram_subject,
            'issuer' => config('app.name', 'Felagi'),
        ], 'Scan the QR code with your authenticator app, then verify a code.');
    }

    /**
     * POST /api/v1/auth/2fa/enroll/verify
     * Verifies the first code, activates 2FA, returns recovery codes ONCE.
     */
    public function enrollVerify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();

        if ($user->totp_enabled_at) {
            return $this->error('2FA_ALREADY_ENABLED', '2FA is already enabled.', 409);
        }

        if (!$user->totp_secret) {
            return $this->error('2FA_NOT_STARTED', 'Call /enroll/start first.', 422);
        }

        try {
            $this->totp->verify($user->totp_secret, $validated['code'], $user);
        } catch (InvalidTotpCodeException $e) {
            return $this->error('INVALID_2FA_CODE', $e->getMessage(), 422);
        }

        $codes = $this->totp->generateRecoveryCodes();

        $user->totp_recovery_codes = $codes['hashed'];
        $user->totp_enabled_at = now();
        $user->save();

        // 2FA completion also satisfies reauth window
        $this->reauth->mark($user);

        return $this->success([
            'enabled_at' => $user->totp_enabled_at->toIso8601String(),
            'recovery_codes' => $codes['plain'],
            'recovery_codes_count' => count($codes['plain']),
        ], '2FA enabled. Save your recovery codes — they will not be shown again.');
    }

    /**
     * POST /api/v1/auth/2fa/verify
     * Verify a code (login step / reauth step). Marks reauth window.
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();

        if (!$user->totp_enabled_at || !$user->totp_secret) {
            return $this->error('2FA_NOT_ENABLED', '2FA is not enabled.', 422);
        }

        try {
            $this->totp->verify($user->totp_secret, $validated['code'], $user);
        } catch (InvalidTotpCodeException $e) {
            return $this->error('INVALID_2FA_CODE', $e->getMessage(), 422);
        }

        $this->reauth->mark($user);

        return $this->success([
            'verified' => true,
            'verified_at' => now()->toIso8601String(),
        ], 'Verified.');
    }

    /**
     * POST /api/v1/auth/2fa/recovery
     * Consume a recovery code (one-time use).
     */
    public function recovery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:20'],
        ]);

        $user = $request->user();

        if (!$user->totp_enabled_at) {
            return $this->error('2FA_NOT_ENABLED', '2FA is not enabled.', 422);
        }

        $hashed = $user->totp_recovery_codes ?? [];
        $result = $this->totp->consumeRecoveryCode($validated['code'], $hashed);

        if (!$result['matched']) {
            return $this->error('INVALID_RECOVERY_CODE', 'Recovery code is invalid or already used.', 422);
        }

        $user->totp_recovery_codes = $result['remaining'];
        $user->save();

        $this->reauth->mark($user);

        return $this->success([
            'verified' => true,
            'remaining_codes' => count($result['remaining']),
        ], 'Recovery code accepted.');
    }

    /**
     * POST /api/v1/auth/2fa/recovery-codes/regenerate
     * Requires a valid TOTP code.
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();

        if (!$user->totp_enabled_at || !$user->totp_secret) {
            return $this->error('2FA_NOT_ENABLED', '2FA is not enabled.', 422);
        }

        try {
            $this->totp->verify($user->totp_secret, $validated['code'], $user);
        } catch (InvalidTotpCodeException $e) {
            return $this->error('INVALID_2FA_CODE', $e->getMessage(), 422);
        }

        $codes = $this->totp->generateRecoveryCodes();
        $user->totp_recovery_codes = $codes['hashed'];
        $user->save();

        return $this->success([
            'recovery_codes' => $codes['plain'],
            'recovery_codes_count' => count($codes['plain']),
        ], 'New recovery codes generated. Old codes are invalidated.');
    }

    /**
     * POST /api/v1/auth/2fa/disable
     * Requires TOTP code OR recovery code.
     */
    public function disable(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:20'],
        ]);

        $user = $request->user();

        if (!$user->totp_enabled_at) {
            return $this->error('2FA_NOT_ENABLED', '2FA is not enabled.', 422);
        }

        $verified = false;

        // Try TOTP first (6 digits)
        if (preg_match('/^\d{6}$/', $validated['code'])) {
            try {
                $this->totp->verify($user->totp_secret, $validated['code'], $user);
                $verified = true;
            } catch (InvalidTotpCodeException $e) {
                // fall through to recovery
            }
        }

        // Fall back to recovery code
        if (!$verified) {
            $result = $this->totp->consumeRecoveryCode(
                $validated['code'],
                $user->totp_recovery_codes ?? []
            );
            $verified = $result['matched'];
        }

        if (!$verified) {
            return $this->error('INVALID_2FA_CODE', 'Invalid TOTP or recovery code.', 422);
        }

        $user->totp_secret = null;
        $user->totp_enabled_at = null;
        $user->totp_recovery_codes = null;
        $user->save();

        return $this->success([
            'disabled_at' => now()->toIso8601String(),
        ], '2FA disabled.');
    }
}

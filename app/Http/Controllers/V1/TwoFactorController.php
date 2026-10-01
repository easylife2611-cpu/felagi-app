<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Two-Factor Authentication (TOTP) — L302 fresh implementation
 */
class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorService $totp) {}

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(['data' => [
            'enabled'                  => $this->totp->isEnabled($user),
            'enabled_at'               => $user->totp_enabled_at,
            'recovery_codes_remaining' => is_array($user->totp_recovery_codes)
                ? count($user->totp_recovery_codes)
                : 0,
        ]]);
    }

    public function enrollStart(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($this->totp->isEnabled($user)) {
            return response()->json(['message' => '2FA already enabled'], 409);
        }

        $secret = $this->totp->generateSecret();
        $user->forceFill(['totp_secret' => $secret])->save();

        $url = $this->totp->otpauthUrl($user, $secret);
        $svg = $this->totp->qrSvg($url);

        return response()->json(['data' => [
            'secret'      => $secret,
            'otpauth_url' => $url,
            'qr_svg'      => $svg,
        ]]);
    }

    public function enrollVerify(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|size:6']);
        $user = $request->user();

        if (empty($user->totp_secret)) {
            return response()->json(['message' => 'Enrollment not started'], 400);
        }

        if (! $this->totp->verifyCode($user->totp_secret, $data['code'])) {
            return response()->json(['message' => 'Invalid code'], 422);
        }

        $codes = $this->totp->generateRecoveryCodes();
        $user->forceFill([
            'totp_enabled_at'     => now(),
            'totp_recovery_codes' => $codes,
        ])->save();

        return response()->json(['data' => [
            'enabled'        => true,
            'recovery_codes' => $codes,
        ]]);
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|size:6']);
        $user = $request->user();
        $ok = $this->totp->verifyCode($user->totp_secret ?? '', $data['code']);
        return response()->json(['valid' => $ok], $ok ? 200 : 422);
    }

    public function recovery(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|max:20']);
        $user = $request->user();
        $codes = $user->totp_recovery_codes ?? [];
        $normalized = array_map('strtoupper', $codes);
        $index = array_search(strtoupper($data['code']), $normalized, true);

        if ($index === false) {
            return response()->json(['message' => 'Invalid recovery code'], 422);
        }

        unset($codes[$index]);
        $user->forceFill(['totp_recovery_codes' => array_values($codes)])->save();

        return response()->json(['valid' => true, 'remaining' => count($codes)]);
    }

    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $data = $request->validate(['password' => 'required|string']);
        $user = $request->user();

        if (! empty($user->password) && ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Password required'], 422);
        }

        $codes = $this->totp->generateRecoveryCodes();
        $user->forceFill(['totp_recovery_codes' => $codes])->save();

        return response()->json(['data' => ['recovery_codes' => $codes]]);
    }

    public function disable(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|size:6']);
        $user = $request->user();

        if (! $this->totp->verifyCode($user->totp_secret ?? '', $data['code'])) {
            return response()->json(['message' => 'Invalid code'], 422);
        }

        $user->forceFill([
            'totp_secret'         => null,
            'totp_enabled_at'     => null,
            'totp_recovery_codes' => null,
        ])->save();

        return response()->json(['data' => ['enabled' => false]]);
    }
}

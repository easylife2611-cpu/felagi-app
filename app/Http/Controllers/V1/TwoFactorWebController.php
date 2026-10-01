<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Two-Factor Authentication Web UI — L302
 */
class TwoFactorWebController extends Controller
{
    public function __construct(private TwoFactorService $totp) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $status = [
            'enabled'      => $this->totp->isEnabled($user),
            'enabled_at'   => $user->totp_enabled_at,
            'codes_left'   => is_array($user->totp_recovery_codes)
                ? count($user->totp_recovery_codes)
                : 0,
        ];
        return view('profile.2fa', ['status' => $status]);
    }
}

<?php

namespace App\Services\Auth;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Email OTP — Authentication via email with 6-digit code
 * Supports Amharic (am) and English (en) locales.
 */
class EmailOtpService
{
    public function send(string $email, Request $request): EmailOtp
    {
        // Invalidate previous unused OTPs for this email
        EmailOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $otp = EmailOtp::create([
            'email'      => $email,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(EmailOtp::TTL_MINUTES),
            'ip_address' => $request->ip(),
        ]);

        // Dispatch mail (locale-aware)
        $locale = $request->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'am') ? 'am' : 'en';

        \Mail::to($email)->send(new \App\Mail\OtpMail($code, $locale));

        return $otp;
    }

    public function verify(string $email, string $code): ?User
    {
        $otp = EmailOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (! $otp || ! $otp->verify($code)) {
            return null;
        }

        return User::firstOrCreate(
            ['email' => $email],
            [
                'name'   => explode('@', $email)[0],
                'status' => User::STATUS_ACTIVE,
            ]
        );
    }
}

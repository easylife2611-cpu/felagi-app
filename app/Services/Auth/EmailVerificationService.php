<?php

namespace App\Services\Auth;

use App\Mail\VerifyEmailMail;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Email Verification Service — L304
 */
class EmailVerificationService
{
    public function send(User $user, string $locale = 'en'): EmailVerificationToken
    {
        EmailVerificationToken::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $plaintext = bin2hex(random_bytes(32));

        $token = EmailVerificationToken::create([
            'user_id'    => $user->id,
            'token_hash' => EmailVerificationToken::hashToken($plaintext),
            'expires_at' => now()->addHours(EmailVerificationToken::TTL_HOURS),
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user, $plaintext, $locale));

        return $token;
    }

    public function verify(string $plaintext): ?User
    {
        $hash = EmailVerificationToken::hashToken($plaintext);

        $token = EmailVerificationToken::where('token_hash', $hash)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $token) {
            return null;
        }

        $user = $token->user;
        if (! $user) {
            return null;
        }

        $token->consume();
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user->fresh();
    }

    public function isVerified(User $user): bool
    {
        return $user->email_verified_at !== null;
    }
}

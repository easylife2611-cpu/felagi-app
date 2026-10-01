<?php

namespace App\Services\Auth;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Password Reset Service — L304
 */
class PasswordResetService
{
    public const TTL_MINUTES = 60;
    public const TABLE = 'password_reset_tokens';

    public function send(User $user, string $locale = 'en'): string
    {
        $plaintext = Str::random(64);

        DB::table(self::TABLE)->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => Hash::make($plaintext),
                'created_at' => now(),
            ]
        );

        Mail::to($user->email)->send(new PasswordResetMail($user, $plaintext, $locale));

        return $plaintext;
    }

    public function verify(string $email, string $token): ?User
    {
        $row = DB::table(self::TABLE)->where('email', $email)->first();

        if (! $row) {
            return null;
        }

        if (now()->diffInMinutes($row->created_at) > self::TTL_MINUTES) {
            DB::table(self::TABLE)->where('email', $email)->delete();
            return null;
        }

        if (! Hash::check($token, $row->token)) {
            return null;
        }

        return User::where('email', $email)->first();
    }

    public function reset(string $email, string $token, string $newPassword): ?User
    {
        $user = $this->verify($email, $token);

        if (! $user) {
            return null;
        }

        $user->forceFill(['password' => Hash::make($newPassword)])->save();

        DB::table(self::TABLE)->where('email', $email)->delete();

        return $user;
    }
}

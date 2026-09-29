<?php

namespace App\Services\Admin;

use App\Exceptions\InvalidTotpCodeException;
use App\Exceptions\TwoFactorRequiredException;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

/**
 * Wraps pragmarx/google2fa for TOTP (RFC 6238).
 *
 * - Secret generation
 * - Code verification (with replay protection)
 * - Recovery codes (one-time, hashed)
 * - OTPAuth URL for QR provisioning
 *
 * Window: 1 step before/after (±30 seconds tolerance).
 * Replay: A code cannot be reused within its 30-second window.
 */
class TotpService
{
    /** Window in steps (1 = ±30 seconds). */
    public const WINDOW = 1;

    /** Recovery code count. */
    public const RECOVERY_CODE_COUNT = 8;

    /** Recovery code length (characters). */
    public const RECOVERY_CODE_LENGTH = 10;

    /** Replay-protection cache TTL (seconds) — 1 step. */
    public const REPLAY_TTL = 60;

    public function __construct(
        private readonly Google2FA $google2fa = new Google2FA(),
    ) {}

    /**
     * Generate a new TOTP secret.
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Build the OTPAuth URL for QR provisioning.
     */
    public function getOtpAuthUrl(string $secret, User $user): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name', 'Felagi'),
            $user->telegram_subject,
            $secret,
        );
    }

    /**
     * Verify a submitted TOTP code.
     *
     * @throws InvalidTotpCodeException on invalid/replay
     */
    public function verify(string $secret, string $code, User $user): bool
    {
        if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
            throw new InvalidTotpCodeException('invalid');
        }

        // Replay protection: same code cannot be reused
        $cacheKey = "totp:used:{$user->id}:{$code}";
        if (Cache::has($cacheKey)) {
            throw new InvalidTotpCodeException('replay');
        }

        $valid = $this->google2fa->verifyKey($secret, $code, self::WINDOW);

        if (!$valid) {
            throw new InvalidTotpCodeException('invalid');
        }

        // Mark this code as used for 1 step
        Cache::put($cacheKey, true, self::REPLAY_TTL);

        return true;
    }

    /**
     * Generate N recovery codes (plaintext — return once, store hashed).
     *
     * @return array{plain: array<string>, hashed: array<string>}
     */
    public function generateRecoveryCodes(int $count = self::RECOVERY_CODE_COUNT): array
    {
        $plain = [];
        $hashed = [];

        for ($i = 0; $i < $count; $i++) {
            $code = $this->randomRecoveryCode();
            $plain[] = $code;
            $hashed[] = hash('sha256', $code);
        }

        return ['plain' => $plain, 'hashed' => $hashed];
    }

    /**
     * Consume a recovery code.
     *
     * @param  array<string>  $hashedCodes  Current hashed codes on user
     * @return array{matched: bool, remaining: array<string>}
     */
    public function consumeRecoveryCode(string $submitted, array $hashedCodes): array
    {
        $submittedHash = hash('sha256', trim($submitted));

        $index = array_search($submittedHash, $hashedCodes, true);

        if ($index === false) {
            return ['matched' => false, 'remaining' => $hashedCodes];
        }

        // Remove used code
        unset($hashedCodes[$index]);

        return ['matched' => true, 'remaining' => array_values($hashedCodes)];
    }

    /**
     * Check if user can use 2FA for a given setting.
     *
     * @throws TwoFactorRequiredException
     */
    public function requireFor(User $user, Setting $setting): void
    {
        if (!$setting->requiresSecondFactor()) {
            return;
        }

        if (!$user->totp_enabled_at || empty($user->totp_secret)) {
            throw new TwoFactorRequiredException($setting->key);
        }
    }

    /**
     * Random alphanumeric recovery code (uppercase + digits).
     */
    protected function randomRecoveryCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O/1/I/L
        $code = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < self::RECOVERY_CODE_LENGTH; $i++) {
            $code .= $chars[random_int(0, $max)];
        }

        return $code;
    }
}

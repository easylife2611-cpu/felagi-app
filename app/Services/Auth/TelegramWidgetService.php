<?php

namespace App\Services\Auth;

use App\Exceptions\OidcExchangeException;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * TelegramWidgetService — Telegram Login Widget verification.
 *
 * Per Telegram docs (core.telegram.org/widgets/login):
 *   Widget sends: id, first_name, last_name, username, photo_url,
 *                 auth_date, hash
 *   Verification:
 *     1. Build data_check_string = sorted lines "key=value" joined by \n
 *        (excluding hash field)
 *     2. secret_key = SHA256(bot_token)
 *     3. expected_hash = HMAC_SHA256(data_check_string, secret_key)
 *     4. hash_equals(received_hash, expected_hash)
 *     5. Check auth_date freshness (≤ WIDGET_MAX_AGE_SECONDS)
 *
 * User upsert: find by telegram_subject (numeric telegram id), or create new.
 */
class TelegramWidgetService
{
    /** Max age of auth_date (seconds). */
    public const WIDGET_MAX_AGE_SECONDS = 300;

    /**
     * Verify widget payload and return verified data.
     *
     * @param  array  $payload  Raw query payload from Telegram
     * @return array{id: int, first_name: ?string, last_name: ?string,
     *               username: ?string, photo_url: ?string, auth_date: int}
     * @throws OidcExchangeException
     */
    public function verify(array $payload): array
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            Log::error('Telegram widget verification: bot_token missing');
            throw new OidcExchangeException(
                OidcExchangeException::REASON_CODE_EXCHANGE_FAILED,
                'Bot token not configured.'
            );
        }

        if (empty($payload['hash']) || empty($payload['id'])) {
            throw new OidcExchangeException(
                OidcExchangeException::REASON_INVALID_TOKEN,
                'Missing hash or id.'
            );
        }

        $receivedHash = (string) $payload['hash'];
        $dataCheckArray = [];

        // Telegram widget signature includes ONLY: id, first_name, last_name,
        // username, photo_url, auth_date. Exclude `hash` (signature itself)
        // and `state` (our addition, not signed by Telegram).
        $signedFields = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date'];

        foreach ($signedFields as $key) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }
            $value = $payload[$key];
            if ($value === null || $value === '') {
                continue;
            }
            $dataCheckArray[$key] = $value;
        }

        ksort($dataCheckArray);

        $dataCheckLines = [];
        foreach ($dataCheckArray as $k => $v) {
            $dataCheckLines[] = $k . '=' . $v;
        }
        $dataCheckString = implode("\n", $dataCheckLines);

        $secretKey = hash('sha256', $botToken, true);
        $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($expectedHash, $receivedHash)) {
            Log::warning('Telegram widget: hash mismatch');
            throw new OidcExchangeException(
                OidcExchangeException::REASON_INVALID_TOKEN,
                'Widget hash verification failed.'
            );
        }

        $authDate = (int) ($payload['auth_date'] ?? 0);
        $age = time() - $authDate;
        if ($age > self::WIDGET_MAX_AGE_SECONDS || $age < -60) {
            Log::warning('Telegram widget: auth_date out of range', ['age' => $age]);
            throw new OidcExchangeException(
                OidcExchangeException::REASON_INVALID_TOKEN,
                'Widget auth_date expired.'
            );
        }

        return [
            'id'         => (int) $payload['id'],
            'first_name' => $payload['first_name'] ?? null,
            'last_name'  => $payload['last_name'] ?? null,
            'username'   => $payload['username'] ?? null,
            'photo_url'  => $payload['photo_url'] ?? null,
            'auth_date'  => $authDate,
        ];
    }

    /**
     * Upsert user by telegram_subject and mark reauth.
     *
     * @return array{user: User, created: bool}
     */
    public function upsertUser(array $verified): array
    {
        $subject = (string) $verified['id'];

        $user = User::where('telegram_subject', $subject)->first();
        $created = false;

        $fullName = trim(($verified['first_name'] ?? '') . ' ' . ($verified['last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = $verified['username'] ?? 'Telegram User';
        }

        if (!$user) {
            $user = User::create([
                'telegram_subject' => $subject,
                'full_name'        => $fullName,
                'profile_photo_url'=> $verified['photo_url'] ?? null,
                'status'           => 'ACTIVE',
                'version'          => 1,
            ]);
            $created = true;
        } else {
            $user->full_name = $fullName;
            if (!empty($verified['photo_url'])) {
                $user->profile_photo_url = $verified['photo_url'];
            }
        }

        $user->last_login_at = now();
        $user->recently_authenticated_at = now();
        $user->version = $user->version + 1;
        $user->save();

        return ['user' => $user->fresh(), 'created' => $created];
    }
}

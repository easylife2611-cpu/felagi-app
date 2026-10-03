<?php

declare(strict_types=1);

namespace Tests\Feature\Telegram;

use App\Exceptions\OidcExchangeException;
use App\Services\Auth\TelegramWidgetService;
use Tests\TestCase;

/**
 * L343-C3 — Telegram Widget HMAC verification.
 *
 * Pure crypto test — no HTTP, no live credentials.
 * Bot token is a local test string, never leaves the process.
 */
final class TelegramWidgetServiceTest extends TestCase
{
    private const BOT_TOKEN = 'test_bot_token_not_real';

    private function signedPayload(array $fields): array
    {
        ksort($fields);

        $lines = [];
        foreach ($fields as $k => $v) {
            $lines[] = "{$k}={$v}";
        }
        $dataCheckString = implode("\n", $lines);

        $secretKey = hash('sha256', self::BOT_TOKEN, true);
        $fields['hash'] = hash_hmac('sha256', $dataCheckString, $secretKey);

        return $fields;
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.telegram.bot_token' => self::BOT_TOKEN]);
    }

    public function test_verifies_valid_payload(): void
    {
        $payload = $this->signedPayload([
            'id'         => 12345,
            'first_name' => 'Test',
            'auth_date'  => time(),
        ]);

        $result = app(TelegramWidgetService::class)->verify($payload);

        $this->assertSame(12345, $result['id']);
        $this->assertSame('Test', $result['first_name']);
    }

    public function test_rejects_tampered_payload(): void
    {
        $payload = $this->signedPayload([
            'id'         => 12345,
            'first_name' => 'Test',
            'auth_date'  => time(),
        ]);

        // Tamper after signing
        $payload['first_name'] = 'Attacker';

        $this->expectException(OidcExchangeException::class);
        app(TelegramWidgetService::class)->verify($payload);
    }

    public function test_rejects_missing_hash(): void
    {
        $this->expectException(OidcExchangeException::class);
        app(TelegramWidgetService::class)->verify([
            'id'         => 12345,
            'first_name' => 'Test',
            'auth_date'  => time(),
        ]);
    }

    public function test_rejects_missing_bot_token(): void
    {
        config(['services.telegram.bot_token' => null]);

        $payload = $this->signedPayload([
            'id'         => 12345,
            'first_name' => 'Test',
            'auth_date'  => time(),
        ]);

        $this->expectException(OidcExchangeException::class);
        app(TelegramWidgetService::class)->verify($payload);
    }

    public function test_rejects_stale_auth_date(): void
    {
        $payload = $this->signedPayload([
            'id'         => 12345,
            'first_name' => 'Test',
            'auth_date'  => time() - TelegramWidgetService::WIDGET_MAX_AGE_SECONDS - 60,
        ]);

        $this->expectException(OidcExchangeException::class);
        app(TelegramWidgetService::class)->verify($payload);
    }
}

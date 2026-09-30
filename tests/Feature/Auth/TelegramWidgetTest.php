<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\Auth\TelegramWidgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramWidgetTest extends TestCase
{
    use RefreshDatabase;

    private string $botToken = '1234567890:TESTTOKENforWidgetFlow1234567890ABC';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.telegram.bot_token' => $this->botToken]);
    }

    private function buildSignedPayload(array $overrides = []): array
    {
        $payload = array_merge([
            'id'         => '8629327448',
            'first_name' => 'Test',
            'last_name'  => 'User',
            'username'   => 'testuser',
            'photo_url'  => 'https://example.com/p.jpg',
            'auth_date'  => (string) time(),
        ], $overrides);

        ksort($payload);
        $lines = [];
        foreach ($payload as $k => $v) {
            if ($v !== '' && $v !== null) $lines[] = $k . '=' . $v;
        }
        $dataCheckString = implode("\n", $lines);
        $secretKey = hash('sha256', $this->botToken, true);
        $payload['hash'] = hash_hmac('sha256', $dataCheckString, $secretKey);

        return $payload;
    }

    public function test_verify_accepts_valid_signature(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();
        $verified = $service->verify($payload);

        $this->assertSame(8629327448, $verified['id']);
        $this->assertSame('Test', $verified['first_name']);
        $this->assertSame('testuser', $verified['username']);
    }

    public function test_verify_rejects_tampered_hash(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();
        $payload['hash'] = str_repeat('0', 64);

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        $service->verify($payload);
    }

    public function test_verify_rejects_tampered_first_name(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();
        $payload['first_name'] = 'Hacker';

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        $service->verify($payload);
    }

    public function test_verify_rejects_expired_auth_date(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload(['auth_date' => (string) (time() - 3600)]);

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        $service->verify($payload);
    }

    public function test_verify_rejects_missing_bot_token(): void
    {
        config(['services.telegram.bot_token' => null]);
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        $service->verify($payload);
    }

    public function test_upsert_user_creates_new(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();
        $verified = $service->verify($payload);
        $result = $service->upsertUser($verified);

        $this->assertTrue($result['created']);
        $this->assertSame('8629327448', $result['user']->telegram_subject);
        $this->assertSame('Test User', $result['user']->full_name);
        $this->assertSame('ACTIVE', $result['user']->status);
    }

    public function test_upsert_user_updates_existing(): void
    {
        $service = app(TelegramWidgetService::class);
        $payload = $this->buildSignedPayload();
        $verified = $service->verify($payload);

        $r1 = $service->upsertUser($verified);
        $this->assertTrue($r1['created']);

        $r2 = $service->upsertUser($verified);
        $this->assertFalse($r2['created']);
        $this->assertSame($r1['user']->id, $r2['user']->id);
        $this->assertSame(1, User::where('telegram_subject', '8629327448')->count());
    }

    public function test_widget_start_returns_config(): void
    {
        $response = $this->postJson('/api/v1/auth/telegram/widget/start', [
            'return_uri' => 'https://zagcreativity.com/',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['bot_username', 'callback_url', 'attempt_id', 'expires_at'],
            ]);

        $callbackUrl = $response->json('data.callback_url');
        $this->assertStringContainsString('/api/v1/auth/telegram/widget/callback', $callbackUrl);
        $this->assertStringContainsString('state=', $callbackUrl);
    }

    public function test_widget_start_validates_return_uri(): void
    {
        $this->postJson('/api/v1/auth/telegram/widget/start', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('return_uri');
    }

    public function test_widget_callback_full_flow(): void
    {
        $startResp = $this->postJson('/api/v1/auth/telegram/widget/start', [
            'return_uri' => 'https://zagcreativity.com/',
        ]);
        $this->assertTrue($startResp->json('success'));

        $callbackUrl = $startResp->json('data.callback_url');
        parse_str(parse_url($callbackUrl, PHP_URL_QUERY), $qs);

        $payload = $this->buildSignedPayload();
        $payload['state'] = $qs['state'];

        $response = $this->getJson('/api/v1/auth/telegram/widget/callback?' . http_build_query($payload));

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['handoff_code', 'redirect_url', 'user']]);

        $this->assertDatabaseHas('users', ['telegram_subject' => '8629327448']);
    }

    public function test_widget_callback_rejects_invalid_hash(): void
    {
        $startResp = $this->postJson('/api/v1/auth/telegram/widget/start', [
            'return_uri' => 'https://zagcreativity.com/',
        ]);
        $callbackUrl = $startResp->json('data.callback_url');
        parse_str(parse_url($callbackUrl, PHP_URL_QUERY), $qs);

        $payload = $this->buildSignedPayload();
        $payload['state'] = $qs['state'];
        $payload['hash'] = str_repeat('f', 64);

        $this->getJson('/api/v1/auth/telegram/widget/callback?' . http_build_query($payload))
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'WIDGET_VERIFICATION_FAILED');
    }

    public function test_widget_callback_rejects_missing_state(): void
    {
        $payload = $this->buildSignedPayload();
        $this->getJson('/api/v1/auth/telegram/widget/callback?' . http_build_query($payload))
            ->assertStatus(400)
            ->assertJsonPath('error.code', 'INVALID_CALLBACK');
    }
}

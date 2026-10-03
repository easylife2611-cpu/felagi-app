<?php

declare(strict_types=1);

namespace Tests\Feature\Telegram;

use App\Services\Auth\TelegramOidcService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * L343-C3 — Telegram OIDC code exchange.
 *
 * Uses Http::fake() to simulate the token endpoint.
 * No live credentials, no network — proves the flow is testable
 * without external infrastructure.
 */
final class TelegramOidcExchangeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.telegram.oidc.token_url'      => 'https://oauth.telegram.org/token',
            'services.telegram.client_id'           => 'test_client',
            'services.telegram.client_secret'       => 'test_secret',
            'services.telegram.redirect_uri'        => 'https://localhost/callback',
        ]);
    }

    public function test_exchange_code_posts_expected_payload(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response([
                'id_token'     => 'header.payload.signature',
                'access_token' => 'fake_access_token',
            ], 200),
        ]);

        $result = app(TelegramOidcService::class)->exchangeCode('auth_code_123', 'verifier_abc');

        $this->assertSame('header.payload.signature', $result['id_token']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://oauth.telegram.org/token'
                && $request['grant_type']    === 'authorization_code'
                && $request['code']          === 'auth_code_123'
                && $request['code_verifier'] === 'verifier_abc'
                && $request['client_id']     === 'test_client';
        });
    }

    public function test_exchange_code_throws_on_http_error(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        app(TelegramOidcService::class)->exchangeCode('bad_code', 'verifier');
    }

    public function test_exchange_code_throws_when_id_token_missing(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response([
                'access_token' => 'fake_access_token',
            ], 200),
        ]);

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        app(TelegramOidcService::class)->exchangeCode('auth_code', 'verifier');
    }

    public function test_exchange_code_throws_when_endpoint_unreachable(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('connection refused');
            },
        ]);

        $this->expectException(\App\Exceptions\OidcExchangeException::class);
        app(TelegramOidcService::class)->exchangeCode('auth_code', 'verifier');
    }
}

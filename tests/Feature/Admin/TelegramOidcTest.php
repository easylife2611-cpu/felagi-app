<?php

namespace Tests\Feature\Admin;

use App\Exceptions\OidcExchangeException;
use App\Models\AuthAttempt;
use App\Models\User;
use App\Services\Auth\AuthAttemptService;
use App\Services\Auth\TelegramOidcService;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramOidcTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();  // fresh JWKS cache
    }

    // ─── Helpers ───

    /**
     * Build a self-signed RSA key pair (test only, no live Telegram).
     */
    private function generateRsaKey(): array
    {
        $resource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($resource, $privateKey);
        $details = openssl_pkey_get_details($resource);

        return [
            'private'  => $privateKey,
            'public'   => $details['key'],
            'n'        => rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '='),
            'e'        => rtrim(strtr(base64_encode($details['rsa']['e']), '+/', '-_'), '='),
            'kid'      => 'test_kid_' . uniqid(),
        ];
    }

    /**
     * Build JWKS document from RSA key.
     */
    private function jwksFromKey(array $key): array
    {
        return [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'kid' => $key['kid'],
                    'alg' => 'RS256',
                    'n'   => $key['n'],
                    'e'   => $key['e'],
                ],
            ],
        ];
    }

    /**
     * Create an ID token signed with the test private key.
     */
    private function makeIdToken(array $key, array $claims): string
    {
        return JWT::encode($claims, $key['private'], 'RS256', $key['kid']);
    }

    /**
     * Build an AuthAttempt with PKCE verifier.
     */
    private function makeAttempt(string $nonce = 'test_nonce'): array
    {
        $svc = app(AuthAttemptService::class);
        $created = $svc->create('https://zagcreativity.com/auth/callback');
        return [
            'attempt' => $created['attempt'],
            'state'   => $created['state'],
            'nonce'   => $created['nonce'],
            'verifier'=> $svc->getPkceVerifier($created['attempt']),
        ];
    }

    // ─── Exception tests ───

    /** T01: Exception carries reason */
    public function test_exception_carries_reason(): void
    {
        $e = new OidcExchangeException(OidcExchangeException::REASON_INVALID_TOKEN);
        $this->assertEquals('invalid_token', $e->reason);
        $this->assertEquals('OIDC_EXCHANGE_FAILED', $e->toArray()['code']);
        $this->assertEquals('invalid_token', $e->toArray()['reason']);
    }

    /** T02: All 11 reason constants exist */
    public function test_exception_has_11_reasons(): void
    {
        $reflection = new \ReflectionClass(OidcExchangeException::class);
        $constants = array_filter(
            $reflection->getConstants(),
            fn($k) => str_starts_with($k, 'REASON_'),
            ARRAY_FILTER_USE_KEY
        );
        $this->assertCount(11, $constants);
    }

    // ─── Config tests ───

    /** T03: Config loaded correctly */
    public function test_config_loaded(): void
    {
        $this->assertNotNull(config('services.telegram.client_id'));
        $this->assertNotNull(config('services.telegram.client_secret'));
        $this->assertNotNull(config('services.telegram.redirect_uri'));
        $this->assertStringContainsString('oauth.telegram.org', config('services.telegram.oidc.token_url'));
    }

    // ─── Service tests (mocked) ───

    /** T04: exchangeCode with mocked token endpoint */
    public function test_exchange_code_returns_id_token(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response([
                'id_token'     => 'mock_id_token',
                'access_token' => 'mock_access_token',
                'token_type'   => 'Bearer',
            ], 200),
        ]);

        $svc = app(TelegramOidcService::class);
        $result = $svc->exchangeCode('auth_code_x', 'pkce_verifier_x');

        $this->assertEquals('mock_id_token', $result['id_token']);
        $this->assertEquals('mock_access_token', $result['access_token']);
    }

    /** T05: exchangeCode with endpoint error → OidcExchangeException */
    public function test_exchange_code_on_http_error_throws(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $svc = app(TelegramOidcService::class);

        $this->expectException(OidcExchangeException::class);
        $svc->exchangeCode('bad_code', 'pkce_verifier');
    }

    /** T06: exchangeCode with no id_token → exception */
    public function test_exchange_code_without_id_token_throws(): void
    {
        Http::fake([
            'oauth.telegram.org/token' => Http::response(['access_token' => 'x'], 200),
        ]);

        $svc = app(TelegramOidcService::class);

        $this->expectException(OidcExchangeException::class);
        $svc->exchangeCode('code_x', 'verifier_x');
    }

    /** T07: validateIdToken with valid JWT → returns claims */
    public function test_validate_id_token_valid(): void
    {
        $key = $this->generateRsaKey();
        $jwks = $this->jwksFromKey($key);

        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($jwks, 200),
        ]);

        $attemptData = $this->makeAttempt();
        $attempt = $attemptData['attempt'];

        $claims = [
            'iss'   => 'https://oauth.telegram.org',
            'aud'   => config('services.telegram.client_id'),
            'sub'   => 'telegram_subject_12345',
            'exp'   => time() + 3600,
            'iat'   => time(),
            'nonce' => $attemptData['nonce'],
            'name'  => 'Test User',
        ];
        $token = $this->makeIdToken($key, $claims);

        $svc = app(TelegramOidcService::class);
        $result = $svc->validateIdToken($token, $attempt);

        $this->assertEquals('telegram_subject_12345', $result['sub']);
        $this->assertEquals('Test User', $result['name']);
    }

    /** T08: validateIdToken with wrong issuer → throws */
    public function test_validate_id_token_wrong_issuer(): void
    {
        $key = $this->generateRsaKey();
        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($this->jwksFromKey($key), 200),
        ]);

        $attemptData = $this->makeAttempt();
        $claims = [
            'iss'   => 'https://evil.example.com',
            'aud'   => config('services.telegram.client_id'),
            'sub'   => 'x',
            'exp'   => time() + 3600,
            'nonce' => $attemptData['nonce'],
        ];
        $token = $this->makeIdToken($key, $claims);

        $this->expectException(OidcExchangeException::class);
        app(TelegramOidcService::class)->validateIdToken($token, $attemptData['attempt']);
    }

    /** T09: validateIdToken with wrong audience → throws */
    public function test_validate_id_token_wrong_audience(): void
    {
        $key = $this->generateRsaKey();
        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($this->jwksFromKey($key), 200),
        ]);

        $attemptData = $this->makeAttempt();
        $claims = [
            'iss'   => 'https://oauth.telegram.org',
            'aud'   => 'wrong_client_id',
            'sub'   => 'x',
            'exp'   => time() + 3600,
            'nonce' => $attemptData['nonce'],
        ];
        $token = $this->makeIdToken($key, $claims);

        $this->expectException(OidcExchangeException::class);
        app(TelegramOidcService::class)->validateIdToken($token, $attemptData['attempt']);
    }

    /** T10: validateIdToken expired → throws */
    public function test_validate_id_token_expired(): void
    {
        $key = $this->generateRsaKey();
        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($this->jwksFromKey($key), 200),
        ]);

        $attemptData = $this->makeAttempt();
        $claims = [
            'iss'   => 'https://oauth.telegram.org',
            'aud'   => config('services.telegram.client_id'),
            'sub'   => 'x',
            'exp'   => time() - 3600,  // expired
            'nonce' => $attemptData['nonce'],
        ];
        $token = $this->makeIdToken($key, $claims);

        $this->expectException(OidcExchangeException::class);
        app(TelegramOidcService::class)->validateIdToken($token, $attemptData['attempt']);
    }

    /** T11: validateIdToken wrong nonce → throws */
    public function test_validate_id_token_wrong_nonce(): void
    {
        $key = $this->generateRsaKey();
        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($this->jwksFromKey($key), 200),
        ]);

        $attemptData = $this->makeAttempt();
        $claims = [
            'iss'   => 'https://oauth.telegram.org',
            'aud'   => config('services.telegram.client_id'),
            'sub'   => 'x',
            'exp'   => time() + 3600,
            'nonce' => 'wrong_nonce_xyz',
        ];
        $token = $this->makeIdToken($key, $claims);

        $this->expectException(OidcExchangeException::class);
        app(TelegramOidcService::class)->validateIdToken($token, $attemptData['attempt']);
    }

    /** T12: validateIdToken missing subject → throws */
    public function test_validate_id_token_missing_subject(): void
    {
        $key = $this->generateRsaKey();
        Http::fake([
            'oauth.telegram.org/.well-known/jwks.json' => Http::response($this->jwksFromKey($key), 200),
        ]);

        $attemptData = $this->makeAttempt();
        $claims = [
            'iss'   => 'https://oauth.telegram.org',
            'aud'   => config('services.telegram.client_id'),
            'exp'   => time() + 3600,
            'nonce' => $attemptData['nonce'],
            // no 'sub'
        ];
        $token = $this->makeIdToken($key, $claims);

        $this->expectException(OidcExchangeException::class);
        app(TelegramOidcService::class)->validateIdToken($token, $attemptData['attempt']);
    }

    // ─── User upsert tests ───

    /** T13: upsertUser creates new user */
    public function test_upsert_user_creates_new(): void
    {
        $svc = app(TelegramOidcService::class);
        $claims = [
            'sub'  => 'new_user_subject_999',
            'name' => 'New User',
        ];

        $user = $svc->upsertUser($claims);

        $this->assertEquals('new_user_subject_999', $user->telegram_subject);
        $this->assertEquals('New User', $user->full_name);
        $this->assertEquals('ACTIVE', $user->status);
    }

    /** T14: upsertUser updates existing user */
    public function test_upsert_user_updates_existing(): void
    {
        $existing = User::create([
            'telegram_subject' => 'existing_subject',
            'full_name'        => 'Old Name',
            'status'           => 'ACTIVE',
            'version'          => 1,
        ]);

        $svc = app(TelegramOidcService::class);
        $user = $svc->upsertUser([
            'sub'  => 'existing_subject',
            'name' => 'New Name',
        ]);

        $this->assertEquals($existing->id, $user->id);
        $this->assertEquals('New Name', $user->full_name);
        $this->assertEquals(2, $user->version);
    }

    /** T15: markReauth updates timestamp */
    public function test_mark_reauth(): void
    {
        $user = User::create([
            'telegram_subject' => 'mark_test',
            'full_name'        => 'X',
            'status'           => 'ACTIVE',
            'version'          => 1,
        ]);

        $this->assertNull($user->recently_authenticated_at);
        app(TelegramOidcService::class)->markReauth($user);
        $this->assertNotNull($user->fresh()->recently_authenticated_at);
    }

    // ─── HTTP endpoint tests ───

    /** T16: callback without state → 400 */
    public function test_callback_without_state(): void
    {
        $res = $this->getJson('/api/v1/auth/telegram/callback');
        $res->assertStatus(400)
            ->assertJsonPath('error.code', 'INVALID_CALLBACK');
    }

    /** T17: callback with error param → 400 OIDC_PROVIDER_ERROR */
    public function test_callback_with_error(): void
    {
        $res = $this->getJson('/api/v1/auth/telegram/callback?error=access_denied');
        $res->assertStatus(400)
            ->assertJsonPath('error.code', 'OIDC_PROVIDER_ERROR');
    }

    /** T18: exchange without handoff_code → 422 */
    public function test_exchange_without_handoff(): void
    {
        $res = $this->postJson('/api/v1/auth/telegram/exchange', []);
        $res->assertStatus(422);
    }

    /** T19: exchange with invalid handoff → 401 */
    public function test_exchange_with_invalid_handoff(): void
    {
        $res = $this->postJson('/api/v1/auth/telegram/exchange', [
            'handoff_code' => 'invalid_handoff_xyz',
        ]);
        $res->assertStatus(401)
            ->assertJsonPath('error.code', 'OIDC_EXCHANGE_FAILED');
    }

    /** T20: exchange with valid handoff → issues token */
    public function test_exchange_with_valid_handoff(): void
    {
        // Create a user with fresh reauth
        $user = User::create([
            'telegram_subject' => 'exchange_test_subject',
            'full_name'        => 'Exchange User',
            'status'           => 'ACTIVE',
            'version'          => 1,
            'recently_authenticated_at' => now(),
        ]);

        // Create an attempt + handoff
        $svc = app(AuthAttemptService::class);
        $created = $svc->create('https://zagcreativity.com/auth/callback');
        $handoff = $svc->generateHandoff($created['attempt']);

        $res = $this->postJson('/api/v1/auth/telegram/exchange', [
            'handoff_code' => $handoff,
            'device_name'  => 'test_device',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'user' => ['id', 'full_name']],
            ]);

        // Handoff is single-use — replay must fail
        $replay = $this->postJson('/api/v1/auth/telegram/exchange', [
            'handoff_code' => $handoff,
        ]);
        $replay->assertStatus(401);
    }
}

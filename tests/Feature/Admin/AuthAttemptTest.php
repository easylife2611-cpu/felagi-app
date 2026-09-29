<?php

namespace Tests\Feature\Admin;

use App\Models\AuthAttempt;
use App\Services\Auth\AuthAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAttemptTest extends TestCase
{
    use RefreshDatabase;

    private function svc(): AuthAttemptService
    {
        return app(AuthAttemptService::class);
    }

    // ─── Service-level tests ───

    /** T01: create() returns state + nonce + PKCE challenge */
    public function test_create_returns_all_fields(): void
    {
        $r = $this->svc()->create('https://zagcreativity.com/auth/callback');

        $this->assertEquals(64, strlen($r['state']));
        $this->assertEquals(64, strlen($r['nonce']));
        $this->assertEquals(43, strlen($r['code_challenge']));  // S256 base64url
        $this->assertEquals('S256', $r['code_challenge_method']);
        $this->assertInstanceOf(AuthAttempt::class, $r['attempt']);
        $this->assertTrue($r['attempt']->isActive());
    }

    /** T02: PKCE verifier is 64 chars from RFC 7636 alphabet */
    public function test_pkce_verifier_alphabet(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $verifier = $this->svc()->getPkceVerifier($r['attempt']);

        $this->assertEquals(64, strlen($verifier));
        $this->assertMatchesRegularExpression(
            '/^[A-Za-z0-9\-._~]{64}$/',
            $verifier
        );
    }

    /** T03: PKCE challenge is SHA256(verifier) base64url */
    public function test_pkce_challenge_is_sha256_of_verifier(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $verifier = $this->svc()->getPkceVerifier($r['attempt']);

        $expected = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $this->assertEquals($expected, $r['code_challenge']);
    }

    /** T04: findByState returns active attempt */
    public function test_find_by_state(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $found = $this->svc()->findByState($r['state']);

        $this->assertNotNull($found);
        $this->assertEquals($r['attempt']->id, $found->id);
    }

    /** T05: findByState returns null for wrong state */
    public function test_find_by_state_wrong(): void
    {
        $this->svc()->create('https://example.com/cb');
        $found = $this->svc()->findByState('wrong-state');

        $this->assertNull($found);
    }

    /** T06: validateNonce correct */
    public function test_validate_nonce_correct(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $this->assertTrue($this->svc()->validateNonce($r['attempt'], $r['nonce']));
    }

    /** T07: validateNonce wrong */
    public function test_validate_nonce_wrong(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $this->assertFalse($this->svc()->validateNonce($r['attempt'], 'wrong'));
    }

    /** T08: handoff code generation + consume (single-use) */
    public function test_handoff_generation_and_consume(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $handoff = $this->svc()->generateHandoff($r['attempt']);

        // WP-27b: HMAC-signed format = <base64url_payload>.<hex_signature>
        $this->assertNotNull($handoff);
        $this->assertStringContainsString('.', $handoff);

        // First consume succeeds — returns array{attempt, user}
        $result = $this->svc()->consumeByHandoff($handoff);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('attempt', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertNotNull($result['attempt']->consumed_at);

        // Second consume fails (single-use)
        $result2 = $this->svc()->consumeByHandoff($handoff);
        $this->assertNull($result2);
    }

    /** T09: expires_at is 15 min in future */
    public function test_expires_at_is_15_min(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $delta = abs($r["attempt"]->expires_at->diffInMinutes(now()));

        $this->assertEqualsWithDelta(15, $delta, 1);
    }

    /** T10: expired attempt is not found by findByState */
    public function test_expired_attempt_not_found(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $r['attempt']->expires_at = now()->subMinute();
        $r['attempt']->save();

        $found = $this->svc()->findByState($r['state']);
        $this->assertNull($found);
    }

    /** T11: cleanupExpired removes old attempts */
    public function test_cleanup_expired(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $r['attempt']->expires_at = now()->subHour();
        $r['attempt']->save();

        $deleted = $this->svc()->cleanupExpired();
        $this->assertGreaterThanOrEqual(1, $deleted);
        $this->assertDatabaseMissing('auth_attempts', ['id' => $r['attempt']->id]);
    }

    /** T12: PKCE verifier is encrypted at rest */
    public function test_pkce_verifier_encrypted_at_rest(): void
    {
        $r = $this->svc()->create('https://example.com/cb');
        $verifierPlain = $this->svc()->getPkceVerifier($r['attempt']);

        // Read raw value from DB (bypassing model cast)
        $raw = \DB::table('auth_attempts')
            ->where('id', $r['attempt']->id)
            ->value('pkce_verifier_encrypted');

        $this->assertNotEquals($verifierPlain, $raw);
        $this->assertStringContainsString('eyJ', $raw); // Laravel encrypted payload prefix
    }

    // ─── HTTP-level tests ───

    /** T13: POST /auth/telegram/start returns auth_url with PKCE */
    public function test_telegram_start_returns_pkce_url(): void
    {
        $res = $this->postJson('/api/v1/auth/telegram/start', [
            'return_uri' => 'https://zagcreativity.com/auth/callback',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.attempt_id', fn ($v) => is_int($v) || is_numeric($v));

        $authUrl = $res->json('data.auth_url');
        $this->assertStringContainsString('code_challenge=', $authUrl);
        $this->assertStringContainsString('code_challenge_method=S256', $authUrl);
        $this->assertStringContainsString('state=', $authUrl);
        $this->assertStringContainsString('nonce=', $authUrl);
    }

    /** T14: POST with invalid return_uri → 422 */
    public function test_telegram_start_invalid_uri(): void
    {
        $res = $this->postJson('/api/v1/auth/telegram/start', [
            'return_uri' => 'not-a-url',
        ]);

        $res->assertStatus(422);
    }
}

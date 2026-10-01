<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function totp(): TwoFactorService
    {
        return app(TwoFactorService::class);
    }

    private function code(string $secret): string
    {
        return (new Google2FA())->getCurrentOtp($secret);
    }

    // Service

    public function test_service_generates_secret(): void
    {
        $secret = $this->totp()->generateSecret();
        $this->assertEquals(32, strlen($secret));
    }

    public function test_service_verifies_valid_code(): void
    {
        $secret = $this->totp()->generateSecret();
        $code = $this->code($secret);
        $this->assertTrue($this->totp()->verifyCode($secret, $code));
    }

    public function test_service_rejects_invalid_code(): void
    {
        $secret = $this->totp()->generateSecret();
        $this->assertFalse($this->totp()->verifyCode($secret, '000000'));
    }

    public function test_service_generates_recovery_codes(): void
    {
        $codes = $this->totp()->generateRecoveryCodes(8);
        $this->assertCount(8, $codes);
        $this->assertEquals(8, count(array_unique($codes)));
    }

    // Status

    public function test_status_requires_auth(): void
    {
        $this->getJson('/api/v1/auth/2fa/status')->assertUnauthorized();
    }

    public function test_status_reports_disabled_by_default(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/2fa/status')
            ->assertOk()
            ->assertJsonPath('data.enabled', false)
            ->assertJsonPath('data.recovery_codes_remaining', 0);
    }

    // Enroll

    public function test_enroll_start_returns_secret_and_qr(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start')
            ->assertOk()
            ->assertJsonStructure(['data' => ['secret', 'otpauth_url', 'qr_svg']]);

        $this->assertNotEmpty($user->fresh()->totp_secret);
    }

    public function test_enroll_verify_activates_2fa(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/auth/2fa/enroll/start')->assertOk();
        $secret = $user->fresh()->totp_secret;
        $code = $this->code($secret);

        $res = $this->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => $code]);
        $res->assertOk()
            ->assertJsonPath('data.enabled', true)
            ->assertJsonStructure(['data' => ['recovery_codes']]);

        $this->assertNotNull($user->fresh()->totp_enabled_at);
    }

    public function test_enroll_verify_rejects_invalid_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/auth/2fa/enroll/start')->assertOk();

        $this->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => '000000'])
            ->assertStatus(422);
    }

    // Verify

    public function test_verify_accepts_valid_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/auth/2fa/enroll/start');
        $secret = $user->fresh()->totp_secret;
        $this->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => $this->code($secret)]);

        $this->postJson('/api/v1/auth/2fa/verify', ['code' => $this->code($secret)])
            ->assertOk()
            ->assertJsonPath('valid', true);
    }

    public function test_verify_rejects_invalid_code(): void
    {
        $user = User::factory()->create(['totp_secret' => 'JBSWY3DPEHPK3PXP']);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '000000'])
            ->assertStatus(422)
            ->assertJsonPath('valid', false);
    }

    // Recovery

    public function test_recovery_consumes_code(): void
    {
        $user = User::factory()->create([
            'totp_secret'         => 'JBSWY3DPEHPK3PXP',
            'totp_enabled_at'     => now(),
            'totp_recovery_codes' => ['ABCDEF1234', 'GHIJKL5678'],
        ]);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/auth/2fa/recovery', ['code' => 'ABCDEF1234'])
            ->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('remaining', 1);
    }

    public function test_recovery_rejects_invalid_code(): void
    {
        $user = User::factory()->create([
            'totp_recovery_codes' => ['ABCDEF1234'],
        ]);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/auth/2fa/recovery', ['code' => 'WRONG'])
            ->assertStatus(422);
    }

    // Disable

    public function test_disable_clears_2fa(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/auth/2fa/enroll/start');
        $secret = $user->fresh()->totp_secret;
        $this->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => $this->code($secret)]);

        $this->postJson('/api/v1/auth/2fa/disable', ['code' => $this->code($secret)])
            ->assertOk()
            ->assertJsonPath('data.enabled', false);

        $fresh = $user->fresh();
        $this->assertNull($fresh->totp_secret);
        $this->assertNull($fresh->totp_enabled_at);
    }

    // Web UI

    public function test_web_page_renders_when_authenticated(): void
    {
        app()->setLocale('en');
        $user = User::factory()->create();
        $this->actingAs($user, 'web')
            ->get('/profile/2fa')
            ->assertOk()
            ->assertSee('Two-Factor Authentication', false);
    }

    public function test_web_page_requires_auth(): void
    {
        $this->get('/profile/2fa')->assertRedirect();
    }

    // Lang

    public function test_2fa_lang_keys_exist(): void
    {
        app()->setLocale('en');
        $this->assertNotEquals('2fa.title', __('2fa.title'));

        app()->setLocale('am');
        $this->assertNotEquals('2fa.title', __('2fa.title'));
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * WP-13c (backend) — 2FA enrollment API endpoints.
 */
class TwoFactorEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function totp(): TotpService
    {
        return app(TotpService::class);
    }

    private function code(string $secret): string
    {
        return (new Google2FA())->getCurrentOtp($secret);
    }

    // ─── Status ───

    public function test_status_requires_auth(): void
    {
        $this->getJson('/api/v1/auth/2fa/status')->assertStatus(401);
    }

    public function test_status_reports_disabled_by_default(): void
    {
        $user = $this->user();

        $res = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/2fa/status');

        $res->assertStatus(200)
            ->assertJsonPath('data.enabled', false)
            ->assertJsonPath('data.recovery_codes_remaining', 0);
    }

    // ─── Enroll start ───

    public function test_enroll_start_requires_auth(): void
    {
        $this->postJson('/api/v1/auth/2fa/enroll/start')->assertStatus(401);
    }

    public function test_enroll_start_returns_secret_and_otpauth_url(): void
    {
        $user = $this->user();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start');

        $res->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['secret', 'otpauth_url', 'account', 'issuer'],
            ]);

        $this->assertNotEmpty($res->json('data.secret'));
        $this->assertStringStartsWith('otpauth://totp/', $res->json('data.otpauth_url'));
    }

    public function test_enroll_start_persists_secret_but_does_not_enable(): void
    {
        $user = $this->user();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start')
            ->assertStatus(200);

        $user->refresh();
        $this->assertNotNull($user->totp_secret);
        $this->assertNull($user->totp_enabled_at);
    }

    // ─── Enroll verify ───

    public function test_enroll_verify_requires_valid_code(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start');

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => '000000']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'INVALID_2FA_CODE');
    }

    public function test_enroll_verify_activates_2fa(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start');

        $user->refresh();
        $code = $this->code($user->totp_secret);

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => $code]);

        $res->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['enabled_at', 'recovery_codes', 'recovery_codes_count'],
            ]);

        $user->refresh();
        $this->assertNotNull($user->totp_enabled_at);
        $this->assertCount(8, $user->totp_recovery_codes);
    }

    public function test_enroll_verify_returns_8_recovery_codes_once(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start');

        $user->refresh();
        $code = $this->code($user->totp_secret);

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => $code]);

        $this->assertCount(8, $res->json('data.recovery_codes'));
    }

    public function test_enroll_verify_fails_if_not_started(): void
    {
        $user = $this->user();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/verify', ['code' => '123456']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', '2FA_NOT_STARTED');
    }

    public function test_enroll_start_fails_if_already_enabled(): void
    {
        $user = $this->user();
        $user->totp_secret = $this->totp()->generateSecret();
        $user->totp_enabled_at = now();
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/enroll/start');

        $res->assertStatus(409)
            ->assertJsonPath('error.code', '2FA_ALREADY_ENABLED');
    }

    // ─── Verify (reauth) ───

    public function test_verify_requires_auth(): void
    {
        $this->postJson('/api/v1/auth/2fa/verify', ['code' => '123456'])
            ->assertStatus(401);
    }

    public function test_verify_accepts_valid_code_and_marks_reauth(): void
    {
        $user = $this->user();
        $user->totp_secret = $this->totp()->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = [];
        $user->save();

        $code = $this->code($user->totp_secret);

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/verify', ['code' => $code]);

        $res->assertStatus(200)
            ->assertJsonPath('data.verified', true);

        $user->refresh();
        $this->assertNotNull($user->recently_authenticated_at);
    }

    public function test_verify_rejects_invalid_code(): void
    {
        $user = $this->user();
        $user->totp_secret = $this->totp()->generateSecret();
        $user->totp_enabled_at = now();
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/verify', ['code' => '000000']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'INVALID_2FA_CODE');
    }

    public function test_verify_fails_if_not_enrolled(): void
    {
        $user = $this->user();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/verify', ['code' => '123456']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', '2FA_NOT_ENABLED');
    }

    // ─── Recovery ───

    public function test_recovery_consumes_code(): void
    {
        $svc = $this->totp();
        $user = $this->user();
        $codes = $svc->generateRecoveryCodes();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = $codes['hashed'];
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/recovery', ['code' => $codes['plain'][0]]);

        $res->assertStatus(200)
            ->assertJsonPath('data.verified', true)
            ->assertJsonPath('data.remaining_codes', 7);
    }

    public function test_recovery_rejects_unknown_code(): void
    {
        $svc = $this->totp();
        $user = $this->user();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = $svc->generateRecoveryCodes()['hashed'];
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/recovery', ['code' => 'WRONGCODE1']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'INVALID_RECOVERY_CODE');
    }

    public function test_recovery_rejects_used_code(): void
    {
        $svc = $this->totp();
        $user = $this->user();
        $codes = $svc->generateRecoveryCodes();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = $codes['hashed'];
        $user->save();

        // Use once
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/recovery', ['code' => $codes['plain'][0]])
            ->assertStatus(200);

        // Use again → 422
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/recovery', ['code' => $codes['plain'][0]])
            ->assertStatus(422);
    }

    // ─── Regenerate recovery codes ───

    public function test_regenerate_requires_valid_totp(): void
    {
        $user = $this->user();
        $user->totp_secret = $this->totp()->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = [];
        $user->save();

        $code = $this->code($user->totp_secret);

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/recovery-codes/regenerate', ['code' => $code]);

        $res->assertStatus(200)
            ->assertJsonStructure(['data' => ['recovery_codes', 'recovery_codes_count']]);

        $this->assertCount(8, $res->json('data.recovery_codes'));
    }

    // ─── Disable ───

    public function test_disable_with_valid_totp(): void
    {
        $svc = $this->totp();
        $user = $this->user();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = $svc->generateRecoveryCodes()['hashed'];
        $user->save();

        $code = $this->code($user->totp_secret);

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/disable', ['code' => $code]);

        $res->assertStatus(200);

        $user->refresh();
        $this->assertNull($user->totp_secret);
        $this->assertNull($user->totp_enabled_at);
        $this->assertNull($user->totp_recovery_codes);
    }

    public function test_disable_with_recovery_code(): void
    {
        $svc = $this->totp();
        $user = $this->user();
        $codes = $svc->generateRecoveryCodes();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = $codes['hashed'];
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/disable', ['code' => $codes['plain'][0]]);

        $res->assertStatus(200);

        $user->refresh();
        $this->assertNull($user->totp_enabled_at);
    }

    public function test_disable_rejects_bad_code(): void
    {
        $user = $this->user();
        $user->totp_secret = $this->totp()->generateSecret();
        $user->totp_enabled_at = now();
        $user->totp_recovery_codes = [];
        $user->save();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/disable', ['code' => 'WRONGCODE1']);

        $res->assertStatus(422);
    }

    public function test_disable_fails_if_not_enrolled(): void
    {
        $user = $this->user();

        $res = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/2fa/disable', ['code' => '123456']);

        $res->assertStatus(422)
            ->assertJsonPath('error.code', '2FA_NOT_ENABLED');
    }
}

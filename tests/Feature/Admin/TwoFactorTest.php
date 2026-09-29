<?php

namespace Tests\Feature\Admin;

use App\Exceptions\InvalidTotpCodeException;
use App\Exceptions\TwoFactorRequiredException;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\TotpService;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function mainAdmin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => 'MAIN_ADMIN']);
        return $u;
    }

    // ─── Secret generation ───

    /** T01: generateSecret() returns valid base32 */
    public function test_generate_secret_returns_base32(): void
    {
        $svc = app(TotpService::class);
        $secret = $svc->generateSecret();

        $this->assertEquals(32, strlen($secret));
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
    }

    /** T02: verify() accepts current code */
    public function test_verify_accepts_current_code(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $secret = $svc->generateSecret();

        $g2fa = new Google2FA();
        $code = $g2fa->getCurrentOtp($secret);

        $this->assertTrue($svc->verify($secret, $code, $user));
    }

    /** T03: verify() rejects invalid code */
    public function test_verify_rejects_invalid_code(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $secret = $svc->generateSecret();

        $this->expectException(InvalidTotpCodeException::class);
        $svc->verify($secret, '000000', $user);
    }

    /** T04: verify() rejects malformed code */
    public function test_verify_rejects_malformed(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $secret = $svc->generateSecret();

        $this->expectException(InvalidTotpCodeException::class);
        $svc->verify($secret, 'abc', $user);
    }

    /** T05: verify() detects replay within window */
    public function test_verify_detects_replay(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $secret = $svc->generateSecret();

        $g2fa = new Google2FA();
        $code = $g2fa->getCurrentOtp($secret);

        // First use → OK
        $this->assertTrue($svc->verify($secret, $code, $user));

        // Second use → replay
        $this->expectException(InvalidTotpCodeException::class);
        $svc->verify($secret, $code, $user);
    }

    // ─── Recovery codes ───

    /** T06: generateRecoveryCodes() returns N pairs */
    public function test_generate_recovery_codes(): void
    {
        $svc = app(TotpService::class);
        $codes = $svc->generateRecoveryCodes(5);

        $this->assertCount(5, $codes['plain']);
        $this->assertCount(5, $codes['hashed']);
        $this->assertEquals(64, strlen($codes['hashed'][0]));
    }

    /** T07: consumeRecoveryCode() matches + removes */
    public function test_consume_recovery_code(): void
    {
        $svc = app(TotpService::class);
        $codes = $svc->generateRecoveryCodes(3);
        $plain = $codes['plain'][1];
        $hashed = $codes['hashed'];

        $result = $svc->consumeRecoveryCode($plain, $hashed);

        $this->assertTrue($result['matched']);
        $this->assertCount(2, $result['remaining']);
    }

    /** T08: consumeRecoveryCode() rejects unknown code */
    public function test_consume_recovery_code_rejects_unknown(): void
    {
        $svc = app(TotpService::class);
        $codes = $svc->generateRecoveryCodes(3);

        $result = $svc->consumeRecoveryCode('WRONGCODE1', $codes['hashed']);

        $this->assertFalse($result['matched']);
        $this->assertCount(3, $result['remaining']);
    }

    // ─── Setting-level enforcement ───

    /** T09: requireFor() passes for HIGH (no 2FA needed) */
    public function test_require_for_passes_for_high(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $setting = Setting::find('feature.payments'); // HIGH

        $svc->requireFor($user, $setting);
        $this->assertTrue(true);
    }

    /** T10: requireFor() throws for CRITICAL when not enrolled */
    public function test_require_for_throws_for_critical_without_2fa(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $setting = Setting::find('system.safe_mode'); // CRITICAL

        $this->expectException(TwoFactorRequiredException::class);
        $svc->requireFor($user, $setting);
    }

    /** T11: requireFor() passes for CRITICAL when enrolled */
    public function test_require_for_passes_for_critical_with_2fa(): void
    {
        $svc = app(TotpService::class);
        $user = $this->mainAdmin();
        $user->totp_secret = $svc->generateSecret();
        $user->totp_enabled_at = now();
        $user->save();

        $setting = Setting::find('system.safe_mode'); // CRITICAL

        $svc->requireFor($user, $setting);
        $this->assertTrue(true);
    }
}

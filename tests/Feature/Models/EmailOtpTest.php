<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class EmailOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_constants_match(): void
    {
        $this->assertSame(5, EmailOtp::MAX_ATTEMPTS);
        $this->assertSame(10, EmailOtp::TTL_MINUTES);
    }

    public function test_expires_at_casts_to_carbon(): void
    {
        $o = EmailOtp::factory()->create();
        $this->assertInstanceOf(Carbon::class, $o->fresh()->expires_at);
    }

    public function test_attempts_casts_to_integer(): void
    {
        $o = EmailOtp::factory()->withAttempts(3)->create();
        $this->assertSame(3, $o->fresh()->attempts);
    }

    public function test_is_expired_false_when_future(): void
    {
        $o = EmailOtp::factory()->create();
        $this->assertFalse($o->fresh()->isExpired());
    }

    public function test_is_expired_true_when_past(): void
    {
        $o = EmailOtp::factory()->expired()->create();
        $this->assertTrue($o->fresh()->isExpired());
    }

    public function test_is_consumed_false_initially(): void
    {
        $o = EmailOtp::factory()->create();
        $this->assertFalse($o->fresh()->isConsumed());
    }

    public function test_is_consumed_true_when_set(): void
    {
        $o = EmailOtp::factory()->consumed()->create();
        $this->assertTrue($o->fresh()->isConsumed());
    }

    public function test_is_usable_true_for_fresh_otp(): void
    {
        $o = EmailOtp::factory()->create();
        $this->assertTrue($o->fresh()->isUsable());
    }

    public function test_is_usable_false_when_expired(): void
    {
        $o = EmailOtp::factory()->expired()->create();
        $this->assertFalse($o->fresh()->isUsable());
    }

    public function test_is_usable_false_when_consumed(): void
    {
        $o = EmailOtp::factory()->consumed()->create();
        $this->assertFalse($o->fresh()->isUsable());
    }

    public function test_is_usable_false_when_max_attempts_reached(): void
    {
        $o = EmailOtp::factory()->withAttempts(EmailOtp::MAX_ATTEMPTS)->create();
        $this->assertFalse($o->fresh()->isUsable());
    }

    public function test_verify_true_with_correct_code(): void
    {
        $o = EmailOtp::factory()->withCode('654321')->create();
        $this->assertTrue($o->fresh()->verify('654321'));
    }

    public function test_verify_consumes_on_success(): void
    {
        $o = EmailOtp::factory()->withCode('654321')->create();
        $o->fresh()->verify('654321');
        $this->assertNotNull($o->fresh()->consumed_at);
    }

    public function test_verify_false_with_wrong_code(): void
    {
        $o = EmailOtp::factory()->withCode('654321')->create();
        $this->assertFalse($o->fresh()->verify('000000'));
    }

    public function test_verify_increments_attempts_on_failure(): void
    {
        $o = EmailOtp::factory()->withCode('654321')->create();
        $o->fresh()->verify('000000');
        $this->assertSame(1, $o->fresh()->attempts);
    }

    public function test_verify_false_when_expired(): void
    {
        $o = EmailOtp::factory()->expired()->withCode('654321')->create();
        $this->assertFalse($o->fresh()->verify('654321'));
    }

    public function test_verify_false_when_consumed(): void
    {
        $o = EmailOtp::factory()->consumed()->withCode('654321')->create();
        $this->assertFalse($o->fresh()->verify('654321'));
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $o = new EmailOtp();
        foreach (['email', 'code_hash', 'attempts', 'expires_at', 'consumed_at', 'ip_address'] as $f) {
            $this->assertContains($f, $o->getFillable());
        }
    }

    public function test_factory_hashes_default_code(): void
    {
        $o = EmailOtp::factory()->create();
        $this->assertTrue(Hash::check('123456', $o->code_hash));
    }
}

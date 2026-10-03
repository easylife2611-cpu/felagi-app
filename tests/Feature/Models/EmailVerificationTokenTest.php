<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class EmailVerificationTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_ttl_hours_constant_is_24(): void
    {
        $this->assertSame(24, EmailVerificationToken::TTL_HOURS);
    }

    public function test_expires_at_casts_to_carbon(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $this->assertInstanceOf(Carbon::class, $t->fresh()->expires_at);
    }

    public function test_consumed_at_casts_to_carbon(): void
    {
        $t = EmailVerificationToken::factory()->consumed()->create();
        $this->assertInstanceOf(Carbon::class, $t->fresh()->consumed_at);
    }

    public function test_belongs_to_user(): void
    {
        $u = User::factory()->create();
        $t = EmailVerificationToken::factory()->create(['user_id' => $u->id]);
        $this->assertSame($u->id, $t->fresh()->user->id);
    }

    public function test_is_usable_true_for_fresh_token(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $this->assertTrue($t->fresh()->isUsable());
    }

    public function test_is_usable_false_when_expired(): void
    {
        $t = EmailVerificationToken::factory()->expired()->create();
        $this->assertFalse($t->fresh()->isUsable());
    }

    public function test_is_usable_false_when_consumed(): void
    {
        $t = EmailVerificationToken::factory()->consumed()->create();
        $this->assertFalse($t->fresh()->isUsable());
    }

    public function test_consume_sets_consumed_at(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $t->fresh()->consume();
        $this->assertNotNull($t->fresh()->consumed_at);
    }

    public function test_consume_makes_token_unusable(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $t->fresh()->consume();
        $this->assertFalse($t->fresh()->isUsable());
    }

    public function test_hash_token_is_deterministic(): void
    {
        $a = EmailVerificationToken::hashToken('plaintext');
        $b = EmailVerificationToken::hashToken('plaintext');
        $this->assertSame($a, $b);
    }

    public function test_hash_token_returns_sha256_hex(): void
    {
        $hash = EmailVerificationToken::hashToken('test');
        $expected = hash('sha256', 'test');
        $this->assertSame($expected, $hash);
        $this->assertSame(64, strlen($hash));
    }

    public function test_hash_token_differs_for_different_inputs(): void
    {
        $a = EmailVerificationToken::hashToken('a');
        $b = EmailVerificationToken::hashToken('b');
        $this->assertNotSame($a, $b);
    }

    public function test_factory_with_plaintext_stores_correct_hash(): void
    {
        $t = EmailVerificationToken::factory()
            ->withPlaintext('my-secret-token')
            ->create();
        $expected = EmailVerificationToken::hashToken('my-secret-token');
        $this->assertSame($expected, $t->fresh()->token_hash);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $t = new EmailVerificationToken();
        foreach (['user_id', 'token_hash', 'expires_at', 'consumed_at', 'ip_address'] as $f) {
            $this->assertContains($f, $t->getFillable());
        }
    }

    public function test_default_expires_at_is_24h_in_future(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $fresh = $t->fresh();
        // Should be within a minute of +24h from now
        $this->assertTrue($fresh->expires_at->greaterThan(now()->addHours(23)->addMinutes(59)));
        $this->assertTrue($fresh->expires_at->lessThanOrEqualTo(now()->addHours(24)->addMinute()));
    }

    public function test_consumed_at_null_by_default(): void
    {
        $t = EmailVerificationToken::factory()->create();
        $this->assertNull($t->fresh()->consumed_at);
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Need;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B25 / R-TEST-01 — Boost model unit tests.
 * Schema: 2026_09_29_000015. No HasFactory.
 */
class BoostTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeNeed(): Need
    {
        $requester = User::factory()->create();

        return Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);
    }

    private function makePackage(array $overrides = []): BoostPackage
    {
        return BoostPackage::create(array_merge([
            'duration_days' => 7,
            'price'         => '99.99',
            'currency'      => 'ETB',
            'active'        => true,
        ], $overrides));
    }

    private function makePayment(Need $need): Payment
    {
        return Payment::create([
            'payer_id'           => $need->requester_id,
            'need_id'            => $need->id,
            'purpose'            => 'BOOST',
            'provider'           => 'telebirr',
            'provider_reference' => 'TXN-' . Str::uuid(),
            'idempotency_key'    => (string) Str::uuid(),
            'amount'             => '99.99',
            'currency'           => 'ETB',
            'status'             => 'CONFIRMED',
        ]);
    }

    private function makeBoost(array $overrides = []): Boost
    {
        $need    = $this->makeNeed();
        $package = $this->makePackage();
        $payment = $this->makePayment($need);

        return Boost::create(array_merge([
            'need_id'        => $need->id,
            'requester_id'   => $need->requester_id,
            'package_id'     => $package->id,
            'payment_id'     => $payment->id,
            'price_snapshot' => '99.99',
            'currency'       => 'ETB',
            'duration_days'  => 7,
            'status'         => Boost::STATUS_PENDING,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $this->assertTrue(Str::isUuid($this->makeBoost()->id));
    }

    public function test_persists_core_attributes(): void
    {
        $b = $this->makeBoost([
            'price_snapshot' => '150.00',
            'duration_days'  => 30,
        ]);

        $this->assertDatabaseHas('boosts', [
            'id'             => $b->id,
            'price_snapshot' => 150.00,
            'currency'       => 'ETB',
            'duration_days'  => 30,
            'status'         => 'PENDING',
        ]);
    }

    public function test_belongs_to_need(): void
    {
        $b = $this->makeBoost();
        $this->assertInstanceOf(Need::class, $b->need);
        $this->assertSame($b->need_id, $b->need->id);
    }

    public function test_belongs_to_requester(): void
    {
        $b = $this->makeBoost();
        $this->assertInstanceOf(User::class, $b->requester);
        $this->assertSame($b->requester_id, $b->requester->id);
    }

    public function test_belongs_to_package(): void
    {
        $b = $this->makeBoost();
        $this->assertInstanceOf(BoostPackage::class, $b->package);
        $this->assertSame($b->package_id, $b->package->id);
    }

    public function test_belongs_to_payment(): void
    {
        $b = $this->makeBoost();
        $this->assertInstanceOf(Payment::class, $b->payment);
        $this->assertSame($b->payment_id, $b->payment->id);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PENDING', Boost::STATUS_PENDING);
        $this->assertSame('ACTIVE', Boost::STATUS_ACTIVE);
        $this->assertSame('EXPIRED', Boost::STATUS_EXPIRED);
        $this->assertSame('CANCELLED', Boost::STATUS_CANCELLED);
    }

    public function test_price_snapshot_casts_decimal_2(): void
    {
        $b = $this->makeBoost(['price_snapshot' => '50.5']);
        $this->assertSame('50.50', (string) $b->fresh()->price_snapshot);
    }

    public function test_duration_days_casts_integer(): void
    {
        $b = $this->makeBoost(['duration_days' => 14]);
        $this->assertSame(14, $b->fresh()->duration_days);
    }

    public function test_starts_at_casts_to_datetime(): void
    {
        $b = $this->makeBoost(['starts_at' => '2026-01-01 00:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $b->fresh()->starts_at);
    }

    public function test_expires_at_casts_to_datetime(): void
    {
        $b = $this->makeBoost(['expires_at' => '2026-01-08 00:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $b->fresh()->expires_at);
    }

    public function test_starts_at_nullable(): void
    {
        $b = $this->makeBoost(['starts_at' => null]);
        $this->assertNull($b->fresh()->starts_at);
    }

    public function test_scope_active_requires_status_and_window(): void
    {
        $this->makeBoost([
            'status'     => Boost::STATUS_ACTIVE,
            'starts_at'  => now()->subHour(),
            'expires_at' => now()->addHour(),
        ]);

        $this->makeBoost([
            'status'     => Boost::STATUS_PENDING,
            'starts_at'  => now()->subHour(),
            'expires_at' => now()->addHour(),
        ]);

        $this->makeBoost([
            'status'     => Boost::STATUS_ACTIVE,
            'starts_at'  => now()->subHours(3),
            'expires_at' => now()->subHour(),
        ]);

        $this->makeBoost([
            'status'     => Boost::STATUS_ACTIVE,
            'starts_at'  => now()->addHour(),
            'expires_at' => now()->addHours(3),
        ]);

        $this->assertSame(1, Boost::active()->count());
    }

    public function test_payment_id_is_unique_per_boost(): void
    {
        $b = $this->makeBoost();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Boost::create([
            'need_id'        => $b->need_id,
            'requester_id'   => $b->requester_id,
            'package_id'     => $b->package_id,
            'payment_id'     => $b->payment_id,
            'price_snapshot' => '99.99',
            'currency'       => 'ETB',
            'duration_days'  => 7,
            'status'         => Boost::STATUS_PENDING,
        ]);
    }
}

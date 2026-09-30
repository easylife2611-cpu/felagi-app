<?php

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B25 / R-TEST-01 — Payment model unit tests.
 * Aligned to schema 2026_09_29_000014. No HasFactory.
 */
class PaymentTest extends TestCase
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

    private function makePayment(array $overrides = []): Payment
    {
        $need = $this->makeNeed();

        return Payment::create(array_merge([
            'payer_id'           => $need->requester_id,
            'need_id'            => $need->id,
            'purpose'            => Payment::PURPOSE_BOOST,
            'provider'           => 'telebirr',
            'provider_reference' => 'TXN-' . Str::uuid(),
            'idempotency_key'    => (string) Str::uuid(),
            'amount'             => '199.99',
            'currency'           => 'ETB',
            'status'             => Payment::STATUS_PENDING,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $p = $this->makePayment();
        $this->assertNotNull($p->id);
        $this->assertTrue(Str::isUuid($p->id));
    }

    public function test_persists_core_attributes(): void
    {
        $p = $this->makePayment(['amount' => '250.50']);
        $this->assertDatabaseHas('payments', [
            'id'       => $p->id,
            'amount'   => 250.50,
            'currency' => 'ETB',
            'status'   => 'PENDING',
            'purpose'  => 'BOOST',
        ]);
    }

    public function test_belongs_to_payer(): void
    {
        $p = $this->makePayment();
        $this->assertInstanceOf(User::class, $p->payer);
        $this->assertSame($p->payer_id, $p->payer->id);
    }

    public function test_belongs_to_need(): void
    {
        $p = $this->makePayment();
        $this->assertInstanceOf(Need::class, $p->need);
        $this->assertSame($p->need_id, $p->need->id);
    }

    public function test_has_many_events(): void
    {
        $p = $this->makePayment();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $p->events
        );
        $this->assertCount(0, $p->events);
    }

    public function test_boost_is_null_initially(): void
    {
        $this->assertNull($this->makePayment()->boost);
    }

    public function test_amount_casts_to_decimal_2(): void
    {
        $p = $this->makePayment(['amount' => '250.5']);
        $this->assertSame('250.50', (string) $p->fresh()->amount);
    }

    public function test_confirmed_at_casts_to_datetime(): void
    {
        $p = $this->makePayment(['confirmed_at' => '2026-01-01 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $p->fresh()->confirmed_at);
    }

    public function test_failed_at_casts_to_datetime(): void
    {
        $p = $this->makePayment(['failed_at' => '2026-01-01 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $p->fresh()->failed_at);
    }

    public function test_is_confirmed_false_for_pending(): void
    {
        $p = $this->makePayment(['status' => Payment::STATUS_PENDING]);
        $this->assertFalse($p->isConfirmed());
    }

    public function test_is_confirmed_true_for_confirmed(): void
    {
        $p = $this->makePayment(['status' => Payment::STATUS_CONFIRMED]);
        $this->assertTrue($p->isConfirmed());
    }

    public function test_scope_confirmed_filters(): void
    {
        $need = $this->makeNeed();
        $base = (string) Str::uuid();

        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'p1', 'provider_reference' => 'R1',
            'idempotency_key' => $base . '1', 'amount' => '10.00',
            'currency' => 'ETB', 'status' => 'CONFIRMED',
        ]);
        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'p2', 'provider_reference' => 'R2',
            'idempotency_key' => $base . '2', 'amount' => '20.00',
            'currency' => 'ETB', 'status' => 'CONFIRMED',
        ]);
        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'p3', 'provider_reference' => 'R3',
            'idempotency_key' => $base . '3', 'amount' => '30.00',
            'currency' => 'ETB', 'status' => 'PENDING',
        ]);
        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'p4', 'provider_reference' => 'R4',
            'idempotency_key' => $base . '4', 'amount' => '40.00',
            'currency' => 'ETB', 'status' => 'FAILED',
        ]);

        $this->assertSame(2, Payment::confirmed()->count());
    }

    public function test_purpose_constants(): void
    {
        $this->assertSame('BOOST', Payment::PURPOSE_BOOST);
        $this->assertSame('OFFER_UNLOCK', Payment::PURPOSE_OFFER_UNLOCK);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PENDING', Payment::STATUS_PENDING);
        $this->assertSame('CONFIRMED', Payment::STATUS_CONFIRMED);
        $this->assertSame('FAILED', Payment::STATUS_FAILED);
        $this->assertSame('CANCELLED', Payment::STATUS_CANCELLED);
        $this->assertSame('REVIEW_REQUIRED', Payment::STATUS_REVIEW_REQUIRED);
    }

    public function test_unique_payer_id_and_idempotency_key(): void
    {
        $need = $this->makeNeed();
        $key  = (string) Str::uuid();

        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'tx1', 'provider_reference' => 'R1',
            'idempotency_key' => $key, 'amount' => '10.00',
            'currency' => 'ETB', 'status' => 'PENDING',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'tx2', 'provider_reference' => 'R2',
            'idempotency_key' => $key, 'amount' => '20.00',
            'currency' => 'ETB', 'status' => 'PENDING',
        ]);
    }

    public function test_unique_provider_and_provider_reference(): void
    {
        $need = $this->makeNeed();

        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'telebirr', 'provider_reference' => 'SHARED-REF',
            'idempotency_key' => (string) Str::uuid(), 'amount' => '10.00',
            'currency' => 'ETB', 'status' => 'PENDING',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Payment::create([
            'payer_id' => $need->requester_id, 'need_id' => $need->id,
            'purpose' => 'BOOST', 'provider' => 'telebirr', 'provider_reference' => 'SHARED-REF',
            'idempotency_key' => (string) Str::uuid(), 'amount' => '20.00',
            'currency' => 'ETB', 'status' => 'PENDING',
        ]);
    }
}

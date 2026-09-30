<?php

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B25 / R-TEST-01 — PaymentEvent model unit tests.
 * Schema: 2026_09_29_000016. No HasFactory, no timestamps.
 */
class PaymentEventTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makePayment(): Payment
    {
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);

        return Payment::create([
            'payer_id'           => $requester->id,
            'need_id'            => $need->id,
            'purpose'            => 'BOOST',
            'provider'           => 'telebirr',
            'provider_reference' => 'TXN-' . Str::uuid(),
            'idempotency_key'    => (string) Str::uuid(),
            'amount'             => '100.00',
            'currency'           => 'ETB',
            'status'             => 'PENDING',
        ]);
    }

    private function makeEvent(array $overrides = []): PaymentEvent
    {
        return PaymentEvent::create(array_merge([
            'payment_id'         => $this->makePayment()->id,
            'provider'           => 'telebirr',
            'provider_event_id'  => 'EVT-' . Str::uuid(),
            'payload_digest'     => hash('sha256', 'test-payload'),
            'signature_valid'    => true,
            'event_type'         => 'payment.succeeded',
            'processing_status'  => PaymentEvent::STATUS_RECEIVED,
            'sanitized_metadata' => ['amount' => '100.00'],
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $e = $this->makeEvent();
        $this->assertNotNull($e->id);
        $this->assertTrue(Str::isUuid($e->id));
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new PaymentEvent())->usesTimestamps());
    }

    public function test_belongs_to_payment(): void
    {
        $payment = $this->makePayment();
        $event = $this->makeEvent(['payment_id' => $payment->id]);

        $this->assertInstanceOf(Payment::class, $event->payment);
        $this->assertSame($payment->id, $event->payment->id);
    }

    public function test_payment_id_nullable(): void
    {
        $event = $this->makeEvent(['payment_id' => null]);
        $this->assertNull($event->fresh()->payment_id);
        $this->assertNull($event->fresh()->payment);
    }

    public function test_signature_valid_casts_to_boolean(): void
    {
        $eTrue = $this->makeEvent(['signature_valid' => true]);
        $this->assertTrue($eTrue->fresh()->signature_valid);

        $eFalse = $this->makeEvent([
            'provider_event_id' => 'EVT-' . Str::uuid(),
            'signature_valid'   => false,
        ]);
        $this->assertFalse($eFalse->fresh()->signature_valid);
    }

    public function test_sanitized_metadata_casts_to_array(): void
    {
        $e = $this->makeEvent(['sanitized_metadata' => ['a' => 1, 'b' => 2]]);
        $fresh = $e->fresh();

        $this->assertIsArray($fresh->sanitized_metadata);
        $this->assertSame(1, $fresh->sanitized_metadata['a']);
        $this->assertSame(2, $fresh->sanitized_metadata['b']);
    }

    public function test_sanitized_metadata_nullable(): void
    {
        $e = $this->makeEvent(['sanitized_metadata' => null]);
        $this->assertNull($e->fresh()->sanitized_metadata);
    }

    public function test_received_at_casts_to_datetime(): void
    {
        $e = $this->makeEvent();
        $this->assertInstanceOf(\Carbon\Carbon::class, $e->fresh()->received_at);
    }

    public function test_processing_status_constants(): void
    {
        $this->assertSame('RECEIVED', PaymentEvent::STATUS_RECEIVED);
        $this->assertSame('APPLIED', PaymentEvent::STATUS_APPLIED);
        $this->assertSame('DUPLICATE', PaymentEvent::STATUS_DUPLICATE);
        $this->assertSame('REVIEW_REQUIRED', PaymentEvent::STATUS_REVIEW_REQUIRED);
        $this->assertSame('REJECTED', PaymentEvent::STATUS_REJECTED);
    }

    public function test_default_processing_status_is_received(): void
    {
        $e = $this->makeEvent();
        $this->assertSame('RECEIVED', $e->fresh()->processing_status);
    }

    public function test_unique_provider_and_provider_event_id(): void
    {
        $this->makeEvent([
            'provider'          => 'telebirr',
            'provider_event_id' => 'DUP-001',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->makeEvent([
            'provider'          => 'telebirr',
            'provider_event_id' => 'DUP-001',
        ]);
    }

    public function test_payload_digest_stores_64_hex(): void
    {
        $digest = hash('sha256', 'my-payload');
        $e = $this->makeEvent(['payload_digest' => $digest]);

        $this->assertSame(64, strlen($e->fresh()->payload_digest));
        $this->assertSame($digest, $e->fresh()->payload_digest);
    }

    public function test_event_type_stored_correctly(): void
    {
        $e = $this->makeEvent(['event_type' => 'payment.failed']);
        $this->assertSame('payment.failed', $e->fresh()->event_type);
    }
}

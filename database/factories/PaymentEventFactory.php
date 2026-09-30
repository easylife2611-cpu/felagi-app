<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentEventFactory extends Factory
{
    protected $model = PaymentEvent::class;

    public function definition(): array
    {
        return [
            'payment_id'         => Payment::factory(),
            'provider'           => 'telebirr',
            'provider_event_id'  => 'EVT-' . Str::uuid(),
            'payload_digest'     => hash('sha256', 'test-payload-' . Str::random(16)),
            'signature_valid'    => true,
            'event_type'         => 'payment.succeeded',
            'processing_status'  => PaymentEvent::STATUS_RECEIVED,
            'sanitized_metadata' => ['amount' => '199.99', 'currency' => 'ETB'],
        ];
    }

    public function applied(): static
    {
        return $this->state(fn () => ['processing_status' => PaymentEvent::STATUS_APPLIED]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'processing_status' => PaymentEvent::STATUS_REJECTED,
            'signature_valid'   => false,
        ]);
    }

    public function orphan(): static
    {
        return $this->state(fn () => ['payment_id' => null]);
    }
}

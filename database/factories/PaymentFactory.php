<?php

namespace Database\Factories;

use App\Models\Need;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $requester = User::factory();

        return [
            'payer_id'           => User::factory(),
            'need_id'            => Need::factory(),
            'purpose'            => Payment::PURPOSE_BOOST,
            'provider'           => 'telebirr',
            'provider_reference' => 'TXN-' . Str::uuid(),
            'idempotency_key'    => (string) Str::uuid(),
            'amount'             => '199.99',
            'currency'           => 'ETB',
            'status'             => Payment::STATUS_PENDING,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_FAILED,
            'failed_at' => now(),
            'failure_code' => 'INSUFFICIENT_FUNDS',
        ]);
    }
}

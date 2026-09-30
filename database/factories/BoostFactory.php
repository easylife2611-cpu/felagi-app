<?php

namespace Database\Factories;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Need;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoostFactory extends Factory
{
    protected $model = Boost::class;

    public function definition(): array
    {
        return [
            'need_id'        => Need::factory(),
            'requester_id'   => User::factory(),
            'package_id'     => BoostPackage::factory(),
            'payment_id'     => Payment::factory(),
            'price_snapshot' => '99.99',
            'currency'       => 'ETB',
            'duration_days'  => 7,
            'status'         => Boost::STATUS_PENDING,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status'     => Boost::STATUS_ACTIVE,
            'starts_at'  => now()->subHour(),
            'expires_at' => now()->addDay(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'     => Boost::STATUS_EXPIRED,
            'starts_at'  => now()->subDays(8),
            'expires_at' => now()->subDay(),
        ]);
    }
}

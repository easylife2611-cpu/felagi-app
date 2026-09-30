<?php

namespace Database\Factories;

use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'need_id'          => Need::factory(),
            'provider_id'      => User::factory(),
            'offered_price'    => '500.00',
            'currency'         => 'ETB',
            'proposal_message' => fake()->sentence(),
            'status'           => 'PENDING',
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status'      => 'ACCEPTED',
            'accepted_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'REJECTED']);
    }
}

<?php

namespace Database\Factories;

use App\Models\Advertiser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdvertiserFactory extends Factory
{
    protected $model = Advertiser::class;

    public function definition(): array
    {
        return [
            'name'              => 'sponsor_' . Str::random(8),
            'display_name'      => $this->faker->company(),
            'contact_reference' => $this->faker->email(),
            'status'            => Advertiser::STATUS_ACTIVE,
            'notes'             => null,
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['status' => Advertiser::STATUS_BLOCKED]);
    }
}

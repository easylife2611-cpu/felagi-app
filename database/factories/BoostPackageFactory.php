<?php

namespace Database\Factories;

use App\Models\BoostPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoostPackageFactory extends Factory
{
    protected $model = BoostPackage::class;

    public function definition(): array
    {
        return [
            'duration_days' => 7,
            'price'         => '99.99',
            'currency'      => 'ETB',
            'active'        => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }

    public function forDays(int $days): static
    {
        return $this->state(fn () => ['duration_days' => $days]);
    }
}

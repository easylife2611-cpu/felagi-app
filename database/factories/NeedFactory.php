<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NeedFactory extends Factory
{
    protected $model = Need::class;

    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'category_id'  => Category::factory(),
            'title'        => 'Test Need ' . fake()->word(),
            'description'  => fake()->paragraph(),
            'status'       => 'OPEN',
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => 'OPEN']);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'       => 'COMPLETED',
            'completed_at' => now(),
        ]);
    }
}

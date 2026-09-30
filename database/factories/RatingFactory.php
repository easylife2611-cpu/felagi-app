<?php

namespace Database\Factories;

use App\Models\Need;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'need_id' => Need::factory(),
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'score' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->optional()->sentence(),
            'created_at' => now(),
        ];
    }

    public function positive(): static
    {
        return $this->state(fn () => ['score' => 5]);
    }

    public function negative(): static
    {
        return $this->state(fn () => ['score' => 1]);
    }
}

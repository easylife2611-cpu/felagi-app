<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comparison;
use App\Models\ComparisonFeedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComparisonFeedbackFactory extends Factory
{
    protected $model = ComparisonFeedback::class;

    public function definition(): array
    {
        return [
            'comparison_id' => Comparison::factory(),
            'provider_id'   => User::factory(),
            'rating'        => ComparisonFeedback::RATING_FAIR,
            'comment'       => $this->faker->sentence(),
            'status'        => 'SUBMITTED',
        ];
    }

    public function fair(): static
    {
        return $this->state(fn () => ['rating' => ComparisonFeedback::RATING_FAIR]);
    }

    public function inaccurate(): static
    {
        return $this->state(fn () => ['rating' => ComparisonFeedback::RATING_INACCURATE]);
    }

    public function unclear(): static
    {
        return $this->state(fn () => ['rating' => ComparisonFeedback::RATING_UNCLEAR]);
    }

    public function other(): static
    {
        return $this->state(fn () => ['rating' => ComparisonFeedback::RATING_OTHER]);
    }

    public function withoutComment(): static
    {
        return $this->state(fn () => ['comment' => null]);
    }
}

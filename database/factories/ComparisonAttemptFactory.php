<?php

namespace Database\Factories;

use App\Models\Comparison;
use App\Models\ComparisonAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComparisonAttemptFactory extends Factory
{
    protected $model = ComparisonAttempt::class;

    public function definition(): array
    {
        return [
            'comparison_id'       => Comparison::factory(),
            'attempt_number'      => 1,
            'status'              => ComparisonAttempt::STATUS_PROCESSING,
            'provider_request_id' => 'req-' . Str::random(8),
            'token_usage'         => ['prompt' => 100, 'completion' => 200],
        ];
    }

    public function succeeded(): static
    {
        return $this->state(fn () => [
            'status'      => ComparisonAttempt::STATUS_SUCCEEDED,
            'finished_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'       => ComparisonAttempt::STATUS_FAILED,
            'finished_at'  => now(),
            'failure_code' => 'TIMEOUT',
        ]);
    }
}

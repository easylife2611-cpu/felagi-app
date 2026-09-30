<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'entity_type' => Report::ENTITY_NEED,
            'entity_id'   => (string) Str::uuid(),
            'reason_code' => Report::REASON_SPAM,
            'details'     => fake()->sentence(),
            'status'      => Report::STATUS_OPEN,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status'          => Report::STATUS_RESOLVED,
            'assigned_to'     => User::factory(),
            'resolution_code' => 'ACTION_TAKEN',
        ]);
    }
}

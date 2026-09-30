<?php

namespace Database\Factories;

use App\Models\OutboxEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OutboxEventFactory extends Factory
{
    protected $model = OutboxEvent::class;

    public function definition(): array
    {
        return [
            'event_type' => 'setting.changed',
            'aggregate_type' => 'setting',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => (string) Str::uuid(),
            'payload_json' => ['key' => 'feature.x', 'value' => true],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
            'locked_until' => null,
            'created_at' => now(),
            'completed_at' => null,
        ];
    }

    public function done(): static
    {
        return $this->state(fn () => [
            'status' => OutboxEvent::STATUS_DONE,
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => OutboxEvent::STATUS_FAILED]);
    }

    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => OutboxEvent::STATUS_PROCESSING,
            'locked_until' => now()->addMinutes(5),
        ]);
    }
}

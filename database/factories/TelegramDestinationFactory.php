<?php

namespace Database\Factories;

use App\Models\TelegramDestination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramDestinationFactory extends Factory
{
    protected $model = TelegramDestination::class;

    public function definition(): array
    {
        return [
            'telegram_chat_id' => '-' . $this->faker->numerify('100#########'),
            'public_username' => $this->faker->userName(),
            'type' => TelegramDestination::TYPE_OWNED_CHANNEL,
            'name' => $this->faker->company(),
            'allowed_category_ids' => [],
            'permission_evidence' => ['verified_at' => now()->toIso8601String()],
            'granted_at' => now(),
            'reviewed_at' => null,
            'expires_at' => null,
            'bot_permission_checked_at' => now(),
            'status' => TelegramDestination::STATUS_ACTIVE,
            'daily_cap' => 10,
            'quiet_hours' => ['start' => '22:00', 'end' => '06:00'],
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => TelegramDestination::STATUS_DRAFT]);
    }

    public function paused(): static
    {
        return $this->state(fn () => ['status' => TelegramDestination::STATUS_PAUSED]);
    }
}

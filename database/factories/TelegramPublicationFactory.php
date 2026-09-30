<?php

namespace Database\Factories;

use App\Models\Need;
use App\Models\TelegramDestination;
use App\Models\TelegramPublication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TelegramPublicationFactory extends Factory
{
    protected $model = TelegramPublication::class;

    public function definition(): array
    {
        return [
            'need_id' => Need::factory(),
            'destination_id' => TelegramDestination::factory(),
            'need_publication_version' => 1,
            'content_hash' => hash('sha256', Str::uuid()->toString()),
            'payload_snapshot' => ['title' => $this->faker->sentence()],
            'state' => TelegramPublication::STATE_QUEUED,
            'telegram_message_id' => null,
            'attempts' => 0,
            'next_attempt_at' => now(),
            'posted_at' => null,
            'removed_at' => null,
            'last_error_code' => null,
        ];
    }

    public function posted(): static
    {
        return $this->state(fn () => [
            'state' => TelegramPublication::STATE_POSTED,
            'telegram_message_id' => $this->faker->numberBetween(1000, 999999),
            'posted_at' => now(),
            'next_attempt_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'state' => TelegramPublication::STATE_FAILED,
            'last_error_code' => 'SEND_FAILED',
        ]);
    }

    public function removed(): static
    {
        return $this->state(fn () => [
            'state' => TelegramPublication::STATE_REMOVED,
            'removed_at' => now(),
        ]);
    }
}

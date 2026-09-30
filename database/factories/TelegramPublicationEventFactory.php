<?php

namespace Database\Factories;

use App\Models\TelegramPublication;
use App\Models\TelegramPublicationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TelegramPublicationEventFactory extends Factory
{
    protected $model = TelegramPublicationEvent::class;

    public function definition(): array
    {
        return [
            'publication_id' => TelegramPublication::factory(),
            'event_type' => TelegramPublicationEvent::TYPE_QUEUED,
            'request_id' => (string) Str::uuid(),
            'telegram_response_code' => null,
            'safe_metadata' => [],
            'occurred_at' => now(),
        ];
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'event_type' => TelegramPublicationEvent::TYPE_SENT,
            'telegram_response_code' => 200,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'event_type' => TelegramPublicationEvent::TYPE_FAILED,
            'telegram_response_code' => 400,
        ]);
    }
}

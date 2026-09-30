<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'recipient_user_id' => User::factory(),
            'type' => 'general',
            'entity_type' => 'need',
            'entity_id' => (string) Str::uuid(),
            'event_id' => (string) Str::uuid(),
            'title' => $this->faker->sentence(3),
            'body' => $this->faker->sentence(8),
            'channel' => Notification::CHANNEL_IN_APP,
            'delivery_status' => Notification::STATUS_SENT,
            'available_at' => now(),
            'sent_at' => now(),
            'read_at' => null,
            'failure_code' => null,
        ];
    }
}

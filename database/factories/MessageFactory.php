<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'sender_id' => User::factory(),
            'content' => $this->faker->sentence(6),
            'read_at' => null,
        ];
    }
}

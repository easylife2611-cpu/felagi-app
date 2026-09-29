<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * UserFactory — aligned to users table schema.
 *
 * Schema: id (uuid), telegram_subject (string unique), full_name (string),
 *         phone_number (nullable), profile_photo_url (nullable),
 *         status (enum), rating_score, rating_count, version.
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'telegram_subject' => 'tg_' . fake()->unique()->numerify('##########'),
            'full_name'        => fake()->name(),
            'phone_number'     => null,
            'profile_photo_url'=> null,
            'status'           => 'ACTIVE',
            'rating_score'     => null,
            'rating_count'     => 0,
            'version'          => 1,
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => 'SUSPENDED']);
    }

    public function banned(): static
    {
        return $this->state(fn () => ['status' => 'BANNED']);
    }
}

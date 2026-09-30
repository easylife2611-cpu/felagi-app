<?php

namespace Database\Factories;

use App\Models\AuthAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthAttemptFactory extends Factory
{
    protected $model = AuthAttempt::class;

    public function definition(): array
    {
        return [
            'state_hash' => hash('sha256', Str::random(32)),
            'nonce_hash' => hash('sha256', Str::random(32)),
            'pkce_verifier_encrypted' => Str::random(64),
            'handoff_hash' => null,
            'return_uri_allowlisted' => 'https://example.test/callback',
            'expires_at' => now()->addMinutes(10),
            'consumed_at' => null,
            'created_at' => now(),
        ];
    }

    public function consumed(): static
    {
        return $this->state(fn () => ['consumed_at' => now()]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => now()->subMinute(),
            'consumed_at' => null,
        ]);
    }
}

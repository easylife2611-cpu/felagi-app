<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailVerificationTokenFactory extends Factory
{
    protected $model = EmailVerificationToken::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'token_hash'  => EmailVerificationToken::hashToken(bin2hex(random_bytes(32))),
            'expires_at'  => now()->addHours(EmailVerificationToken::TTL_HOURS),
            'consumed_at' => null,
            'ip_address'  => $this->faker->ipv4(),
        ];
    }

    public function consumed(): static
    {
        return $this->state(fn () => ['consumed_at' => now()]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subMinute()]);
    }

    public function withPlaintext(string $plaintext): static
    {
        return $this->state(fn () => [
            'token_hash' => EmailVerificationToken::hashToken($plaintext),
        ]);
    }
}

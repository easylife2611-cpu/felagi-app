<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmailOtp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmailOtpFactory extends Factory
{
    protected $model = EmailOtp::class;

    public function definition(): array
    {
        return [
            'email'       => $this->faker->unique()->safeEmail(),
            'code_hash'   => Hash::make('123456'),
            'attempts'    => 0,
            'expires_at'  => now()->addMinutes(EmailOtp::TTL_MINUTES),
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

    public function withAttempts(int $n): static
    {
        return $this->state(fn () => ['attempts' => $n]);
    }

    public function withCode(string $plaintext): static
    {
        return $this->state(fn () => ['code_hash' => Hash::make($plaintext)]);
    }

    public function forEmail(string $email): static
    {
        return $this->state(fn () => ['email' => $email]);
    }
}

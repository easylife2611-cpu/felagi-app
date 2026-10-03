<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Need;
use App\Models\OfferSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferSubmissionFactory extends Factory
{
    protected $model = OfferSubmission::class;

    public function definition(): array
    {
        return [
            'need_id'     => Need::factory(),
            'provider_id' => User::factory(),
            'status'      => OfferSubmission::STATUS_PENDING_PAYMENT,
            'payment_id'  => null,
            'unlocked_at' => null,
            'expires_at'  => null,
        ];
    }

    public function unlocked(): static
    {
        return $this->state(fn () => [
            'status'      => OfferSubmission::STATUS_UNLOCKED,
            'unlocked_at' => now(),
            'expires_at'  => now()->addDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'      => OfferSubmission::STATUS_EXPIRED,
            'unlocked_at' => now()->subDays(10),
            'expires_at'  => now()->subDay(),
        ]);
    }

    public function forNeed(string $needId): static
    {
        return $this->state(fn () => ['need_id' => $needId]);
    }

    public function forProvider(string $providerId): static
    {
        return $this->state(fn () => ['provider_id' => $providerId]);
    }
}

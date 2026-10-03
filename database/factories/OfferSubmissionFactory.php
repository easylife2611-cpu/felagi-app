<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Need;
use App\Models\OfferSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OfferSubmissionFactory extends Factory
{
    protected $model = OfferSubmission::class;

    public function definition(): array
    {
        return [
            'need_id'          => Need::factory(),
            'provider_id'      => User::factory(),
            'state'            => OfferSubmission::STATE_FREE,
            'draft_id'         => (string) Str::uuid(),
            'draft_version'    => 1,
            'draft_hash'       => hash('sha256', 'draft-default'),
            'idempotency_key'  => 'idem-' . Str::random(20),
            'policy_version'   => '1.3',
            'amount_minor'     => 0,
            'currency'         => 'ETB',
            'payment_id'       => null,
            'offer_id'         => null,
            'refund_reference' => null,
            'unlocked_at'      => null,
            'expires_at'       => null,
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_FREE, 'amount_minor' => 0]);
    }

    public function paymentRequired(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_PAYMENT_REQUIRED, 'amount_minor' => 5000]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_PENDING, 'amount_minor' => 5000]);
    }

    public function paymentVerified(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_PAYMENT_VERIFIED, 'amount_minor' => 5000]);
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_SUBMITTED]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_FAILED]);
    }

    public function refundPending(): static
    {
        return $this->state(fn () => ['state' => OfferSubmission::STATE_REFUND_PENDING]);
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
<?php

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
            'need_id' => Need::factory(),
            'provider_id' => User::factory(),
            'status' => OfferSubmission::STATUS_PENDING_PAYMENT,
            'payment_id' => null,
            'unlocked_at' => null,
            'expires_at' => null,
        ];
    }
}

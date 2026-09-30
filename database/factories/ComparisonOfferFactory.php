<?php

namespace Database\Factories;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComparisonOfferFactory extends Factory
{
    protected $model = ComparisonOffer::class;

    public function definition(): array
    {
        return [
            'comparison_id'        => Comparison::factory(),
            'offer_id'             => Offer::factory(),
            'provider_id'          => User::factory(),
            'offer_snapshot'       => ['price' => 500, 'notes' => 'OK'],
            'credibility_snapshot' => ['rating' => 4.5, 'count' => 10],
            'offer_snapshot_hash'  => hash('sha256', 'offer-' . Str::random(8)),
        ];
    }
}

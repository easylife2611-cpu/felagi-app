<?php

namespace Database\Factories;

use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComparisonResultFactory extends Factory
{
    protected $model = ComparisonResult::class;

    public function definition(): array
    {
        return [
            'comparison_offer_id' => ComparisonOffer::factory(),
            'comparison_id'       => function (array $attrs) {
                return \App\Models\ComparisonOffer::find($attrs['comparison_offer_id'])->comparison_id;
            },
            'score'               => '8.50',
            'criterion_scores'    => ['price' => 9, 'speed' => 8],
            'strengths'           => ['Fast', 'Fair price'],
            'weaknesses'          => ['No portfolio'],
            'missing_information' => ['No ETA'],
            'risk_notes'          => ['First-time'],
            'fit_explanation'     => 'Good match.',
            'result_hash'         => hash('sha256', 'result-' . Str::random(8)),
        ];
    }
}

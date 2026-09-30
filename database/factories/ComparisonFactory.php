<?php

namespace Database\Factories;

use App\Models\Comparison;
use App\Models\Need;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComparisonFactory extends Factory
{
    protected $model = Comparison::class;

    public function definition(): array
    {
        return [
            'need_id'             => Need::factory(),
            'version_number'      => 1,
            'triggered_by'        => function (array $attrs) {
                return \App\Models\Need::find($attrs['need_id'])->requester_id;
            },
            'status'              => Comparison::STATUS_PENDING,
            'criteria_version'    => 'v1',
            'prompt_version'      => 'p1',
            'output_schema_version' => 's1',
            'ai_provider'         => 'openai',
            'model_id'            => 'gpt-4',
            'need_snapshot'       => ['title' => 'Test Need', 'budget' => 1000],
            'snapshot_hash'       => hash('sha256', 'test-snapshot-' . Str::random(8)),
            'eligible_offer_count'=> 0,
            'included_offer_count'=> 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'       => Comparison::STATUS_COMPLETED,
            'started_at'   => now()->subMinute(),
            'completed_at' => now(),
        ]);
    }
}

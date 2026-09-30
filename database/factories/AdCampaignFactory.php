<?php

namespace Database\Factories;

use App\Models\AdCampaign;
use App\Models\AdCreative;
use App\Models\Advertiser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdCampaignFactory extends Factory
{
    protected $model = AdCampaign::class;

    public function definition(): array
    {
        return [
            'advertiser_id'      => Advertiser::factory(),
            'campaign_name'      => $this->faker->sentence(3),
            'status'             => AdCampaign::STATUS_DRAFT,
            'start_at'           => now()->addDay(),
            'end_at'             => now()->addDays(8),
            'timezone'           => 'Africa/Addis_Ababa',
            'creative_id'        => AdCreative::factory(),
            'placement_ids'      => [AdCampaign::PLACEMENT_BROWSE],
            'targeting_policy'   => null,
            'frequency_policy'   => null,
            'priority'           => 0,
            'commercial_reference' => 'REF-' . strtoupper(bin2hex(random_bytes(4))),
            'destination_type'   => 'INTERNAL',
            'destination_value'  => '/browse',
            'created_by'         => User::factory(),
            'approved_by'        => null,
            'published_at'       => null,
            'paused_at'          => null,
            'ended_at'           => null,
            'version'            => 1,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status'       => AdCampaign::STATUS_ACTIVE,
            'start_at'     => now()->subHour(),
            'end_at'       => now()->addDays(7),
            'published_at' => now()->subHour(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status'   => AdCampaign::STATUS_SCHEDULED,
            'start_at' => now()->addDays(2),
            'end_at'   => now()->addDays(9),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'status'   => AdCampaign::STATUS_ENDED,
            'start_at' => now()->subDays(10),
            'end_at'   => now()->subDay(),
            'ended_at' => now()->subDay(),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn () => [
            'status'    => AdCampaign::STATUS_PAUSED,
            'paused_at' => now()->subHour(),
        ]);
    }

    public function onPlacement(string $placement): static
    {
        return $this->state(fn () => ['placement_ids' => [$placement]]);
    }
}

<?php

namespace Database\Factories;

use App\Models\AdCampaign;
use App\Models\AdDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdDeliveryFactory extends Factory
{
    protected $model = AdDelivery::class;

    public function definition(): array
    {
        return [
            'campaign_id'      => AdCampaign::factory(),
            'campaign_version' => 1,
            'placement_id'     => AdCampaign::PLACEMENT_BROWSE,
            'slot_id'          => 'slot_' . uniqid(),
            'creative_version' => 1,
            'policy_version'   => '1.4.0',
            'session_ref'      => 'sess_' . bin2hex(random_bytes(8)),
            'eligible_at'      => now(),
            'expires_at'       => now()->addMinutes(30),
        ];
    }
}

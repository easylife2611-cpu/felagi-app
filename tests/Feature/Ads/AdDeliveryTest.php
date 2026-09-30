<?php

namespace Tests\Feature\Ads;

use App\Models\AdCampaign;
use App\Models\AdCreative;
use App\Models\Advertiser;
use App\Models\AdDelivery;
use App\Services\Ads\AdDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_returns_disabled_when_master_off(): void
    {
        Cache::put('ads.master_enabled', false, 60);
        $svc = app(AdDeliveryService::class);
        $res = $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sess1');

        $this->assertSame(AdDeliveryService::SLOT_DISABLED, $res['state']);
        $this->assertSame('master_off', $res['reason']);
    }

    public function test_returns_disabled_when_placement_off(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        Cache::put('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', false, 60);

        $svc = app(AdDeliveryService::class);
        $res = $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sess1');

        $this->assertSame(AdDeliveryService::SLOT_DISABLED, $res['state']);
        $this->assertSame('placement_off', $res['reason']);
    }

    public function test_returns_not_eligible_for_unregistered_placement(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        $svc = app(AdDeliveryService::class);
        $res = $svc->resolve('AD_UNKNOWN_PLACEMENT', 'sess1');

        $this->assertSame(AdDeliveryService::SLOT_NOT_ELIGIBLE, $res['state']);
    }

    public function test_returns_empty_when_no_campaign_matches(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        Cache::put('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', true, 60);

        $svc = app(AdDeliveryService::class);
        $res = $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sess1');

        $this->assertSame(AdDeliveryService::SLOT_EMPTY, $res['state']);
    }

    public function test_returns_ready_with_active_campaign(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        Cache::put('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', true, 60);

        $adv = Advertiser::factory()->create();
        $cr  = AdCreative::factory()->create();
        AdCampaign::factory()->active()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
        ]);

        $svc = app(AdDeliveryService::class);
        $res = $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sess1');

        $this->assertSame(AdDeliveryService::SLOT_READY, $res['state']);
        $this->assertNotNull($res['delivery_id']);
        $this->assertIsArray($res['payload']);
        $this->assertArrayHasKey('copy', $res['payload']);
        $this->assertArrayHasKey('am', $res['payload']['copy']);
        $this->assertArrayHasKey('en', $res['payload']['copy']);
    }

    public function test_delivery_is_persisted(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        Cache::put('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', true, 60);

        $adv = Advertiser::factory()->create();
        $cr  = AdCreative::factory()->create();
        AdCampaign::factory()->active()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
        ]);

        $svc = app(AdDeliveryService::class);
        $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sess1');

        $this->assertEquals(1, AdDelivery::count());
    }

    public function test_frequency_cap_after_3_session_deliveries(): void
    {
        Cache::put('ads.master_enabled', true, 60);
        Cache::put('ads.placement.' . AdCampaign::PLACEMENT_BROWSE . '.enabled', true, 60);

        $adv = Advertiser::factory()->create();
        $cr  = AdCreative::factory()->create();
        AdCampaign::factory()->active()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
        ]);

        $svc = app(AdDeliveryService::class);
        $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sessA');
        $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sessA');
        $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sessA');

        $res = $svc->resolve(AdCampaign::PLACEMENT_BROWSE, 'sessA');
        $this->assertSame(AdDeliveryService::SLOT_FREQUENCY_CAPPED, $res['state']);
    }

    public function test_delivery_endpoint_returns_json(): void
    {
        Cache::put('ads.master_enabled', false, 60);

        $res = $this->getJson('/api/ads/placements/' . AdCampaign::PLACEMENT_BROWSE . '/delivery');
        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slot_state', 'DISABLED');
    }
}

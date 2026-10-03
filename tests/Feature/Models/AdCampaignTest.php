<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\AdCampaign;
use App\Models\AdCreative;
use App\Models\AdDelivery;
use App\Models\Advertiser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AdCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $c = AdCampaign::factory()->create();
        $this->assertTrue(Str::isUuid($c->id));
    }

    public function test_all_statuses_lists_ten_states(): void
    {
        $this->assertCount(10, AdCampaign::ALL_STATUSES);
    }

    public function test_all_statuses_contains_expected_values(): void
    {
        foreach ([
            'DRAFT', 'VALIDATED', 'SCHEDULED', 'ACTIVE', 'PAUSED',
            'ENDED', 'ARCHIVED', 'REJECTED', 'EXPIRED', 'BLOCKED',
        ] as $status) {
            $this->assertContains($status, AdCampaign::ALL_STATUSES);
        }
    }

    public function test_placements_lists_three(): void
    {
        $this->assertCount(3, AdCampaign::PLACEMENTS);
        $this->assertContains('AD_BROWSE_INLINE_01', AdCampaign::PLACEMENTS);
        $this->assertContains('AD_SEARCH_RESULTS_INLINE_01', AdCampaign::PLACEMENTS);
        $this->assertContains('AD_NEED_DETAIL_BOTTOM_01', AdCampaign::PLACEMENTS);
    }

    public function test_default_status_is_draft(): void
    {
        $c = AdCampaign::factory()->create();
        $this->assertSame(AdCampaign::STATUS_DRAFT, $c->status);
    }

    public function test_placement_ids_casts_to_array(): void
    {
        $c = AdCampaign::factory()->create(['placement_ids' => ['AD_BROWSE_INLINE_01']]);
        $fresh = $c->fresh();
        $this->assertIsArray($fresh->placement_ids);
        $this->assertSame(['AD_BROWSE_INLINE_01'], $fresh->placement_ids);
    }

    public function test_targeting_policy_casts_to_array(): void
    {
        $c = AdCampaign::factory()->create(['targeting_policy' => ['cities' => ['Addis']]]);
        $this->assertSame(['cities' => ['Addis']], $c->fresh()->targeting_policy);
    }

    public function test_start_at_casts_to_carbon(): void
    {
        $c = AdCampaign::factory()->create();
        $this->assertInstanceOf(Carbon::class, $c->fresh()->start_at);
    }

    public function test_belongs_to_advertiser(): void
    {
        $adv = Advertiser::factory()->create();
        $c = AdCampaign::factory()->create(['advertiser_id' => $adv->id]);
        $this->assertSame($adv->id, $c->fresh()->advertiser->id);
    }

    public function test_belongs_to_creative(): void
    {
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create(['creative_id' => $cr->id]);
        $this->assertSame($cr->id, $c->fresh()->creative->id);
    }

    public function test_belongs_to_creator(): void
    {
        $u = User::factory()->create();
        $c = AdCampaign::factory()->create(['created_by' => $u->id]);
        $this->assertSame($u->id, $c->fresh()->creator->id);
    }

    public function test_has_many_deliveries(): void
    {
        $c = AdCampaign::factory()->create();
        AdDelivery::factory()->count(2)->create(['campaign_id' => $c->id]);
        $this->assertCount(2, $c->fresh()->deliveries);
    }

    public function test_scope_active_returns_only_active(): void
    {
        AdCampaign::factory()->count(2)->create(['status' => AdCampaign::STATUS_DRAFT]);
        AdCampaign::factory()->active()->count(3)->create();
        $this->assertSame(3, AdCampaign::active()->count());
    }

    public function test_scope_servable_requires_active_and_in_window(): void
    {
        AdCampaign::factory()->active()->count(2)->create();
        AdCampaign::factory()->create([
            'status'   => AdCampaign::STATUS_DRAFT,
            'start_at' => now()->subHour(),
            'end_at'   => now()->addDay(),
        ]);
        $this->assertSame(2, AdCampaign::servable()->count());
    }

    public function test_scope_servable_excludes_future(): void
    {
        AdCampaign::factory()->create([
            'status'   => AdCampaign::STATUS_ACTIVE,
            'start_at' => now()->addDay(),
            'end_at'   => now()->addDays(8),
        ]);
        $this->assertSame(0, AdCampaign::servable()->count());
    }

    public function test_scope_servable_excludes_expired(): void
    {
        AdCampaign::factory()->create([
            'status'   => AdCampaign::STATUS_ACTIVE,
            'start_at' => now()->subDays(8),
            'end_at'   => now()->subDay(),
        ]);
        $this->assertSame(0, AdCampaign::servable()->count());
    }

    public function test_active_factory_state(): void
    {
        $c = AdCampaign::factory()->active()->create();
        $this->assertSame(AdCampaign::STATUS_ACTIVE, $c->status);
        $this->assertNotNull($c->published_at);
    }

    public function test_scheduled_factory_state(): void
    {
        $c = AdCampaign::factory()->scheduled()->create();
        $this->assertSame(AdCampaign::STATUS_SCHEDULED, $c->status);
    }

    public function test_ended_factory_state(): void
    {
        $c = AdCampaign::factory()->ended()->create();
        $this->assertSame(AdCampaign::STATUS_ENDED, $c->status);
        $this->assertNotNull($c->ended_at);
    }

    public function test_paused_factory_state(): void
    {
        $c = AdCampaign::factory()->paused()->create();
        $this->assertSame(AdCampaign::STATUS_PAUSED, $c->status);
        $this->assertNotNull($c->paused_at);
    }

    public function test_on_placement_factory_state(): void
    {
        $c = AdCampaign::factory()
            ->onPlacement(AdCampaign::PLACEMENT_SEARCH)
            ->create();
        $this->assertSame([AdCampaign::PLACEMENT_SEARCH], $c->fresh()->placement_ids);
    }
}

<?php

namespace Tests\Feature\Ads;

use App\Models\AdCampaign;
use App\Models\AdCreative;
use App\Models\Advertiser;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminAdsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => UserRole::ROLE_MAIN_ADMIN]);
        return $u;
    }

    public function test_index_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/ads')->assertStatus(401);
    }

    public function test_index_returns_overview(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/ads');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['master_enabled', 'counts', 'placements', 'today'],
            ]);
    }

    public function test_create_advertiser(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/ads/advertisers', [
            'name'         => 'acme_co',
            'display_name' => 'ACME Co.',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.name', 'acme_co');
        $this->assertEquals(1, Advertiser::count());
    }

    public function test_create_campaign(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/ads/campaigns', [
            'advertiser_id'    => $adv->id,
            'campaign_name'    => 'Test campaign',
            'start_at'         => now()->addDay()->toIso8601String(),
            'end_at'           => now()->addDays(8)->toIso8601String(),
            'placement_ids'    => [AdCampaign::PLACEMENT_BROWSE],
            'destination_type' => 'INTERNAL',
            'destination_value'=> '/browse',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.status', 'DRAFT');
        $this->assertEquals(1, AdCampaign::count());
    }

    public function test_validate_requires_creative(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => null,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/validate");

        $res->assertStatus(422);
    }

    public function test_validate_rejects_invalid_external_destination(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id'    => $adv->id,
            'creative_id'      => $cr->id,
            'placement_ids'    => [AdCampaign::PLACEMENT_BROWSE],
            'destination_type' => 'EXTERNAL',
            'destination_value'=> 'http://insecure.example.com',
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/validate");

        $res->assertStatus(422);
    }

    public function test_validate_rejects_invalid_internal_destination(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id'    => $adv->id,
            'creative_id'      => $cr->id,
            'placement_ids'    => [AdCampaign::PLACEMENT_BROWSE],
            'destination_type' => 'INTERNAL',
            'destination_value'=> '/some/other/path',
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/validate");

        $res->assertStatus(422);
    }

    public function test_validate_succeeds_with_complete_data(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id'    => $adv->id,
            'creative_id'      => $cr->id,
            'placement_ids'    => [AdCampaign::PLACEMENT_BROWSE],
            'destination_type' => 'INTERNAL',
            'destination_value'=> '/browse',
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/validate");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'VALIDATED');
    }

    public function test_publish_only_validated_campaigns(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
            'status'        => AdCampaign::STATUS_DRAFT,
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/publish");

        $res->assertStatus(409);
    }

    public function test_publish_validated_future_campaign_goes_scheduled(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
            'status'        => AdCampaign::STATUS_VALIDATED,
            'start_at'      => now()->addDay(),
            'end_at'        => now()->addDays(8),
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/publish");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'SCHEDULED');
    }

    public function test_publish_validated_started_campaign_goes_active(): void
    {
        $admin = $this->admin();
        $adv = Advertiser::factory()->create();
        $cr = AdCreative::factory()->create();
        $c = AdCampaign::factory()->create([
            'advertiser_id' => $adv->id,
            'creative_id'   => $cr->id,
            'placement_ids' => [AdCampaign::PLACEMENT_BROWSE],
            'status'        => AdCampaign::STATUS_VALIDATED,
            'start_at'      => now()->subHour(),
            'end_at'        => now()->addDays(7),
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/publish");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'ACTIVE');
    }

    public function test_pause_campaign(): void
    {
        $admin = $this->admin();
        $c = AdCampaign::factory()->active()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/pause");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'PAUSED');
    }

    public function test_archive_only_ended(): void
    {
        $admin = $this->admin();
        $c = AdCampaign::factory()->active()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/archive");

        $res->assertStatus(409);
    }

    public function test_archive_ended_campaign(): void
    {
        $admin = $this->admin();
        $c = AdCampaign::factory()->ended()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/campaigns/{$c->id}/archive");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'ARCHIVED');
    }

    public function test_destination_validate_accepts_https(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/ads/destinations/validate', [
                'type'  => 'EXTERNAL',
                'value' => 'https://example.com/page',
            ]);

        $res->assertStatus(200)
            ->assertJsonPath('data.valid', true);
    }

    public function test_destination_validate_rejects_localhost(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/ads/destinations/validate', [
                'type'  => 'EXTERNAL',
                'value' => 'https://localhost/secret',
            ]);

        $res->assertStatus(422);
    }

    public function test_destination_validate_rejects_private_ip(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/ads/destinations/validate', [
                'type'  => 'EXTERNAL',
                'value' => 'https://192.168.1.1/admin',
            ]);

        $res->assertStatus(422);
    }

    public function test_report_returns_ctr_when_impressions_exist(): void
    {
        $admin = $this->admin();
        $c = AdCampaign::factory()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/admin/ads/campaigns/{$c->id}/reports");

        $res->assertStatus(200)
            ->assertJsonStructure(['data' => ['impressions', 'clicks', 'ctr', 'coverage']]);
    }

    public function test_audit_returns_version_info(): void
    {
        $admin = $this->admin();
        $c = AdCampaign::factory()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/admin/ads/campaigns/{$c->id}/audit");

        $res->assertStatus(200)
            ->assertJsonPath('data.current_version', 1);
    }
}

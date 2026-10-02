<?php

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminMarketplaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminMarketplaceStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create([
            'user_id'    => $u->id,
            'role'       => UserRole::ROLE_MAIN_ADMIN,
            'granted_at' => now(),
        ]);
        return $u;
    }

    private function seedSetting(string $key, mixed $value, string $type = 'INTEGER'): Setting
    {
        return Setting::create([
            'key'            => $key,
            'group'          => 'CONFIG',
            'type'           => $type,
            'value_json'     => ['value' => $value],
            'default_json'   => ['value' => null],
            'risk'           => 'MEDIUM',
            'is_secret'      => false,
            'version_number' => 1,
        ]);
    }

    private function seedReport(string $status = Report::STATUS_OPEN): Report
    {
        $reporter = User::factory()->create();
        return Report::create([
            'id'          => (string) Str::uuid(),
            'reporter_id' => $reporter->id,
            'entity_type' => Report::ENTITY_NEED,
            'entity_id'   => (string) Str::uuid(),
            'reason_code' => Report::REASON_SAFETY,
            'status'      => $status,
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertArrayHasKey('settings', $status);
        $this->assertArrayHasKey('report_stats', $status);
        $this->assertArrayHasKey('recent_reports', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_defaults_when_db_empty(): void
    {
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertSame(20, $status['settings']['max_offers_per_comparison']);
        $this->assertSame(20, $status['settings']['max_open_needs_per_user']);
        $this->assertSame(90, $status['settings']['offer_deadline_max_days']);
        $this->assertSame('default', $status['settings']['source']);
    }

    public function test_service_reads_db_setting_when_present(): void
    {
        $this->seedSetting('marketplace.max_offers_per_comparison', 50);
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertSame(50, $status['settings']['max_offers_per_comparison']);
        $this->assertSame('db', $status['settings']['source']);
    }

    public function test_service_reports_zero_stats_without_data(): void
    {
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertSame(0, $status['report_stats']['open']);
        $this->assertSame(0, $status['report_stats']['in_review']);
        $this->assertSame(0, $status['report_stats']['resolved']);
        $this->assertSame(0, $status['report_stats']['total']);
    }

    public function test_service_counts_reports_by_status(): void
    {
        $this->seedReport(Report::STATUS_OPEN);
        $this->seedReport(Report::STATUS_OPEN);
        $this->seedReport(Report::STATUS_IN_REVIEW);
        $this->seedReport(Report::STATUS_RESOLVED);
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertSame(2, $status['report_stats']['open']);
        $this->assertSame(1, $status['report_stats']['in_review']);
        $this->assertSame(1, $status['report_stats']['resolved']);
        $this->assertSame(4, $status['report_stats']['total']);
    }

    public function test_service_returns_recent_reports(): void
    {
        $this->seedReport(Report::STATUS_OPEN);
        $status = app(AdminMarketplaceService::class)->status();
        $this->assertCount(1, $status['recent_reports']);
        $this->assertSame(Report::REASON_SAFETY, $status['recent_reports'][0]['reason_code']);
    }

    public function test_marketplace_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/marketplace-status')->assertUnauthorized();
    }

    public function test_marketplace_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/marketplace-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'settings' => ['max_offers_per_comparison', 'max_open_needs_per_user', 'offer_deadline_max_days', 'source'],
                    'report_stats' => ['open', 'in_review', 'resolved', 'dismissed', 'total'],
                    'recent_reports',
                    'computed_at',
                ],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A005')
            ->assertJsonPath('meta.source', 'live');
    }

    public function test_marketplace_status_reflects_db_setting(): void
    {
        $admin = $this->admin();
        $this->seedSetting('marketplace.max_offers_per_comparison', 35);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/marketplace-status')
            ->assertOk()
            ->assertJsonPath('data.settings.max_offers_per_comparison', 35)
            ->assertJsonPath('data.settings.source', 'db');
    }

    public function test_marketplace_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/marketplace-status')
            ->assertForbidden();
    }

    public function test_marketplace_page_requires_auth(): void
    {
        $this->get('/admin/marketplace')->assertRedirect('/admin/login');
    }

    public function test_marketplace_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/marketplace')
            ->assertOk()
            ->assertSee('marketplace-stats', false)
            ->assertSee('mk-reports', false);
    }

    public function test_marketplace_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/marketplace')
            ->assertOk()
            ->assertSee('/api/v1/admin/marketplace-status', false);
    }

    public function test_marketplace_page_has_pending_notice(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/marketplace')
            ->assertOk()
            ->assertSee('class="pending"', false);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\BoostPackage;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminMonetizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMonetizationStatusTest extends TestCase
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

    private function seedSetting(string $key, mixed $value, string $group = 'FEATURE', string $type = 'BOOLEAN'): Setting
    {
        return Setting::create([
            'key'            => $key,
            'group'          => $group,
            'type'           => $type,
            'value_json'     => ['value' => $value],
            'default_json'   => ['value' => null],
            'risk'           => 'HIGH',
            'is_secret'      => false,
            'version_number' => 1,
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminMonetizationService::class)->status();
        $this->assertArrayHasKey('master', $status);
        $this->assertArrayHasKey('offer_unlock', $status);
        $this->assertArrayHasKey('stats', $status);
        $this->assertArrayHasKey('packages', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_defaults_when_db_empty(): void
    {
        $status = app(AdminMonetizationService::class)->status();
        $this->assertFalse($status['master']['boost_enabled']);
        $this->assertFalse($status['master']['payments_enabled']);
        $this->assertSame('default', $status['master']['source']);
        $this->assertFalse($status['offer_unlock']['enabled']);
        $this->assertSame(0, $status['offer_unlock']['fee']);
        $this->assertSame('ETB', $status['offer_unlock']['currency']);
    }

    public function test_service_reads_db_values_when_present(): void
    {
        $this->seedSetting('feature.boosts', true);
        $this->seedSetting('feature.payments', true);
        $status = app(AdminMonetizationService::class)->status();
        $this->assertTrue($status['master']['boost_enabled']);
        $this->assertTrue($status['master']['payments_enabled']);
        $this->assertSame('db', $status['master']['source']);
    }

    public function test_service_reports_zero_stats_without_data(): void
    {
        $status = app(AdminMonetizationService::class)->status();
        $this->assertSame(0, $status['stats']['total_packages']);
        $this->assertSame(0, $status['stats']['active_packages']);
        $this->assertSame(0, $status['stats']['active_boosts']);
        $this->assertSame(0, $status['stats']['total_unlocks']);
        $this->assertSame(0.0, (float) $status['stats']['total_revenue']);
    }

    public function test_service_returns_packages_list(): void
    {
        BoostPackage::create([
            'id'            => (string) \Illuminate\Support\Str::uuid(),
            'duration_days' => 7,
            'price'         => 99.50,
            'currency'      => 'ETB',
            'active'        => true,
        ]);
        $status = app(AdminMonetizationService::class)->status();
        $this->assertSame(1, $status['stats']['total_packages']);
        $this->assertSame(1, $status['stats']['active_packages']);
        $this->assertCount(1, $status['packages']);
        $this->assertSame(7, $status['packages'][0]['duration_days']);
    }

    public function test_monetization_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/monetization-status')->assertUnauthorized();
    }

    public function test_monetization_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/monetization-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'master' => ['boost_enabled', 'payments_enabled', 'source'],
                    'offer_unlock' => ['enabled', 'fee', 'currency', 'source'],
                    'stats' => ['total_packages', 'active_packages', 'active_boosts', 'total_unlocks', 'total_revenue'],
                    'packages',
                    'computed_at',
                ],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A020')
            ->assertJsonPath('meta.source', 'live');
    }

    public function test_monetization_status_reflects_db_setting(): void
    {
        $admin = $this->admin();
        $this->seedSetting('feature.boosts', true);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/monetization-status')
            ->assertOk()
            ->assertJsonPath('data.master.boost_enabled', true)
            ->assertJsonPath('data.master.source', 'db');
    }

    public function test_monetization_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/monetization-status')
            ->assertForbidden();
    }

    public function test_monetization_page_requires_auth(): void
    {
        $this->get('/admin/monetization')->assertRedirect('/admin/login');
    }

    public function test_monetization_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/monetization')
            ->assertOk()
            ->assertSee('monetization-stats', false)
            ->assertSee('mon-packages', false);
    }

    public function test_monetization_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/monetization')
            ->assertOk()
            ->assertSee('/api/v1/admin/monetization-status', false);
    }

    public function test_monetization_page_has_pending_notice(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/monetization')
            ->assertOk()
            ->assertSee('class="pending"', false);
    }
}

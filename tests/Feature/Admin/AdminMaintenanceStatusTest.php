<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminMaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminMaintenanceStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
        Cache::flush();
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

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminMaintenanceService::class)->status();
        $this->assertArrayHasKey('maintenance', $status);
        $this->assertArrayHasKey('cache', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_maintenance_disabled_by_default(): void
    {
        $state = app(AdminMaintenanceService::class)->maintenanceState();
        $this->assertFalse($state['enabled']);
        $this->assertNull($state['message']);
        $this->assertNull($state['since']);
    }

    public function test_maintenance_reflects_cache_state(): void
    {
        Cache::forever(AdminMaintenanceService::CACHE_KEY, true);
        Cache::forever(AdminMaintenanceService::MESSAGE_KEY, 'Scheduled maintenance');
        $state = app(AdminMaintenanceService::class)->maintenanceState();
        $this->assertTrue($state['enabled']);
        $this->assertSame('Scheduled maintenance', $state['message']);
    }

    public function test_cache_state_returns_expected_keys(): void
    {
        $cache = app(AdminMaintenanceService::class)->status()['cache'];
        $this->assertArrayHasKey('config_cached', $cache);
        $this->assertArrayHasKey('route_cached', $cache);
        $this->assertArrayHasKey('view_cached', $cache);
        $this->assertArrayHasKey('storage_writable', $cache);
        $this->assertArrayHasKey('last_refresh', $cache);
        $this->assertIsInt($cache['view_cached']);
    }

    public function test_cache_last_refresh_reflects_cache(): void
    {
        Cache::forever(AdminMaintenanceService::LAST_REFRESH_KEY, '2026-10-02T06:00:00+00:00');
        $cache = app(AdminMaintenanceService::class)->status()['cache'];
        $this->assertSame('2026-10-02T06:00:00+00:00', $cache['last_refresh']);
    }

    public function test_maintenance_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/maintenance-status')->assertUnauthorized();
    }

    public function test_maintenance_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/maintenance-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'maintenance' => ['enabled'],
                    'cache' => ['config_cached', 'route_cached', 'view_cached', 'storage_writable'],
                    'computed_at',
                ],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A021')
            ->assertJsonPath('meta.source', 'live')
            ->assertJsonPath('data.maintenance.enabled', false);
    }

    public function test_maintenance_status_reflects_enabled_state(): void
    {
        $admin = $this->admin();
        Cache::forever(AdminMaintenanceService::CACHE_KEY, true);
        Cache::forever(AdminMaintenanceService::MESSAGE_KEY, 'Testing');
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/maintenance-status')
            ->assertOk()
            ->assertJsonPath('data.maintenance.enabled', true)
            ->assertJsonPath('data.maintenance.message', 'Testing');
    }

    public function test_maintenance_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/maintenance-status')
            ->assertForbidden();
    }

    public function test_maintenance_page_requires_auth(): void
    {
        $this->get('/admin/maintenance')->assertRedirect('/admin/login');
    }

    public function test_maintenance_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/maintenance')
            ->assertOk()
            ->assertSee('maintenance-stats', false)
            ->assertSee('cache-status', false);
    }

    public function test_maintenance_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/maintenance')
            ->assertOk()
            ->assertSee('/api/v1/admin/maintenance-status', false);
    }
}

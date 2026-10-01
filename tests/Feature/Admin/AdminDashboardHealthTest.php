<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminHealthService;
use App\Services\Admin\AdminMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminDashboardHealthTest extends TestCase
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
        UserRole::create([
            'user_id'    => $u->id,
            'role'       => UserRole::ROLE_MAIN_ADMIN,
            'granted_at' => now(),
        ]);
        return $u;
    }

    // ─── Metrics Service ───

    public function test_metrics_service_returns_summary(): void
    {
        $service = app(AdminMetricsService::class);
        $summary = $service->summary();

        $this->assertArrayHasKey('users', $summary);
        $this->assertArrayHasKey('needs', $summary);
        $this->assertArrayHasKey('offers', $summary);
        $this->assertArrayHasKey('reports', $summary);
        $this->assertArrayHasKey('computed_at', $summary);
        $this->assertIsInt($summary['users']);
    }

    public function test_metrics_service_counts_users(): void
    {
        User::factory()->count(3)->create();
        $service = app(AdminMetricsService::class);
        $summary = $service->summary();

        $this->assertEquals(3, $summary['users']);
    }

    // ─── Health Service ───

    public function test_health_service_returns_status(): void
    {
        $service = app(AdminHealthService::class);
        $status = $service->status();

        $this->assertArrayHasKey('database', $status);
        $this->assertArrayHasKey('cache', $status);
        $this->assertArrayHasKey('queue', $status);
        $this->assertArrayHasKey('storage', $status);
        $this->assertArrayHasKey('telegram', $status);
    }

    public function test_health_service_database_is_ok(): void
    {
        $status = app(AdminHealthService::class)->status();
        $this->assertEquals('ok', $status['database']['status']);
        $this->assertIsInt($status['database']['latency_ms']);
    }

    public function test_health_service_cache_is_ok(): void
    {
        $status = app(AdminHealthService::class)->status();
        $this->assertEquals('ok', $status['cache']['status']);
    }

    // ─── API Dashboard ───

    public function test_dashboard_api_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/dashboard-metrics')->assertUnauthorized();
    }

    public function test_dashboard_api_returns_stats(): void
    {
        User::factory()->count(2)->create();
        $admin = $this->admin();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/dashboard-metrics')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'stats' => ['users', 'needs', 'offers', 'reports', 'computed_at'],
                    'source',
                ],
            ]);
    }

    // ─── API Health ───

    public function test_health_api_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/health-status')->assertUnauthorized();
    }

    public function test_health_api_returns_components(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/health-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'components' => [
                        'database', 'cache', 'queue', 'storage', 'telegram',
                    ],
                    'source',
                ],
            ]);
    }

    // ─── Web Pages ───

    public function test_dashboard_page_requires_auth(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_dashboard_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('stat-users', false)
            ->assertSee('stat-needs', false);
    }

    public function test_health_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/health')
            ->assertOk()
            ->assertSee('health-components', false);
    }
}

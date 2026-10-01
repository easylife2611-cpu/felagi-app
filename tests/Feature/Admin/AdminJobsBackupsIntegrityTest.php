<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminBackupsService;
use App\Services\Admin\AdminIntegrityService;
use App\Services\Admin\AdminJobsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobsBackupsIntegrityTest extends TestCase
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

    // ─── Jobs Service ───

    public function test_jobs_service_returns_status(): void
    {
        $status = app(AdminJobsService::class)->status();
        $this->assertArrayHasKey('driver', $status);
        $this->assertArrayHasKey('pending', $status);
        $this->assertArrayHasKey('failed', $status);
        $this->assertArrayHasKey('processed', $status);
        $this->assertArrayHasKey('recent_failed', $status);
        $this->assertIsInt($status['pending']);
    }

    // ─── Backups Service ───

    public function test_backups_service_returns_status(): void
    {
        $status = app(AdminBackupsService::class)->status();
        $this->assertArrayHasKey('directory', $status);
        $this->assertArrayHasKey('exists', $status);
        $this->assertArrayHasKey('total_backups', $status);
        $this->assertArrayHasKey('total_size', $status);
        $this->assertIsInt($status['total_backups']);
    }

    // ─── Integrity Service ───

    public function test_integrity_service_returns_status(): void
    {
        $status = app(AdminIntegrityService::class)->status();
        $this->assertArrayHasKey('config_cache', $status);
        $this->assertArrayHasKey('route_cache', $status);
        $this->assertArrayHasKey('view_cache', $status);
        $this->assertArrayHasKey('storage_dirs', $status);
        $this->assertArrayHasKey('db_tables', $status);
    }

    public function test_integrity_db_tables_ok(): void
    {
        $status = app(AdminIntegrityService::class)->status();
        $this->assertGreaterThan(0, $status['db_tables']['count']);
        $this->assertEquals('ok', $status['db_tables']['status']);
    }

    // ─── API Jobs ───

    public function test_jobs_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/jobs-status')->assertUnauthorized();
    }

    public function test_jobs_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/jobs-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['driver', 'pending', 'failed', 'processed', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A012')
            ->assertJsonPath('meta.source', 'live');
    }

    // ─── API Backups ───

    public function test_backups_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/backups-status')->assertUnauthorized();
    }

    public function test_backups_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/backups-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['directory', 'exists', 'total_backups', 'total_size', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A013')
            ->assertJsonPath('meta.source', 'live');
    }

    // ─── API Integrity ───

    public function test_integrity_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/integrity-status')->assertUnauthorized();
    }

    public function test_integrity_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/integrity-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['config_cache', 'route_cache', 'view_cache', 'storage_dirs', 'db_tables', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A014')
            ->assertJsonPath('meta.source', 'live');
    }

    // ─── Web Pages ───

    public function test_jobs_page_requires_auth(): void
    {
        $this->get('/admin/jobs')->assertRedirect('/admin/login');
    }

    public function test_backups_page_requires_auth(): void
    {
        $this->get('/admin/backups')->assertRedirect('/admin/login');
    }

    public function test_integrity_page_requires_auth(): void
    {
        $this->get('/admin/integrity')->assertRedirect('/admin/login');
    }

    public function test_jobs_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/jobs')
            ->assertOk()
            ->assertSee('stat-jobs-pending', false);
    }

    public function test_backups_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/backups')
            ->assertOk()
            ->assertSee('stat-backup-count', false);
    }

    public function test_integrity_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/integrity')
            ->assertOk()
            ->assertSee('integrity-checks', false);
    }
}

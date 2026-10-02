<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminSecurityService;
use App\Services\Admin\AdminRecoveryService;
use App\Services\Admin\AdminSafeModeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityRecoverySafeModeTest extends TestCase
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

    // ─── Security Service ───

    public function test_security_service_returns_status(): void
    {
        $status = app(AdminSecurityService::class)->status();
        $this->assertArrayHasKey('total_users', $status);
        $this->assertArrayHasKey('totp_enabled', $status);
        $this->assertArrayHasKey('suspended', $status);
        $this->assertArrayHasKey('admin_count', $status);
        $this->assertIsInt($status['total_users']);
    }

    // ─── Recovery Service ───

    public function test_recovery_service_returns_status(): void
    {
        $status = app(AdminRecoveryService::class)->status();
        $this->assertArrayHasKey('available_restore_points', $status);
        $this->assertArrayHasKey('recovery_steps', $status);
        $this->assertIsArray($status['recovery_steps']);
        $this->assertCount(5, $status['recovery_steps']);
    }

    // ─── Safe Mode Service ───

    public function test_safe_mode_service_returns_status(): void
    {
        $status = app(AdminSafeModeService::class)->status();
        $this->assertArrayHasKey('enabled', $status);
        $this->assertArrayHasKey('affected', $status);
        $this->assertArrayHasKey('checklist', $status);
        $this->assertIsBool($status['enabled']);
    }

    public function test_safe_mode_can_enable_and_disable(): void
    {
        $svc = app(AdminSafeModeService::class);
        $this->assertFalse($svc->isEnabled());
        $svc->enable('Test reason');
        $this->assertTrue($svc->isEnabled());
        $this->assertEquals('Test reason', $svc->reason());
        $svc->disable();
        $this->assertFalse($svc->isEnabled());
    }

    // ─── API Security ───

    public function test_security_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/security-status')->assertUnauthorized();
    }

    public function test_security_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/security-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['total_users', 'totp_enabled', 'suspended', 'admin_count', 'attempts_24h', 'recent_attempts', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A015')
            ->assertJsonPath('meta.source', 'live');
    }

    // ─── API Recovery ───

    public function test_recovery_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/recovery-status')->assertUnauthorized();
    }

    public function test_recovery_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/recovery-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['available_restore_points', 'recovery_steps', 'recent_restore_points', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A018')
            ->assertJsonPath('meta.source', 'live');
    }

    // ─── API Safe Mode ───

    public function test_safe_mode_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/safe-mode-status')->assertUnauthorized();
    }

    public function test_safe_mode_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/safe-mode-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['enabled', 'affected', 'checklist', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A019')
            ->assertJsonPath('meta.source', 'live')
            ->assertJsonPath('data.enabled', false);
    }

    // ─── Web Pages ───

    public function test_security_page_requires_auth(): void
    {
        $this->get('/admin/security')->assertRedirect('/admin/login');
    }

    public function test_recovery_page_requires_auth(): void
    {
        $this->get('/admin/recovery')->assertRedirect('/admin/login');
    }

    public function test_safe_mode_page_requires_auth(): void
    {
        $this->get('/admin/safe-mode')->assertRedirect('/admin/login');
    }

    public function test_security_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/security')
            ->assertOk()
            ->assertSee('stat-sec-users', false);
    }

    public function test_recovery_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/recovery')
            ->assertOk()
            ->assertSee('rec-points', false);
    }

    public function test_safe_mode_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/safe-mode')
            ->assertOk()
            ->assertSee('sm-status', false);
    }
}

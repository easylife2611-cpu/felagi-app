<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminReadEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public static function screens(): array
    {
        return [
            ['dashboard',         'dashboard'],
            ['telegram-overview', 'telegram'],
            ['health',            'health'],
            ['features',          'features'],
            ['marketplace',       'marketplace'],
            ['ai',                'ai'],
            ['payments',          'payments'],
            ['users',             'users'],
            ['content',           'content'],
            ['notifications',     'notifications'],
            ['files',             'files'],
            ['jobs',              'jobs'],
            ['backups',           'backups'],
            ['integrity',         'integrity'],
            ['security',          'security'],
            ['audit',             'audit'],
            ['settings',          'settings'],
            ['recovery',          'recovery'],
            ['safe-mode',         'safe-mode'],
            ['monetization',      'monetization'],
            ['maintenance',       'maintenance'],
            ['reports',           'reports'],
            ['ads',               'ads'],
        ];
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        UserRole::create([
            'user_id'    => $user->id,
            'role'       => $role,
            'revoked_at' => null,
        ]);
        return $user;
    }

    #[DataProvider('screens')]
    public function test_main_admin_reads_every_screen(string $path, string $area): void
    {
        $admin = $this->makeUserWithRole(UserRole::ROLE_MAIN_ADMIN);

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/admin/{$path}");

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.area', $area)
            ->assertJsonStructure([
                'success', 'data', 'message', 'request_id',
                'meta' => ['screen', 'area', 'source', 'total', 'page', 'per_page', 'last_page'],
            ]);
    }

    public function test_unauthenticated_returns_401(): void
    {
        $this->getJson('/api/v1/admin/dashboard')->assertStatus(401);
    }

    public function test_user_without_admin_role_returns_403(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_admin_cannot_read_security_screen(): void
    {
        $admin = $this->makeUserWithRole(UserRole::ROLE_ADMIN);

        // Non-secret: OK
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/users')
            ->assertStatus(200);

        // Secret: forbidden
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/security')
            ->assertStatus(403);
    }

    public function test_moderator_only_reads_reports_and_telegram(): void
    {
        $mod = $this->makeUserWithRole(UserRole::ROLE_MODERATOR);

        $this->actingAs($mod, 'sanctum')
            ->getJson('/api/v1/admin/reports')
            ->assertStatus(200);

        $this->actingAs($mod, 'sanctum')
            ->getJson('/api/v1/admin/telegram-overview')
            ->assertStatus(200);

        $this->actingAs($mod, 'sanctum')
            ->getJson('/api/v1/admin/users')
            ->assertStatus(403);
    }
}

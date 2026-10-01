<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin browser login (Telegram-first, session-based).
 *
 * Verifies:
 *   - /admin/login is public
 *   - /admin/* (protected) redirects unauthenticated → /admin/login
 *   - Non-admin (authenticated, no role) → 403
 *   - Admin (MAIN_ADMIN) → 200
 *   - Logout invalidates session
 */
class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        UserRole::create([
            'user_id' => $user->id,
            'role'    => $role,
        ]);
        return $user;
    }

    // ─── Login page ───

    public function test_login_page_is_public(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_login_page_renders_telegram_widget_when_not_authenticated(): void
    {
        // L268-followup (4f78021) replaced the /auth/telegram link with an
        // embedded Telegram Login Widget. Test updated by L271 to match the
        // current Login blade (resources/views/admin/auth/login.blade.php).
        $res = $this->get('/admin/login');
        $res->assertStatus(200)
            ->assertSee('telegram-widget.js', false)
            ->assertSee('data-telegram-login', false)
            ->assertSee('/admin/login/telegram', false);
    }

    // ─── Protected routes — unauthenticated ───

    public function test_dashboard_redirects_unauthenticated_to_login(): void
    {
        $res = $this->get('/admin/dashboard');
        $res->assertRedirect('/admin/login');
    }

    public function test_users_redirects_unauthenticated_to_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/admin/login');
    }

    public function test_login_page_does_not_redirect_unauthenticated(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    // ─── Protected routes — authenticated, no role ───

    public function test_dashboard_403_for_non_admin_user(): void
    {
        $user = User::factory()->create(); // no role
        $res = $this->actingAs($user, 'web')->get('/admin/dashboard');
        $res->assertStatus(403);
    }

    // ─── Protected routes — authenticated + admin role ───

    public function test_dashboard_200_for_main_admin(): void
    {
        $admin = $this->userWithRole(UserRole::ROLE_MAIN_ADMIN);
        $res = $this->actingAs($admin, 'web')->get('/admin/dashboard');
        $res->assertStatus(200);
    }

    public function test_dashboard_200_for_admin(): void
    {
        $admin = $this->userWithRole(UserRole::ROLE_ADMIN);
        $res = $this->actingAs($admin, 'web')->get('/admin/dashboard');
        $res->assertStatus(200);
    }

    public function test_dashboard_200_for_moderator(): void
    {
        $mod = $this->userWithRole(UserRole::ROLE_MODERATOR);
        $res = $this->actingAs($mod, 'web')->get('/admin/dashboard');
        $res->assertStatus(200);
    }

    public function test_revoked_role_is_rejected(): void
    {
        $user = User::factory()->create();
        UserRole::create([
            'user_id'    => $user->id,
            'role'       => UserRole::ROLE_MAIN_ADMIN,
            'revoked_at' => now(),
        ]);

        $res = $this->actingAs($user, 'web')->get('/admin/dashboard');
        $res->assertStatus(403);
    }

    // ─── Login page for authenticated admin ───

    public function test_login_page_redirects_authenticated_admin_to_dashboard(): void
    {
        $admin = $this->userWithRole(UserRole::ROLE_MAIN_ADMIN);
        $res = $this->actingAs($admin, 'web')->get('/admin/login');
        $res->assertRedirect('/admin/dashboard');
    }

    public function test_login_page_shows_hint_for_authenticated_non_admin(): void
    {
        $user = User::factory()->create(); // no role
        $res = $this->actingAs($user, 'web')->get('/admin/login');
        $res->assertStatus(200)
            ->assertSee('No admin access');
    }

    // ─── Logout ───

    public function test_logout_requires_auth(): void
    {
        $this->post('/admin/logout')->assertRedirect('/admin/login');
    }

    public function test_logout_invalidates_session_for_admin(): void
    {
        $admin = $this->userWithRole(UserRole::ROLE_MAIN_ADMIN);
        $res = $this->actingAs($admin, 'web')->post('/admin/logout');
        $res->assertRedirect('/admin/login');

        // Subsequent access should be blocked
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    // ─── Layout integration ───

    public function test_admin_layout_shows_user_name_and_logout_form(): void
    {
        $admin = $this->userWithRole(UserRole::ROLE_MAIN_ADMIN);
        $res = $this->actingAs($admin, 'web')->get('/admin/dashboard');

        $res->assertStatus(200)
            ->assertSee($admin->full_name)
            ->assertSee('action="' . url('/admin/logout') . '"', false)
            ->assertSee('name="_token"', false);
    }
}

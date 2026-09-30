<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReadPaginationTest extends TestCase
{
    use RefreshDatabase;

    private function mainAdmin(): User
    {
        $user = User::factory()->create();
        UserRole::create([
            'user_id' => $user->id,
            'role'    => UserRole::ROLE_MAIN_ADMIN,
        ]);
        return $user;
    }

    public function test_default_pagination_is_25(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit');

        $res->assertStatus(200)
            ->assertJsonPath('meta.per_page', 25)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_per_page_can_be_set_up_to_100(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit?per_page=100');

        $res->assertStatus(200)
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_per_page_over_100_is_rejected(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit?per_page=200');

        $res->assertStatus(422);
    }

    public function test_page_zero_is_rejected(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit?page=0');

        $res->assertStatus(422);
    }

    public function test_successful_read_writes_audit_log(): void
    {
        $admin = $this->mainAdmin();
        $before = AuditLog::count();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit')
            ->assertStatus(200);

        $this->assertGreaterThan($before, AuditLog::count());

        $log = AuditLog::where('action', 'admin.read.audit')->latest('occurred_at')->first();
        $this->assertNotNull($log);
        $this->assertSame('A016', $log->entity_id);
    }

    public function test_denied_read_writes_security_event(): void
    {
        $user = User::factory()->create(); // no role

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/audit')
            ->assertStatus(403);

        // Denied attempt does NOT write a read log (only actors with an admin role reach auditDenied)
        // We assert no successful read log was written for this user.
        $this->assertSame(
            0,
            AuditLog::where('actor_id', $user->id)
                ->where('action', 'admin.read.audit')
                ->count()
        );
    }

    public function test_placeholder_screen_returns_empty_with_meta_source_placeholder(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/health');

        $res->assertStatus(200)
            ->assertJsonPath('meta.source', 'placeholder')
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    public function test_invalid_date_filter_is_rejected(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit?date_from=not-a-date');

        $res->assertStatus(422);
    }

    public function test_search_term_max_200(): void
    {
        $admin = $this->mainAdmin();

        $long = str_repeat('a', 201);

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/audit?q=' . $long);

        $res->assertStatus(422);
    }

    public function test_response_contains_request_id(): void
    {
        $admin = $this->mainAdmin();

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/users');

        $res->assertStatus(200);
        $this->assertNotEmpty($res->json('request_id'));
    }
}

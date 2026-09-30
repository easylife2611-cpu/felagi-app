<?php

namespace Tests\Feature\Models;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WP-B28 / R-TEST-07 — UserRole model unit tests.
 * Schema: 2026_09_29_000001.
 * NOTE: Composite PK (user_id, role), no id, no incrementing, no timestamps.
 *       Avoid fresh()/find() — use where()->first().
 */
class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserRole(array $overrides = []): UserRole
    {
        $user = User::factory()->create();

        return UserRole::create(array_merge([
            'user_id'    => $user->id,
            'role'       => UserRole::ROLE_ADMIN,
            'granted_by' => null,
        ], $overrides));
    }

    public function test_no_incrementing(): void
    {
        $this->assertFalse((new UserRole())->getIncrementing());
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new UserRole())->usesTimestamps());
    }

    public function test_persists_core_attributes(): void
    {
        $ur = $this->makeUserRole(['role' => 'MODERATOR']);

        $this->assertDatabaseHas('user_roles', [
            'user_id' => $ur->user_id,
            'role'    => 'MODERATOR',
        ]);
    }

    public function test_belongs_to_user(): void
    {
        $ur = $this->makeUserRole();
        $this->assertInstanceOf(User::class, $ur->user);
        $this->assertSame($ur->user_id, $ur->user->id);
    }

    public function test_granted_by_nullable(): void
    {
        $ur = $this->makeUserRole(['granted_by' => null]);
        $this->assertNull($ur->granted_by);
        $this->assertNull($ur->grantedBy);
    }

    public function test_belongs_to_granted_by_user(): void
    {
        $granter = User::factory()->create();
        $user = User::factory()->create();
        $ur = UserRole::create([
            'user_id'    => $user->id,
            'role'       => UserRole::ROLE_ADMIN,
            'granted_by' => $granter->id,
        ]);

        $this->assertInstanceOf(User::class, $ur->grantedBy);
        $this->assertSame($granter->id, $ur->grantedBy->id);
    }

    public function test_granted_at_casts_to_datetime(): void
    {
        $ur = $this->makeUserRole();
        $fetched = UserRole::where('user_id', $ur->user_id)
            ->where('role', $ur->role)
            ->first();
        $this->assertInstanceOf(\Carbon\Carbon::class, $fetched->granted_at);
    }

    public function test_granted_at_auto_set_by_db(): void
    {
        $ur = $this->makeUserRole();
        $fetched = UserRole::where('user_id', $ur->user_id)
            ->where('role', $ur->role)
            ->first();
        $this->assertNotNull($fetched->granted_at);
    }

    public function test_revoked_at_nullable(): void
    {
        $ur = $this->makeUserRole(['revoked_at' => null]);
        $this->assertNull($ur->revoked_at);
    }

    public function test_revoked_at_casts_to_datetime(): void
    {
        $ur = $this->makeUserRole(['revoked_at' => now()]);
        $this->assertInstanceOf(\Carbon\Carbon::class, $ur->revoked_at);
    }

    public function test_role_constants(): void
    {
        $this->assertSame('MAIN_ADMIN', UserRole::ROLE_MAIN_ADMIN);
        $this->assertSame('ADMIN', UserRole::ROLE_ADMIN);
        $this->assertSame('MODERATOR', UserRole::ROLE_MODERATOR);
    }

    public function test_scope_active_filters_revoked(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();

        UserRole::create(['user_id' => $u1->id, 'role' => 'ADMIN']);
        UserRole::create(['user_id' => $u2->id, 'role' => 'ADMIN']);
        UserRole::create([
            'user_id'    => $u3->id,
            'role'       => 'ADMIN',
            'revoked_at' => now(),
        ]);

        $this->assertSame(2, UserRole::active()->count());
    }

    public function test_composite_pk_prevents_duplicate_user_role(): void
    {
        $ur = $this->makeUserRole(['role' => 'MODERATOR']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        UserRole::create([
            'user_id' => $ur->user_id,
            'role'    => 'MODERATOR',
        ]);
    }

    public function test_same_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();

        UserRole::create(['user_id' => $user->id, 'role' => 'ADMIN']);
        UserRole::create(['user_id' => $user->id, 'role' => 'MODERATOR']);

        $this->assertSame(2, UserRole::where('user_id', $user->id)->count());
    }

    public function test_user_roles_relation(): void
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => 'ADMIN']);

        $this->assertCount(1, $user->roles);
        $this->assertSame('ADMIN', $user->roles->first()->role);
    }

    public function test_user_scope_with_role_finds_admins(): void
    {
        $admin = User::factory()->create();
        $regular = User::factory()->create();

        UserRole::create(['user_id' => $admin->id, 'role' => 'ADMIN']);

        $found = User::withRole('ADMIN')->pluck('id')->all();
        $this->assertContains($admin->id, $found);
        $this->assertNotContains($regular->id, $found);
    }

    public function test_user_scope_with_role_excludes_revoked(): void
    {
        $revoked = User::factory()->create();
        UserRole::create([
            'user_id'    => $revoked->id,
            'role'       => 'ADMIN',
            'revoked_at' => now(),
        ]);

        $this->assertNotContains($revoked->id, User::withRole('ADMIN')->pluck('id')->all());
    }
}

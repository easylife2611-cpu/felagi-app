<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function adminUser(): User
    {
        $u = User::factory()->create();
        UserRole::create([
            'user_id' => $u->id,
            'role'    => 'MAIN_ADMIN',
        ]);

        // WP-13b: fresh reauth within 5-minute window
        // (publish route requires 'reauth' middleware)
        $u->recently_authenticated_at = now();
        $u->save();

        return $u;
    }

    private function nonAdminUser(): User
    {
        return User::factory()->create();
    }

    /** T01 — create draft */
    public function test_can_create_draft(): void
    {
        $admin = $this->adminUser();
        $res = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 50,
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'DRAFT');
    }

    /** T02 — validate rejects type mismatch */
    public function test_validate_rejects_type_mismatch(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 'not-an-int',
        ])->json('data.id');

        $res = $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/validate");

        $res->assertStatus(200);
        $this->assertNotEmpty($res->json('data.errors'));
    }

    /** T03 — publish creates new version */
    public function test_publish_creates_new_version(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 75,
        ])->json('data.id');

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/validate");

        $res = $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/publish", [
                'reason'           => 'Launch cap increase',
                'expected_version' => 1,
            ]);

        $res->assertStatus(200);
        $this->assertDatabaseHas('setting_versions', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'version_number' => 2,
        ]);
        $this->assertEquals(2, Setting::find('marketplace.max_open_needs_per_user')->version_number);
    }

    /** T04 — stale version → 409 */
    public function test_publish_with_stale_version_returns_409(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 100,
        ])->json('data.id');

        $res = $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/publish", [
                'reason'           => 'Stale attempt',
                'expected_version' => 999,
            ]);

        $res->assertStatus(409)
            ->assertJsonPath('error.code', 'SETTINGS_VERSION_CONFLICT');
    }

    /** T05 — dependency blocks boosts */
    public function test_dependency_blocks_boosts_on(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'feature.boosts',
            'proposed_value' => true,
        ])->json('data.id');

        $res = $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/validate");

        $res->assertStatus(200);
        $this->assertNotEmpty($res->json('data.errors'));
    }

    /** T06 — audit log written */
    public function test_publish_writes_audit_log(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 200,
        ])->json('data.id');

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/publish", [
                'reason'           => 'Audit test',
                'expected_version' => 1,
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'      => 'setting.publish',
            'entity_type' => 'setting',
        ]);
    }

    /** T07 — outbox event written */
    public function test_publish_writes_outbox_event(): void
    {
        $admin = $this->adminUser();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 300,
        ])->json('data.id');

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/changes/{$draftId}/publish", [
                'reason'           => 'Outbox test',
                'expected_version' => 1,
            ]);

        $this->assertDatabaseHas('outbox_events', [
            'event_type'     => 'setting.published',
            'aggregate_type' => 'setting_version',
            'status'         => 'PENDING',
        ]);
    }

    /** T08 — unauthorized → 403 */
    public function test_unauthorized_returns_403(): void
    {
        $user = $this->nonAdminUser();
        $res = $this->actingAs($user)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 50,
        ]);
        $res->assertStatus(403);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AE — Change diff view.
 */
class ChangeDiffTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => 'MAIN_ADMIN']);
        $u->recently_authenticated_at = now();
        $u->save();
        return $u;
    }

    private function makeDraft(User $admin, string $key, mixed $value): string
    {
        return $this->actingAs($admin)
            ->postJson('/api/v1/admin/changes', [
                'setting_key'    => $key,
                'proposed_value' => $value,
            ])
            ->json('data.id');
    }

    public function test_diff_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/changes/00000000-0000-0000-0000-000000000000/diff')
            ->assertStatus(401);
    }

    public function test_diff_404_for_unknown_draft(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)
            ->getJson('/api/v1/admin/changes/00000000-0000-0000-0000-000000000000/diff')
            ->assertStatus(404);
    }

    public function test_diff_403_for_non_admin(): void
    {
        $admin = $this->admin();
        $draftId = $this->makeDraft($admin, 'marketplace.max_open_needs_per_user', 100);

        $stranger = User::factory()->create();
        $this->actingAs($stranger)
            ->getJson("/api/v1/admin/changes/{$draftId}/diff")
            ->assertStatus(403);
    }

    public function test_diff_returns_unchanged_when_same_value(): void
    {
        $admin = $this->admin();
        $current = Setting::find('marketplace.max_open_needs_per_user')
            ->value_json['value'] ?? null;
        if ($current === null) {
            $this->markTestSkipped('Setting has no current value');
        }

        $draftId = $this->makeDraft($admin, 'marketplace.max_open_needs_per_user', $current);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/changes/{$draftId}/diff");

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.changed', false)
            ->assertJsonPath('data.counts.total', 0);
    }

    public function test_diff_detects_scalar_modification(): void
    {
        $admin = $this->admin();
        $draftId = $this->makeDraft($admin, 'marketplace.max_open_needs_per_user', 12345);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/changes/{$draftId}/diff");

        $res->assertStatus(200)
            ->assertJsonPath('data.changed', true)
            ->assertJsonPath('data.counts.MODIFIED', 1)
            ->assertJsonPath('data.rows.0.path', 'value')
            ->assertJsonPath('data.rows.0.change', 'MODIFIED')
            ->assertJsonPath('data.rows.0.after', 12345);
    }

    public function test_diff_includes_metadata(): void
    {
        $admin = $this->admin();
        $draftId = $this->makeDraft($admin, 'marketplace.max_open_needs_per_user', 999);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/changes/{$draftId}/diff");

        $res->assertStatus(200)
            ->assertJsonPath('data.setting_key', 'marketplace.max_open_needs_per_user')
            ->assertJsonStructure([
                'data' => [
                    'draft_id', 'setting_key', 'setting_group', 'setting_type',
                    'setting_risk', 'is_secret', 'requires_reauth',
                    'requires_second_factor', 'draft_status', 'current_version',
                    'before_summary', 'after_summary', 'rows', 'counts',
                    'changed', 'generated_at',
                ],
            ]);
    }

    public function test_diff_returns_added_for_new_value(): void
    {
        $admin = $this->admin();
        $draftId = $this->makeDraft($admin, 'marketplace.max_open_needs_per_user', 77777);

        $res = $this->actingAs($admin)
            ->getJson("/api/v1/admin/changes/{$draftId}/diff");

        $res->assertStatus(200);
        $rows = $res->json('data.rows');
        $this->assertNotEmpty($rows);
        $this->assertContains($rows[0]['change'], ['ADDED', 'MODIFIED', 'REMOVED']);
    }
}

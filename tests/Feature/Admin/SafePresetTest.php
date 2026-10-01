<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\SettingPreset;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\ControlRegistrySeeder;
use Database\Seeders\PresetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AH (audit L276) — Safe presets / operation modes.
 */
class SafePresetTest extends TestCase
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

    private function seedPresets(User $admin): void
    {
        $this->seed(PresetSeeder::class);
    }

    public function test_index_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/presets')->assertStatus(401);
    }

    public function test_seeder_creates_two_presets(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        $this->assertSame(2, SettingPreset::count());
        $this->assertNotNull(SettingPreset::where('name', 'safe-defaults')->first());
        $this->assertNotNull(SettingPreset::where('name', 'maintenance')->first());
    }

    public function test_index_returns_presets_for_admin(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        $res = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/presets');
        $res->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_preview_returns_applicable_keys(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/presets/safe-defaults/preview');

        $res->assertStatus(200)
            ->assertJsonPath('data.preset', 'safe-defaults')
            ->assertJsonStructure([
                'data' => ['applicable_keys', 'missing_keys', 'selection_digest', 'selection_ids'],
            ]);
    }

    public function test_preview_404_for_unknown_preset(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/presets/does-not-exist/preview')
            ->assertStatus(404);
    }

    public function test_apply_requires_auth(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);
        $this->postJson('/api/v1/admin/presets/maintenance/apply')->assertStatus(401);
    }

    public function test_apply_changes_values(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        // baseline
        $before = Setting::find('system.safe_mode')->value_json['value'];
        $this->assertFalse($before);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/presets/maintenance/apply');

        $res->assertStatus(201)
            ->assertJsonPath('data.action_type', 'preset.apply')
            ->assertJsonPath('data.status', 'EXECUTED');

        $after = Setting::find('system.safe_mode')->value_json['value'];
        $this->assertTrue($after);
    }

    public function test_apply_returns_bulk_action_with_items(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/presets/safe-defaults/apply');

        $res->assertStatus(201);
        $items = $res->json('data.items');
        $this->assertIsArray($items);
        $this->assertNotEmpty($items);
        foreach ($items as $it) {
            $this->assertArrayHasKey('idempotency_key', $it);
        }
    }

    public function test_apply_is_idempotent(): void
    {
        $admin = $this->admin();
        $this->seedPresets($admin);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/presets/maintenance/apply')
            ->assertStatus(201);

        // second call — value already true, per-item idempotent success
        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/presets/maintenance/apply')
            ->assertStatus(201);
    }
}

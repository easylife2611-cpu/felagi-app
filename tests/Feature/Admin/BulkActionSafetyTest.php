<?php

namespace Tests\Feature\Admin;

use App\Models\BulkAction;
use App\Models\Setting;
use App\Models\SettingVersion;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\BulkActionService;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AM (audit L276) — Bulk action safety.
 */
class BulkActionSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function admin(string $role = 'MAIN_ADMIN'): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => $role]);
        $u->recently_authenticated_at = now();
        $u->save();
        return $u;
    }

    private function boolKeys(int $count): array
    {
        return Setting::where('type', Setting::TYPE_BOOLEAN)
            ->orderBy('key')
            ->limit($count)
            ->pluck('key')
            ->all();
    }

    // ─── Preview ───

    public function test_preview_requires_auth(): void
    {
        $this->postJson('/api/v1/admin/bulk/preview', [])->assertStatus(401);
    }

    public function test_preview_returns_digest_for_admin(): void
    {
        $admin = $this->admin();
        $keys = $this->boolKeys(3);

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type'   => 'setting.disable',
            'entity_type'   => 'setting',
            'scope'         => 'features',
            'selection_ids' => $keys,
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('data.selection_count', 3)
            ->assertJsonPath('data.authorized_count', 3)
            ->assertJsonStructure([
                'data' => ['selection_digest', 'selection_ids', 'selection_count',
                           'authorized_count', 'unauthorized_count', 'max_batch'],
            ]);
        $this->assertSame(64, strlen($res->json('data.selection_digest')));
    }

    public function test_preview_rejects_empty_selection(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type'   => 'setting.disable',
            'entity_type'   => 'setting',
            'scope'         => 'features',
            'selection_ids' => [],
        ]);
        // Laravel validation: min:1 → 422
        $res->assertStatus(422);
    }

    public function test_preview_rejects_unknown_action(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type'   => 'setting.explode',
            'entity_type'   => 'setting',
            'scope'         => 'features',
            'selection_ids' => ['feature.needs'],
        ]);
        $res->assertStatus(422);
    }

    public function test_preview_is_order_insensitive(): void
    {
        $admin = $this->admin();
        $keys = $this->boolKeys(3);

        $a = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $keys,
        ])->json('data.selection_digest');

        $b = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => array_reverse($keys),
        ])->json('data.selection_digest');

        $this->assertSame($a, $b, 'Digest must be stable regardless of order');
    }

    // ─── Execute ───

    public function test_execute_requires_matching_digest(): void
    {
        $admin = $this->admin();
        $keys = $this->boolKeys(2);

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => 'setting.disable',
            'entity_type'      => 'setting',
            'scope'            => 'features',
            'selection_ids'    => $keys,
            'selection_digest' => str_repeat('0', 64), // wrong
        ]);

        $res->assertStatus(409)
            ->assertJsonPath('error.code', 'SELECTION_DIGEST_MISMATCH');
    }

    public function test_execute_disables_settings(): void
    {
        $admin = $this->admin();
        // Pick boolean settings that are ON by default
        $keys = Setting::where('type', Setting::TYPE_BOOLEAN)
            ->where('value_json->value', true)
            ->orderBy('key')->limit(2)->pluck('key')->all();
        $this->assertNotEmpty($keys, 'Seeder must provide ON booleans');

        $preview = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $keys,
        ])->json('data');

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => $preview['action_type'],
            'entity_type'      => $preview['entity_type'],
            'scope'            => $preview['scope'],
            'selection_ids'    => $preview['selection_ids'],
            'selection_digest' => $preview['selection_digest'],
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.status', BulkAction::STATUS_EXECUTED);

        foreach ($keys as $key) {
            $this->assertFalse(
                Setting::find($key)->value_json['value'],
                "Setting {$key} should be disabled"
            );
        }

        // BulkAction persisted with items
        $bulk = BulkAction::first();
        $this->assertNotNull($bulk);
        $this->assertCount(count($keys), $bulk->items);
        foreach ($bulk->items as $item) {
            $this->assertSame(BulkAction::ITEM_SUCCEEDED, $item['status']);
            $this->assertStringStartsWith('bulk:', $item['idempotency_key']);
        }
    }

    public function test_execute_reports_unknown_for_unauthorized_item(): void
    {
        // Create a MODERATOR — not allowed to execute bulk
        $mod = $this->admin('MODERATOR');
        $keys = $this->boolKeys(2);

        $preview = $this->actingAs($mod, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $keys,
        ])->json('data');

        $res = $this->actingAs($mod, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => 'setting.disable',
            'entity_type'      => 'setting',
            'scope'            => 'features',
            'selection_ids'    => $preview['selection_ids'],
            'selection_digest' => $preview['selection_digest'],
        ]);

        // MODERATOR has no bulk permission → all items UNKNOWN → status FAILED
        $res->assertStatus(201);
        $bulk = BulkAction::first();
        $this->assertNotNull($bulk);
        foreach ($bulk->items as $item) {
            $this->assertSame(BulkAction::ITEM_UNKNOWN, $item['status']);
        }
        $this->assertSame(BulkAction::STATUS_FAILED, $bulk->status);
    }

    public function test_execute_reports_failed_for_missing_setting(): void
    {
        $admin = $this->admin();
        // Add a key that doesn't exist (but is a string) — will fail per-item
        $ids = ['feature.needs', 'nonexistent.setting.zzz'];

        $preview = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $ids,
        ])->json('data');

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => 'setting.disable',
            'entity_type'      => 'setting',
            'scope'            => 'features',
            'selection_ids'    => $preview['selection_ids'],
            'selection_digest' => $preview['selection_digest'],
        ]);

        $res->assertStatus(201);
        $bulk = BulkAction::first();
        $this->assertSame(BulkAction::STATUS_PARTIAL, $bulk->status);

        $byId = collect($bulk->items)->keyBy('entity_id');
        $this->assertSame(BulkAction::ITEM_SUCCEEDED, $byId['feature.needs']['status']);
        $this->assertSame(BulkAction::ITEM_FAILED,    $byId['nonexistent.setting.zzz']['status']);
    }

    // ─── Retry ───

    public function test_retry_failed_reuses_only_failed_items(): void
    {
        $admin = $this->admin();
        $ids = ['feature.needs', 'nonexistent.setting.zzz'];

        $preview = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $ids,
        ])->json('data');

        $original = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => 'setting.disable',
            'entity_type'      => 'setting',
            'scope'            => 'features',
            'selection_ids'    => $preview['selection_ids'],
            'selection_digest' => $preview['selection_digest'],
        ])->json('data');

        // Now make the missing setting exist, so the retry succeeds
        Setting::create([
            'key'            => 'nonexistent.setting.zzz',
            'group'          => 'FEATURE',
            'type'           => 'BOOLEAN',
            'value_json'     => ['value' => true],
            'default_json'   => ['value' => true],
            'risk'           => 'LOW',
            'is_secret'      => false,
            'version_number' => 1,
        ]);

        $retryRes = $this->actingAs($admin, 'sanctum')->postJson(
            "/api/v1/admin/bulk/{$original['id']}/retry-failed",
        );

        $retryRes->assertStatus(201)
            ->assertJsonPath('data.action_type', 'setting.disable');

        $retry = BulkAction::where('retry_of_id', $original['id'])->first();
        $this->assertNotNull($retry);
        $this->assertCount(1, $retry->selection_ids);
        $this->assertSame('nonexistent.setting.zzz', $retry->selection_ids[0]);
        $this->assertSame(BulkAction::STATUS_EXECUTED, $retry->status);
    }

    public function test_retry_rejects_when_no_failures(): void
    {
        $admin = $this->admin();
        $keys = Setting::where('type', Setting::TYPE_BOOLEAN)
            ->where('value_json->value', true)
            ->orderBy('key')->limit(1)->pluck('key')->all();

        $preview = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $keys,
        ])->json('data');

        $original = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/execute', [
            'action_type'      => 'setting.disable',
            'entity_type'      => 'setting',
            'scope'            => 'features',
            'selection_ids'    => $preview['selection_ids'],
            'selection_digest' => $preview['selection_digest'],
        ])->json('data');

        $res = $this->actingAs($admin, 'sanctum')->postJson(
            "/api/v1/admin/bulk/{$original['id']}/retry-failed",
        );
        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'BULK_RETRY_FAILED');
    }

    // ─── Max batch ───

    public function test_preview_enforces_max_batch(): void
    {
        $admin = $this->admin();
        $ids = array_map(fn ($i) => "fake.setting.{$i}", range(1, BulkActionService::MAX_BATCH + 1));

        $res = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/bulk/preview', [
            'action_type' => 'setting.disable', 'entity_type' => 'setting',
            'scope' => 'features', 'selection_ids' => $ids,
        ]);
        $res->assertStatus(422);
    }
}

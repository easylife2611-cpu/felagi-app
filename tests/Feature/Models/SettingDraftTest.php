<?php

namespace Tests\Feature\Models;

use App\Models\Setting;
use App\Models\SettingDraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WP-B28 / R-TEST-06 — SettingDraft model unit tests.
 * Schema: 2026_09_29_000022.
 * NOTE: HasUuids, standard timestamps, FK setting_key -> settings.key.
 */
class SettingDraftTest extends TestCase
{
    use RefreshDatabase;

    private function makeSetting(): Setting
    {
        return Setting::create([
            'key'            => 'draft.base.' . Str::lower(Str::random(8)),
            'group'          => 'FEATURE',
            'type'           => 'BOOLEAN',
            'value_json'     => false,
            'default_json'   => false,
            'risk'           => 'LOW',
            'is_secret'      => false,
            'version_number' => 1,
        ]);
    }

    private function makeDraft(array $overrides = []): SettingDraft
    {
        $setting = $this->makeSetting();
        $user = User::factory()->create();

        return SettingDraft::create(array_merge([
            'setting_key'         => $setting->key,
            'proposed_value_json' => ['enabled' => true],
            'proposed_by'         => $user->id,
            'status'              => SettingDraft::STATUS_DRAFT,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $d = $this->makeDraft();
        $this->assertNotNull($d->id);
        $this->assertTrue(Str::isUuid($d->id));
    }

    public function test_uses_timestamps(): void
    {
        $this->assertTrue((new SettingDraft())->usesTimestamps());
    }

    public function test_persists_core_attributes(): void
    {
        $d = $this->makeDraft(['status' => 'VALIDATED']);

        $this->assertDatabaseHas('setting_drafts', [
            'id'     => $d->id,
            'status' => 'VALIDATED',
        ]);
    }

    public function test_belongs_to_setting(): void
    {
        $setting = $this->makeSetting();
        $user = User::factory()->create();
        $d = SettingDraft::create([
            'setting_key'         => $setting->key,
            'proposed_value_json' => ['x' => 1],
            'proposed_by'         => $user->id,
        ]);

        $this->assertInstanceOf(Setting::class, $d->setting);
        $this->assertSame($setting->key, $d->setting->key);
    }

    public function test_belongs_to_proposer(): void
    {
        $d = $this->makeDraft();
        $this->assertInstanceOf(User::class, $d->proposer);
        $this->assertSame($d->proposed_by, $d->proposer->id);
    }

    public function test_proposed_value_json_casts_to_array(): void
    {
        $d = $this->makeDraft([
            'proposed_value_json' => ['a' => 1, 'b' => ['c' => 2]],
        ]);
        $fresh = $d->fresh();

        $this->assertIsArray($fresh->proposed_value_json);
        $this->assertSame(1, $fresh->proposed_value_json['a']);
        $this->assertSame(2, $fresh->proposed_value_json['b']['c']);
    }

    public function test_validation_report_casts_to_array(): void
    {
        $d = $this->makeDraft([
            'validation_report' => ['errors' => [], 'warnings' => ['w1']],
        ]);
        $fresh = $d->fresh();

        $this->assertIsArray($fresh->validation_report);
        $this->assertSame(['w1'], $fresh->validation_report['warnings']);
    }

    public function test_validation_report_nullable(): void
    {
        $d = $this->makeDraft(['validation_report' => null]);
        $this->assertNull($d->fresh()->validation_report);
    }

    public function test_impact_preview_casts_to_array(): void
    {
        $d = $this->makeDraft([
            'impact_preview' => ['affected_users' => 100, 'duration_hours' => 2],
        ]);
        $fresh = $d->fresh();

        $this->assertIsArray($fresh->impact_preview);
        $this->assertSame(100, $fresh->impact_preview['affected_users']);
    }

    public function test_impact_preview_nullable(): void
    {
        $d = $this->makeDraft(['impact_preview' => null]);
        $this->assertNull($d->fresh()->impact_preview);
    }

    public function test_created_at_casts_to_datetime(): void
    {
        $d = $this->makeDraft();
        $this->assertInstanceOf(\Carbon\Carbon::class, $d->fresh()->created_at);
    }

    public function test_updated_at_casts_to_datetime(): void
    {
        $d = $this->makeDraft();
        $this->assertInstanceOf(\Carbon\Carbon::class, $d->fresh()->updated_at);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('DRAFT', SettingDraft::STATUS_DRAFT);
        $this->assertSame('VALIDATED', SettingDraft::STATUS_VALIDATED);
        $this->assertSame('REJECTED', SettingDraft::STATUS_REJECTED);
        $this->assertSame('PUBLISHED', SettingDraft::STATUS_PUBLISHED);
    }

    public function test_default_status_is_draft(): void
    {
        $setting = $this->makeSetting();
        $user = User::factory()->create();
        $d = SettingDraft::create([
            'setting_key'         => $setting->key,
            'proposed_value_json' => ['x' => 1],
            'proposed_by'         => $user->id,
        ]);
        $this->assertSame('DRAFT', $d->fresh()->status);
    }

    public function test_is_editable_true_for_draft(): void
    {
        $d = $this->makeDraft(['status' => 'DRAFT']);
        $this->assertTrue($d->isEditable());
    }

    public function test_is_editable_true_for_validated(): void
    {
        $d = $this->makeDraft(['status' => 'VALIDATED']);
        $this->assertTrue($d->isEditable());
    }

    public function test_is_editable_false_for_rejected(): void
    {
        $d = $this->makeDraft(['status' => 'REJECTED']);
        $this->assertFalse($d->isEditable());
    }

    public function test_is_editable_false_for_published(): void
    {
        $d = $this->makeDraft(['status' => 'PUBLISHED']);
        $this->assertFalse($d->isEditable());
    }

    public function test_fk_setting_key_must_exist(): void
    {
        $user = User::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        SettingDraft::create([
            'setting_key'         => 'nonexistent.setting.' . Str::random(8),
            'proposed_value_json' => ['x' => 1],
            'proposed_by'         => $user->id,
        ]);
    }

    public function test_multiple_drafts_for_same_setting_allowed(): void
    {
        $setting = $this->makeSetting();
        $user = User::factory()->create();

        SettingDraft::create([
            'setting_key'         => $setting->key,
            'proposed_value_json' => ['v' => 1],
            'proposed_by'         => $user->id,
        ]);
        SettingDraft::create([
            'setting_key'         => $setting->key,
            'proposed_value_json' => ['v' => 2],
            'proposed_by'         => $user->id,
        ]);

        $this->assertSame(2, SettingDraft::where('setting_key', $setting->key)->count());
    }
}

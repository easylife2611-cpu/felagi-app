<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\SettingPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class SettingPresetTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $p = SettingPreset::factory()->create();
        $this->assertTrue(Str::isUuid($p->id));
    }

    public function test_status_constants_match(): void
    {
        $this->assertSame('ACTIVE', SettingPreset::STATUS_ACTIVE);
        $this->assertSame('ARCHIVED', SettingPreset::STATUS_ARCHIVED);
    }

    public function test_default_status_is_active(): void
    {
        $p = SettingPreset::factory()->create();
        $this->assertSame(SettingPreset::STATUS_ACTIVE, $p->fresh()->status);
    }

    public function test_archived_state(): void
    {
        $p = SettingPreset::factory()->archived()->create();
        $this->assertSame(SettingPreset::STATUS_ARCHIVED, $p->fresh()->status);
    }

    public function test_values_json_casts_to_array(): void
    {
        $p = SettingPreset::factory()->withValues([
            'feature.payments' => true,
            'system.safe_mode' => false,
        ])->create();

        $fresh = $p->fresh();
        $this->assertIsArray($fresh->values_json);
        $this->assertTrue($fresh->values_json['feature.payments']);
        $this->assertFalse($fresh->values_json['system.safe_mode']);
    }

    public function test_belongs_to_creator(): void
    {
        $u = User::factory()->create();
        $p = SettingPreset::factory()->create(['created_by' => $u->id]);
        $this->assertSame($u->id, $p->fresh()->creator->id);
    }

    public function test_name_is_unique(): void
    {
        SettingPreset::factory()->withName('unique-name')->create();

        $this->expectException(\Illuminate\Database\QueryException::class);
        SettingPreset::factory()->withName('unique-name')->create();
    }

    public function test_with_name_state(): void
    {
        $p = SettingPreset::factory()->withName('my-preset')->create();
        $this->assertSame('my-preset', $p->fresh()->name);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $p = new SettingPreset();
        foreach ([
            'name', 'display_name', 'description',
            'values_json', 'status', 'created_by',
        ] as $f) {
            $this->assertContains($f, $p->getFillable());
        }
    }

    public function test_description_is_nullable(): void
    {
        $p = SettingPreset::factory()->create(['description' => null]);
        $this->assertNull($p->fresh()->description);
    }

    public function test_values_json_can_be_empty(): void
    {
        $p = SettingPreset::factory()->withValues([])->create();
        $this->assertSame([], $p->fresh()->values_json);
    }

    public function test_default_values_contain_safe_defaults(): void
    {
        $p = SettingPreset::factory()->create();
        $values = $p->fresh()->values_json;
        $this->assertArrayHasKey('feature.payments', $values);
        $this->assertArrayHasKey('feature.boosts', $values);
        $this->assertFalse($values['feature.payments']);
        $this->assertFalse($values['feature.boosts']);
    }

    public function test_created_at_and_updated_at_present(): void
    {
        $p = SettingPreset::factory()->create();
        $this->assertNotNull($p->created_at);
        $this->assertNotNull($p->updated_at);
    }
}

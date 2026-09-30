<?php

namespace Tests\Feature\Models;

use App\Models\Setting;
use App\Models\SettingDraft;
use App\Models\SettingVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WP-B28 / R-TEST-06 — Setting model unit tests.
 * Schema: 2026_09_29_000020.
 * NOTE: PK = key (string), no id, no incrementing.
 *       No created_at — only updated_at (DB auto via useCurrentOnUpdate).
 */
class SettingTest extends TestCase
{
    use RefreshDatabase;

    private function makeSetting(array $overrides = []): Setting
    {
        return Setting::create(array_merge([
            'key'            => 'test.setting.' . Str::lower(Str::random(8)),
            'group'          => Setting::GROUP_FEATURE,
            'type'           => Setting::TYPE_BOOLEAN,
            'value_json'     => true,
            'default_json'   => false,
            'risk'           => Setting::RISK_LOW,
            'is_secret'      => false,
            'version_number' => 1,
        ], $overrides));
    }

    public function test_primary_key_is_key_string(): void
    {
        $s = new Setting();
        $this->assertSame('key', $s->getKeyName());
        $this->assertSame('string', $s->getKeyType());
    }

    public function test_no_incrementing(): void
    {
        $this->assertFalse((new Setting())->getIncrementing());
    }

    public function test_no_timestamps_auto(): void
    {
        $this->assertFalse((new Setting())->usesTimestamps());
    }

    public function test_persists_core_attributes(): void
    {
        $s = $this->makeSetting([
            'key'       => 'feature.dark_mode',
            'group'     => 'FEATURE',
            'type'      => 'BOOLEAN',
            'value_json'=> true,
            'risk'      => 'LOW',
        ]);

        $this->assertDatabaseHas('settings', [
            'key'   => 'feature.dark_mode',
            'group' => 'FEATURE',
            'type'  => 'BOOLEAN',
            'risk'  => 'LOW',
        ]);
    }

    public function test_key_is_primary_key(): void
    {
        $s = $this->makeSetting();
        $this->assertSame($s->key, Setting::find($s->key)->key);
    }

    public function test_value_json_casts_to_array(): void
    {
        $s = $this->makeSetting([
            'value_json' => ['a' => 1, 'b' => ['c' => 2]],
        ]);
        $fresh = $s->fresh();

        $this->assertIsArray($fresh->value_json);
        $this->assertSame(1, $fresh->value_json['a']);
        $this->assertSame(2, $fresh->value_json['b']['c']);
    }

    public function test_default_json_casts_to_array(): void
    {
        $s = $this->makeSetting(['default_json' => ['x' => true]]);
        $this->assertSame(['x' => true], $s->fresh()->default_json);
    }

    public function test_schema_json_casts_to_array(): void
    {
        $s = $this->makeSetting([
            'schema_json' => ['type' => 'object', 'properties' => []],
        ]);
        $this->assertSame('object', $s->fresh()->schema_json['type']);
    }

    public function test_is_secret_casts_to_boolean(): void
    {
        $s1 = $this->makeSetting(['is_secret' => true]);
        $this->assertTrue($s1->fresh()->is_secret);

        $s2 = $this->makeSetting(['is_secret' => false]);
        $this->assertFalse($s2->fresh()->is_secret);
    }

    public function test_default_is_secret_is_false(): void
    {
        $s = Setting::create([
            'key'   => 'default.secret.' . Str::random(6),
            'group' => 'CONFIG',
            'type'  => 'STRING',
            'risk'  => 'LOW',
        ]);
        $this->assertFalse($s->fresh()->is_secret);
    }

    public function test_version_number_defaults_to_one(): void
    {
        $s = Setting::create([
            'key'   => 'default.version.' . Str::random(6),
            'group' => 'CONFIG',
            'type'  => 'STRING',
            'risk'  => 'LOW',
        ]);
        $this->assertSame(1, $s->fresh()->version_number);
    }

    public function test_updated_at_cast_datetime(): void
    {
        $s = $this->makeSetting();
        $this->assertInstanceOf(\Carbon\Carbon::class, $s->fresh()->updated_at);
    }

    public function test_updated_by_nullable(): void
    {
        $s = $this->makeSetting(['updated_by' => null]);
        $this->assertNull($s->fresh()->updated_by);
    }

    public function test_belongs_to_updated_by_user(): void
    {
        $user = User::factory()->create();
        $s = $this->makeSetting(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $s->fresh()->updatedBy);
        $this->assertSame($user->id, $s->fresh()->updatedBy->id);
    }

    public function test_has_many_versions(): void
    {
        $s = $this->makeSetting();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $s->versions
        );
        $this->assertCount(0, $s->versions);
    }

    public function test_has_many_drafts(): void
    {
        $s = $this->makeSetting();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $s->drafts
        );
        $this->assertCount(0, $s->drafts);
    }

    public function test_group_constants(): void
    {
        $this->assertSame('FEATURE', Setting::GROUP_FEATURE);
        $this->assertSame('CONFIG', Setting::GROUP_CONFIG);
        $this->assertSame('CONTENT', Setting::GROUP_CONTENT);
        $this->assertSame('SECURITY', Setting::GROUP_SECURITY);
    }

    public function test_risk_constants(): void
    {
        $this->assertSame('LOW', Setting::RISK_LOW);
        $this->assertSame('MEDIUM', Setting::RISK_MEDIUM);
        $this->assertSame('HIGH', Setting::RISK_HIGH);
        $this->assertSame('CRITICAL', Setting::RISK_CRITICAL);
    }

    public function test_type_constants(): void
    {
        $this->assertSame('BOOLEAN', Setting::TYPE_BOOLEAN);
        $this->assertSame('INTEGER', Setting::TYPE_INTEGER);
        $this->assertSame('DECIMAL', Setting::TYPE_DECIMAL);
        $this->assertSame('STRING', Setting::TYPE_STRING);
        $this->assertSame('JSON', Setting::TYPE_JSON);
    }

    public function test_requires_reauth_true_for_high(): void
    {
        $s = $this->makeSetting(['risk' => 'HIGH']);
        $this->assertTrue($s->requiresReauth());
    }

    public function test_requires_reauth_true_for_critical(): void
    {
        $s = $this->makeSetting(['risk' => 'CRITICAL']);
        $this->assertTrue($s->requiresReauth());
    }

    public function test_requires_reauth_false_for_low(): void
    {
        $s = $this->makeSetting(['risk' => 'LOW']);
        $this->assertFalse($s->requiresReauth());
    }

    public function test_requires_reauth_false_for_medium(): void
    {
        $s = $this->makeSetting(['risk' => 'MEDIUM']);
        $this->assertFalse($s->requiresReauth());
    }

    public function test_requires_second_factor_true_for_critical(): void
    {
        $s = $this->makeSetting(['risk' => 'CRITICAL']);
        $this->assertTrue($s->requiresSecondFactor());
    }

    public function test_requires_second_factor_false_for_high(): void
    {
        $s = $this->makeSetting(['risk' => 'HIGH']);
        $this->assertFalse($s->requiresSecondFactor());
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Setting;
use App\Models\SettingVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SettingVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uuid_auto_generated_on_create(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key',
            'group' => 'FEATURE',
            'type' => 'BOOLEAN',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => true,
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => false,
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test',
        ]);

        $this->assertNotNull($version->id);
        $this->assertTrue(Str::isUuid($version->id));
    }

    public function test_uses_no_timestamps(): void
    {
        $version = new SettingVersion();
        $this->assertFalse($version->usesTimestamps());
    }

    public function test_value_json_casts_to_array(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key2',
            'group' => 'CONFIG',
            'type' => 'STRING',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => 'old',
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => ['k' => 'v', 'n' => 42],
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test',
        ]);

        $version->refresh();
        $this->assertIsArray($version->value_json);
        $this->assertSame('v', $version->value_json['k']);
    }

    public function test_version_number_casts_to_integer(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key3',
            'group' => 'CONFIG',
            'type' => 'INTEGER',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => 1,
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 5,
            'value_json' => 10,
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test',
        ]);

        $version->refresh();
        $this->assertIsInt($version->version_number);
        $this->assertSame(5, $version->version_number);
    }

    public function test_published_at_casts_to_datetime(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key4',
            'group' => 'CONTENT',
            'type' => 'STRING',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => 'x',
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => 'y',
            'published_by' => $user->id,
            'published_at' => '2026-09-29 10:00:00',
            'reason' => 'Test',
        ]);

        $version->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $version->published_at);
    }

    public function test_setting_relationship(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key5',
            'group' => 'FEATURE',
            'type' => 'BOOLEAN',
            'risk' => 'HIGH',
            'is_secret' => false,
            'value_json' => true,
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => false,
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test',
        ]);

        $this->assertInstanceOf(Setting::class, $version->setting);
        $this->assertSame($setting->key, $version->setting->key);
    }

    public function test_publisher_relationship(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key6',
            'group' => 'CONFIG',
            'type' => 'STRING',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => 'a',
            'version_number' => 1,
        ]);

        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => 'b',
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test',
        ]);

        $this->assertInstanceOf(User::class, $version->publisher);
        $this->assertSame($user->id, $version->publisher->id);
    }

    public function test_reason_and_source_draft_id_fillable(): void
    {
        $user = User::factory()->create();
        $setting = Setting::create([
            'key' => 'test.key7',
            'group' => 'CONFIG',
            'type' => 'STRING',
            'risk' => 'LOW',
            'is_secret' => false,
            'value_json' => 'x',
            'version_number' => 1,
        ]);

        $draftId = (string) Str::uuid();
        $version = SettingVersion::create([
            'setting_key' => $setting->key,
            'version_number' => 2,
            'value_json' => 'y',
            'published_by' => $user->id,
            'published_at' => now(),
            'reason' => 'Test reason',
            'source_draft_id' => $draftId,
        ]);

        $version->refresh();
        $this->assertSame('Test reason', $version->reason);
        $this->assertSame($draftId, $version->source_draft_id);
    }
}

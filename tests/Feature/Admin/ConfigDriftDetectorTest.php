<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\SettingVersion;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\ConfigDriftDetector;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AC (audit L276) — Config drift detector.
 */
class ConfigDriftDetectorTest extends TestCase
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

    private function publisher(): User
    {
        return User::factory()->create();
    }

    private function detector(): ConfigDriftDetector
    {
        return app(ConfigDriftDetector::class);
    }

    // ─── Seeded state ───

    public function test_seeded_state_has_no_published_versions(): void
    {
        $report = $this->detector()->detect();

        $this->assertGreaterThan(0, $report['total_settings']);
        $this->assertSame(0, $report['total_versions']);
        // No versions → every setting = NO_PUBLISHED_VERSION, no VALUE_DRIFT
        $this->assertSame(0, $report['summary'][ConfigDriftDetector::KIND_VALUE_DRIFT]);
        $this->assertSame(
            $report['total_settings'],
            $report['summary'][ConfigDriftDetector::KIND_NO_PUBLISHED_VERSION]
        );
    }

    // ─── VALUE_DRIFT ───

    public function test_detects_value_drift(): void
    {
        $key = 'marketplace.max_open_needs_per_user';
        $setting = Setting::find($key);
        $publisher = $this->publisher();

        // Publish v1 with value=20
        SettingVersion::create([
            'setting_key'    => $key,
            'version_number' => 1,
            'value_json'     => ['value' => 20],
            'published_by'   => $publisher->id,
            'published_at'   => now(),
            'reason'         => 'baseline',
        ]);

        // Live setting says 999 — divergence
        $setting->value_json = ['value' => 999];
        $setting->version_number = 1;
        $setting->save();

        $report = $this->detector()->detect();

        $matches = array_filter(
            $report['findings'],
            fn ($f) => $f['kind'] === ConfigDriftDetector::KIND_VALUE_DRIFT
                && $f['setting_key'] === $key
        );
        $this->assertNotEmpty($matches, 'Expected a VALUE_DRIFT finding for ' . $key);

        $first = array_values($matches)[0];
        $this->assertSame(999, $first['context']['current_value']);
        $this->assertSame(20, $first['context']['expected_value']);
    }

    // ─── VERSION_DRIFT ───

    public function test_detects_version_drift(): void
    {
        $key = 'marketplace.max_open_needs_per_user';
        $setting = Setting::find($key);
        $publisher = $this->publisher();

        // Live: v1, value=50
        $setting->value_json = ['value' => 50];
        $setting->version_number = 1;
        $setting->save();

        // Published: v2, value=50 (values match; version lags)
        SettingVersion::create([
            'setting_key'    => $key,
            'version_number' => 2,
            'value_json'     => ['value' => 50],
            'published_by'   => $publisher->id,
            'published_at'   => now(),
            'reason'         => 'v2',
        ]);

        $report = $this->detector()->detect();

        $matches = array_filter(
            $report['findings'],
            fn ($f) => $f['kind'] === ConfigDriftDetector::KIND_VERSION_DRIFT
                && $f['setting_key'] === $key
        );
        $this->assertNotEmpty($matches);

        $first = array_values($matches)[0];
        $this->assertSame(1, (int) $first['context']['current_version']);
        $this->assertSame(2, (int) $first['context']['latest_version']);
    }

    // ─── ORPHANED_VERSION ───

    public function test_detects_orphaned_version(): void
    {
        // setting_versions has an FK to settings(key) with restrictOnDelete.
        // A genuine orphan cannot exist through normal operations — the FK
        // prevents it. This test verifies the detector's DEFENSIVE check by
        // temporarily disabling FK enforcement to inject a synthetic orphan.
        $publisher = $this->publisher();

        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            \DB::table('setting_versions')->insert([
                'id'             => (string) \Illuminate\Support\Str::uuid(),
                'setting_key'    => 'nonexistent.setting.key',
                'version_number' => 1,
                'value_json'     => json_encode(['value' => 'x']),
                'published_by'   => $publisher->id,
                'published_at'   => now(),
                'reason'         => 'synthetic orphan',
            ]);
        } finally {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $report = $this->detector()->detect();

        $matches = array_filter(
            $report['findings'],
            fn ($f) => $f['kind'] === ConfigDriftDetector::KIND_ORPHANED_VERSION
                && $f['setting_key'] === 'nonexistent.setting.key'
        );
        $this->assertNotEmpty($matches, 'Detector must flag a synthetic orphan');
    }

    // ─── Report shape ───

    public function test_report_shape(): void
    {
        $report = $this->detector()->detect();

        foreach (['checked_at','total_settings','total_versions','drift_count','findings','summary'] as $k) {
            $this->assertArrayHasKey($k, $report);
        }
        $this->assertIsArray($report['findings']);
        $this->assertIsArray($report['summary']);
        $this->assertIsInt($report['drift_count']);
    }

    // ─── API endpoint ───

    public function test_endpoint_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/integrity/drift')->assertStatus(401);
    }

    public function test_endpoint_returns_report_for_admin(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/integrity/drift');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'checked_at','total_settings','total_versions',
                    'drift_count','findings','summary',
                ],
            ]);
    }

    // ─── Command ───

    public function test_command_returns_nonzero_when_drift_present(): void
    {
        // Freshly seeded → every setting has NO_PUBLISHED_VERSION → drift > 0
        $this->artisan('config:drift')->assertExitCode(1);
    }

    public function test_command_json_output(): void
    {
        $this->artisan('config:drift', ['--json' => true])->assertExitCode(0);
    }

    public function test_command_returns_zero_when_no_drift(): void
    {
        // Wipe settings + versions → nothing to drift
        Setting::query()->delete();
        SettingVersion::query()->delete();

        $this->artisan('config:drift')->assertExitCode(0);
    }
}

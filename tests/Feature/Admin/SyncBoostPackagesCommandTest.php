<?php

namespace Tests\Feature\Admin;

use App\Models\BoostPackage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncBoostPackagesCommandTest extends TestCase
{
    use RefreshDatabase;

    private function seedCanonical(): void
    {
        Setting::updateOrCreate(
            ['key' => 'boostPackages'],
            [
                'group'        => 'CONFIG',
                'type'         => 'JSON',
                'value_json'   => ['value' => [
                    ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
                    ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
                    ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
                ]],
                'default_json' => ['value' => []],
                'risk'         => 'HIGH',
                'is_secret'    => false,
            ],
        );
    }

    public function test_command_runs_successfully(): void
    {
        $this->seedCanonical();

        $this->artisan('boost-packages:sync')
            ->expectsOutputToContain('Created:      3')
            ->expectsOutputToContain('Canonical:    1, 3, 7 day(s)')
            ->assertSuccessful();
    }

    public function test_command_fails_when_setting_missing(): void
    {
        // Migration seeds the Setting, so delete it to test the "missing" path.
        Setting::where('key', 'boostPackages')->delete();

        $this->artisan('boost-packages:sync')
            ->expectsOutputToContain('Sync failed')
            ->assertFailed();
    }

    public function test_command_is_idempotent_on_rerun(): void
    {
        $this->seedCanonical();

        $this->artisan('boost-packages:sync')->assertSuccessful();
        $this->artisan('boost-packages:sync')
            ->expectsOutputToContain('Created:      0')
            ->expectsOutputToContain('Updated:      3')
            ->assertSuccessful();

        $this->assertSame(3, BoostPackage::count());
    }
}

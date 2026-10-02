<?php

namespace Tests\Feature\Admin;

use App\Models\BoostPackage;
use App\Models\Setting;
use App\Services\Admin\BoostPackageSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostPackageSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedSetting(array $canonical): Setting
    {
        return Setting::updateOrCreate(
            ['key' => 'boostPackages'],
            [
                'group'          => 'CONFIG',
                'type'           => 'JSON',
                'value_json'     => ['value' => $canonical],
                'default_json'   => ['value' => $canonical],
                'schema_json'    => ['type' => 'array'],
                'description'    => 'test',
                'risk'           => 'HIGH',
                'is_secret'      => false,
                'dependencies'   => null,
            ],
        );
    }

    public function test_sync_returns_error_when_setting_missing(): void
    {
        // Migration seeds the Setting, so delete it to test the "missing" path.
        Setting::where('key', 'boostPackages')->delete();

        $result = app(BoostPackageSyncService::class)->sync();
        $this->assertArrayHasKey('error', $result);
        $this->assertSame(0, $result['created']);
    }

    public function test_sync_creates_missing_canonical_packages(): void
    {
        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
            ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
            ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
        ]);

        $result = app(BoostPackageSyncService::class)->sync();

        $this->assertSame(3, $result['created']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['deactivated']);
        $this->assertCount(3, BoostPackage::where('active', true)->get());
    }

    public function test_sync_updates_existing_packages(): void
    {
        BoostPackage::create([
            'duration_days' => 1, 'price' => 999.00, 'currency' => 'ETB', 'active' => true,
        ]);

        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
        ]);

        $result = app(BoostPackageSyncService::class)->sync();

        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['updated']);
        $this->assertSame('25.00', BoostPackage::where('duration_days', 1)->first()->price);
    }

    public function test_sync_deactivates_extra_packages_not_in_canonical(): void
    {
        BoostPackage::create([
            'duration_days' => 14, 'price' => 179.00, 'currency' => 'ETB', 'active' => true,
        ]);
        BoostPackage::create([
            'duration_days' => 30, 'price' => 299.00, 'currency' => 'ETB', 'active' => true,
        ]);

        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
            ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
            ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
        ]);

        $result = app(BoostPackageSyncService::class)->sync();

        $this->assertSame(2, $result['deactivated']);
        $this->assertSame(2, BoostPackage::where('active', false)->count());
        $this->assertSame(3, BoostPackage::where('active', true)->count());
    }

    public function test_sync_is_idempotent(): void
    {
        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
            ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
            ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
        ]);

        $svc = app(BoostPackageSyncService::class);
        $first = $svc->sync();
        $second = $svc->sync();

        $this->assertSame(3, $first['created']);
        $this->assertSame(0, $first['updated']);

        $this->assertSame(0, $second['created']);
        $this->assertSame(3, $second['updated']);
        $this->assertSame(0, $second['deactivated']);
        $this->assertSame(3, BoostPackage::where('active', true)->count());
    }

    public function test_sync_converts_amount_minor_to_decimal_correctly(): void
    {
        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2575, 'currency' => 'ETB'],
        ]);

        app(BoostPackageSyncService::class)->sync();

        $pkg = BoostPackage::where('duration_days', 1)->first();
        $this->assertSame('25.75', $pkg->price);
    }

    public function test_sync_handles_currency_change(): void
    {
        BoostPackage::create([
            'duration_days' => 1, 'price' => 25.00, 'currency' => 'USD', 'active' => true,
        ]);

        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
        ]);

        app(BoostPackageSyncService::class)->sync();

        $pkg = BoostPackage::where('duration_days', 1)->first();
        $this->assertSame('ETB', $pkg->currency);
    }

    public function test_sync_skips_invalid_duration(): void
    {
        $this->seedSetting([
            ['duration_days' => 0, 'amount_minor' => 100, 'currency' => 'ETB'],
            ['duration_days' => -1, 'amount_minor' => 100, 'currency' => 'ETB'],
            ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
        ]);

        $result = app(BoostPackageSyncService::class)->sync();

        $this->assertSame(1, $result['created']);
        $this->assertSame(1, BoostPackage::count());
    }

    public function test_sync_reactivates_previously_deactivated_package(): void
    {
        BoostPackage::create([
            'duration_days' => 1, 'price' => 25.00, 'currency' => 'ETB', 'active' => false,
        ]);

        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
        ]);

        app(BoostPackageSyncService::class)->sync();

        $pkg = BoostPackage::where('duration_days', 1)->first();
        $this->assertTrue($pkg->active);
    }

    public function test_sync_returns_canonical_durations(): void
    {
        $this->seedSetting([
            ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
            ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
        ]);

        $result = app(BoostPackageSyncService::class)->sync();

        $this->assertSame([1, 7], $result['canonical_durations']);
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\BoostPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WP-B25 / R-TEST-01 — BoostPackage model unit tests.
 * Schema: 2026_09_29_000013. No HasFactory.
 */
class BoostPackageTest extends TestCase
{
    use RefreshDatabase;

    private function makePackage(array $overrides = []): BoostPackage
    {
        return BoostPackage::create(array_merge([
            'duration_days' => 7,
            'price'         => '99.99',
            'currency'      => 'ETB',
            'active'        => true,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $this->assertTrue(Str::isUuid($this->makePackage()->id));
    }

    public function test_persists_core_attributes(): void
    {
        $p = $this->makePackage([
            'duration_days' => 30,
            'price'         => '199.99',
        ]);

        $this->assertDatabaseHas('boost_packages', [
            'id'            => $p->id,
            'duration_days' => 30,
            'price'         => 199.99,
            'currency'      => 'ETB',
            'active'        => true,
        ]);
    }

    public function test_duration_days_casts_integer(): void
    {
        $this->assertSame(7, $this->makePackage()->fresh()->duration_days);
    }

    public function test_price_casts_to_decimal_2(): void
    {
        $p = $this->makePackage(['price' => '50.5']);
        $this->assertSame('50.50', (string) $p->fresh()->price);
    }

    public function test_active_casts_to_boolean(): void
    {
        $active = $this->makePackage(['active' => true]);
        $this->assertTrue($active->fresh()->active);

        $inactive = $this->makePackage(['active' => false, 'duration_days' => 30]);
        $this->assertFalse($inactive->fresh()->active);
    }

    public function test_default_currency_is_etb(): void
    {
        $p = BoostPackage::create([
            'duration_days' => 7,
            'price'         => '9.99',
            'active'        => true,
        ]);

        $this->assertSame('ETB', $p->fresh()->currency);
    }

    public function test_default_active_is_false(): void
    {
        $p = BoostPackage::create([
            'duration_days' => 7,
            'price'         => '9.99',
        ]);

        $this->assertFalse($p->fresh()->active);
    }

    public function test_published_settings_version_nullable(): void
    {
        $p = $this->makePackage();
        $this->assertNull($p->fresh()->published_settings_version);
    }

    public function test_has_many_boosts(): void
    {
        $p = $this->makePackage();
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $p->boosts
        );
        $this->assertCount(0, $p->boosts);
    }

    public function test_scope_active_filters_correctly(): void
    {
        $this->makePackage(['active' => true,  'duration_days' => 7]);
        $this->makePackage(['active' => true,  'duration_days' => 30]);
        $this->makePackage(['active' => false, 'duration_days' => 90]);

        $this->assertSame(2, BoostPackage::active()->count());
    }

    public function test_duration_days_accepts_unsigned_smallint(): void
    {
        $p = $this->makePackage(['duration_days' => 365]);
        $this->assertSame(365, $p->fresh()->duration_days);
    }
}

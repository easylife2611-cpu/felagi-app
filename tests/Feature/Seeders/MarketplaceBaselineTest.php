<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\BoostPackage;
use App\Models\Category;
use Database\Seeders\MarketplaceBaselineSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

final class MarketplaceBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_runs_without_failure(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);
        $this->assertTrue(true);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);
        $first = [Category::count(), BoostPackage::count()];

        $this->seed(MarketplaceBaselineSeeder::class);
        $second = [Category::count(), BoostPackage::count()];

        $this->assertSame($first, $second, 'Re-running must not duplicate rows.');
    }

    public function test_eight_canonical_categories_present(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);

        $this->assertSame(8, Category::count());
        $this->assertSame(8, Category::active()->count());
    }

    public function test_every_category_has_am_and_en_names(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);

        foreach (Category::all() as $c) {
            $this->assertNotEmpty($c->name_am, "Category {$c->slug} missing name_am");
            $this->assertNotEmpty($c->name_en, "Category {$c->slug} missing name_en");
        }
    }

    public function test_three_canonical_boost_tiers_present(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);

        $tiers = BoostPackage::active()
            ->orderBy('duration_days')
            ->pluck('duration_days')
            ->all();

        $this->assertSame([1, 3, 7], $tiers);
    }

    public function test_boost_prices_are_positive(): void
    {
        $this->seed(MarketplaceBaselineSeeder::class);

        foreach (BoostPackage::all() as $p) {
            $this->assertGreaterThan(0, (float) $p->price);
        }
    }

    /**
     * The guard must fire when the baseline is incomplete.
     * We bypass run() and call assertBaseline() directly, so the
     * inner seeders cannot silently re-seed the data.
     */
    public function test_assert_baseline_throws_when_incomplete(): void
    {
        // RefreshDatabase → empty. No categories, no active packages.
        $seeder = new MarketplaceBaselineSeeder();
        $seeder->setContainer($this->app);

        $this->expectException(RuntimeException::class);
        $seeder->assertBaseline();
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BoostPackage;
use App\Models\Category;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * L343-B — Canonical marketplace baseline entry point.
 *
 * Runs the two data seeders that constitute the marketplace baseline:
 *   - CategorySeeder       (8 canonical categories)
 *   - BoostPackageSeeder   (1 / 3 / 7 day tiers)
 *
 * Then asserts the baseline is present, so silent regressions fail loudly.
 * Idempotent — safe to re-run.
 */
class MarketplaceBaselineSeeder extends Seeder
{
    public const EXPECTED_CATEGORIES  = 8;
    public const EXPECTED_BOOST_TIERS = [1, 3, 7];

    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BoostPackageSeeder::class,
        ]);

        $this->assertBaseline();
    }

    public function assertBaseline(): void
    {
        $catCount = Category::active()->count();
        if ($catCount < self::EXPECTED_CATEGORIES) {
            throw new RuntimeException(
                "Marketplace baseline: expected >= " . self::EXPECTED_CATEGORIES .
                " active categories, found {$catCount}."
            );
        }

        $tiers = BoostPackage::active()
            ->orderBy('duration_days')
            ->pluck('duration_days')
            ->all();

        $missing = array_diff(self::EXPECTED_BOOST_TIERS, $tiers);
        if (! empty($missing)) {
            throw new RuntimeException(
                "Marketplace baseline: missing boost tiers: " . implode(', ', $missing)
            );
        }

        $this->command?->info(sprintf(
            'Marketplace baseline OK — %d categories, %d boost tiers.',
            $catCount,
            count($tiers),
        ));
    }
}

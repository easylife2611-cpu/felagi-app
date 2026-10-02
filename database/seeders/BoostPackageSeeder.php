<?php

namespace Database\Seeders;

use App\Models\BoostPackage;
use Illuminate\Database\Seeder;

/**
 * L330 — BoostPackageSeeder (canonical 1, 3, 7 days).
 *
 * Per DFM-FDS-1.4 line 125: seed 1, 3 and 7 day packages.
 * Prices are illustrative Admin-published seeds, not final.
 *
 * The canonical source of truth is now the `boostPackages` Setting
 * (A020-editable). This seeder bootstraps fresh installs and is
 * superseded by `php artisan boost-packages:sync`.
 */
class BoostPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['duration_days' => 1, 'price' => 25.00, 'currency' => 'ETB'],
            ['duration_days' => 3, 'price' => 49.00, 'currency' => 'ETB'],
            ['duration_days' => 7, 'price' => 99.00, 'currency' => 'ETB'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($packages as $pkg) {
            $existing = BoostPackage::where('duration_days', $pkg['duration_days'])->first();

            BoostPackage::updateOrCreate(
                ['duration_days' => $pkg['duration_days']],
                [
                    'price'    => $pkg['price'],
                    'currency' => $pkg['currency'],
                    'active'   => true,
                ],
            );

            if ($existing) { $updated++; } else { $created++; }
        }

        $this->command->info(
            "Boost packages seeded: " . count($packages) .
            " ({$created} created, {$updated} updated)."
        );
    }
}

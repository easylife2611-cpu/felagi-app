<?php

namespace Database\Seeders;

use App\Models\BoostPackage;
use Illuminate\Database\Seeder;

/**
 * Seeds 4 ETB boost packages.
 *
 * Idempotent — matches on (duration_days, currency).
 * Safe to re-run.
 */
class BoostPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['duration_days' => 3,  'price' => 49.00,  'currency' => 'ETB'],
            ['duration_days' => 7,  'price' => 99.00,  'currency' => 'ETB'],
            ['duration_days' => 14, 'price' => 179.00, 'currency' => 'ETB'],
            ['duration_days' => 30, 'price' => 299.00, 'currency' => 'ETB'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($packages as $pkg) {
            $existing = BoostPackage::where('duration_days', $pkg['duration_days'])
                ->where('currency', $pkg['currency'])
                ->first();

            BoostPackage::updateOrCreate(
                [
                    'duration_days' => $pkg['duration_days'],
                    'currency'      => $pkg['currency'],
                ],
                [
                    'price'  => $pkg['price'],
                    'active' => true,
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

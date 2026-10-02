<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ── Canonical control registry (WP-13) ──
            ControlRegistrySeeder::class,

            // ── L326: Production data ──
            CategorySeeder::class,
            BoostPackageSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}

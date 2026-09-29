<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Canonical control registry (WP-13) ──
        $this->call([
            ControlRegistrySeeder::class,
        ]);
    }
}

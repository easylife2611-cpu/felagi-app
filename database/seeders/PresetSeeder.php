<?php

namespace Database\Seeders;

use App\Models\SettingPreset;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * AH — seed two canonical safe presets.
 */
class PresetSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->first();
        if (! $admin) {
            $this->command?->warn('PresetSeeder: no user to attribute presets to. Skipping.');
            return;
        }

        $presets = [
            [
                'name'         => 'safe-defaults',
                'display_name' => 'Safe Defaults',
                'description'  => 'All revenue-generating features off. Baseline read-only mode.',
                'values_json'  => [
                    'feature.payments'  => false,
                    'feature.boosts'    => false,
                    'feature.ai_compare'=> false,
                    'system.safe_mode'  => false,
                ],
            ],
            [
                'name'         => 'maintenance',
                'display_name' => 'Maintenance Mode',
                'description'  => 'Safe mode + all writes frozen.',
                'values_json'  => [
                    'system.safe_mode' => true,
                ],
            ],
        ];

        foreach ($presets as $p) {
            SettingPreset::updateOrCreate(
                ['name' => $p['name']],
                array_merge($p, ['status' => SettingPreset::STATUS_ACTIVE, 'created_by' => $admin->id]),
            );
        }

        $this->command?->info('PresetSeeder: 2 presets seeded.');
    }
}

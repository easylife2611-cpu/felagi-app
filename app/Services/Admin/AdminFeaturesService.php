<?php

namespace App\Services\Admin;

use App\Models\Setting;

/**
 * Admin Features Status Service — L310 (A004)
 *
 * Real feature-flag state for A004 Features screen.
 * Per WP-05c_LOCKED: columns = fieldSettingKey, effective, dependencies, version.
 * Controls: allowNeeds, allowOffers, messaging, aiCompare.
 *
 * Canonical feature keys come from ControlRegistrySeeder (DB keys,
 * not design display names). When the settings table is empty (seeder
 * not yet run), returns the seeder's documented defaults with
 * source='default' so operators see real intent, never blank cells.
 */
class AdminFeaturesService
{
    public const FEATURES = [
        'feature.needs'      => ['label' => 'adminFeatureNeeds',     'default' => true],
        'feature.offers'     => ['label' => 'adminFeatureOffers',    'default' => true],
        'feature.messaging'  => ['label' => 'adminFeatureMessaging', 'default' => true],
        'feature.ai_compare' => ['label' => 'adminFeatureAiCompare', 'default' => true],
    ];

    public function status(): array
    {
        $features = [];

        foreach (self::FEATURES as $key => $meta) {
            $setting = Setting::find($key);

            if ($setting) {
                $features[] = [
                    'key'          => $key,
                    'label_key'    => $meta['label'],
                    'effective'    => $setting->value_json['value'] ?? $meta['default'],
                    'default'      => $setting->default_json['value'] ?? $meta['default'],
                    'risk'         => $setting->risk,
                    'version'      => $setting->version_number,
                    'dependencies' => $setting->dependencies,
                    'source'       => 'db',
                ];
            } else {
                $features[] = [
                    'key'          => $key,
                    'label_key'    => $meta['label'],
                    'effective'    => $meta['default'],
                    'default'      => $meta['default'],
                    'risk'         => 'HIGH',
                    'version'      => 0,
                    'dependencies' => null,
                    'source'       => 'default',
                ];
            }
        }

        return [
            'total'       => count($features),
            'features'    => $features,
            'computed_at' => now()->toIso8601String(),
        ];
    }
}

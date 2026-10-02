<?php

namespace App\Services\Admin;

use App\Models\BoostPackage;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * L330 — BoostPackageSyncService
 *
 * Syncs the `boost_packages` table from the canonical
 * `boostPackages` Setting (A020 control).
 *
 * Design (DFM-FDS-1.4 §8.2 + control-registry.json):
 *   - Setting is canonical; A020 edits it via WP-13 change lifecycle
 *   - Table is a synced cache for FK integrity and public API
 *   - Rows are NEVER deleted (soft-deactivate only — FK safety)
 *
 * Idempotent — safe to run every minute.
 */
class BoostPackageSyncService
{
    public const SETTING_KEY = 'boostPackages';

    /**
     * @return array{created: int, updated: int, deactivated: int, canonical_durations: int[], error?: string}
     */
    public function sync(): array
    {
        $setting = Setting::find(self::SETTING_KEY);
        if (!$setting) {
            return ['error' => 'Setting ' . self::SETTING_KEY . ' not found', 'created'=>0, 'updated'=>0, 'deactivated'=>0, 'canonical_durations'=>[]];
        }

        $canonical = $setting->value_json['value'] ?? null;
        if (!is_array($canonical)) {
            return ['error' => 'Setting value is not an array', 'created'=>0, 'updated'=>0, 'deactivated'=>0, 'canonical_durations'=>[]];
        }

        $created = 0;
        $updated = 0;
        $deactivated = 0;
        $canonicalDurations = [];

        DB::transaction(function () use ($canonical, &$created, &$updated, &$deactivated, &$canonicalDurations) {
            foreach ($canonical as $row) {
                $days = (int) ($row['duration_days'] ?? 0);
                $amountMinor = (int) ($row['amount_minor'] ?? 0);
                $currency = strtoupper((string) ($row['currency'] ?? 'ETB'));

                if ($days <= 0) {
                    continue; // skip invalid
                }

                $canonicalDurations[] = $days;
                $price = round($amountMinor / 100, 2);

                $existing = BoostPackage::where('duration_days', $days)->first();

                if ($existing) {
                    $existing->update([
                        'price'    => $price,
                        'currency' => $currency,
                        'active'   => true,
                    ]);
                    $updated++;
                } else {
                    BoostPackage::create([
                        'duration_days' => $days,
                        'price'         => $price,
                        'currency'      => $currency,
                        'active'        => true,
                    ]);
                    $created++;
                }
            }

            if (!empty($canonicalDurations)) {
                $toDeactivate = BoostPackage::whereNotIn('duration_days', $canonicalDurations)
                    ->where('active', true)
                    ->get();

                foreach ($toDeactivate as $pkg) {
                    $pkg->update(['active' => false]);
                    $deactivated++;
                }
            }
        });

        return [
            'created'             => $created,
            'updated'             => $updated,
            'deactivated'         => $deactivated,
            'canonical_durations' => $canonicalDurations,
        ];
    }
}

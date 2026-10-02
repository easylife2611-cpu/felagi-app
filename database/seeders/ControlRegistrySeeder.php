<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the 32 canonical settings from DFM-FDS-1.4.md §8.2.
 *
 * Idempotent — uses updateOrCreate. Safe to re-run.
 */
class ControlRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $controls = [
            // ── FEATURE toggles (HIGH risk) ──
            ['feature.needs',                    'FEATURE', 'BOOLEAN', true,  'HIGH',     false],
            ['feature.offers',                   'FEATURE', 'BOOLEAN', true,  'HIGH',     false],
            ['feature.messaging',                'FEATURE', 'BOOLEAN', true,  'HIGH',     false],
            ['feature.telegram_publication',     'FEATURE', 'BOOLEAN', true,  'HIGH',     false],
            ['feature.ai_compare',               'FEATURE', 'BOOLEAN', true,  'HIGH',     false],
            ['feature.payments',                 'FEATURE', 'BOOLEAN', false, 'HIGH',     false],
            ['feature.boosts',                   'FEATURE', 'BOOLEAN', false, 'HIGH',     false],

            // ── Telegram (HIGH/MEDIUM) ──
            ['telegram.max_destinations_per_need', 'CONFIG', 'INTEGER', 3,  'HIGH',   false],
            ['telegram.daily_cap_per_destination', 'CONFIG', 'INTEGER', 10, 'HIGH',   false],
            ['telegram.min_interval_seconds',      'CONFIG', 'INTEGER', 30, 'MEDIUM', false],
            ['telegram.max_attempts',              'CONFIG', 'INTEGER', 3,  'MEDIUM', false],

            // ── Marketplace (MEDIUM/HIGH) ──
            ['marketplace.max_open_needs_per_user',   'CONFIG', 'INTEGER', 20, 'MEDIUM', false],
            ['marketplace.offer_deadline_max_days',   'CONFIG', 'INTEGER', 90, 'MEDIUM', false],
            ['marketplace.max_offers_per_comparison', 'CONFIG', 'INTEGER', 20, 'HIGH',   false],

            // ── AI (HIGH/MEDIUM) ──
            ['ai.criteria_version',  'CONFIG', 'STRING',  'v1',   'HIGH',   false],
            ['ai.prompt_version',    'CONFIG', 'STRING',  'v1',   'HIGH',   false],
            ['ai.model_id',          'CONFIG', 'STRING',  null,   'HIGH',   false],
            ['ai.max_input_tokens',  'CONFIG', 'INTEGER', 4000,   'HIGH',   false],
            ['ai.max_output_tokens', 'CONFIG', 'INTEGER', 1000,   'HIGH',   false],
            ['ai.timeout_seconds',   'CONFIG', 'INTEGER', 60,     'MEDIUM', false],
            ['ai.max_attempts',      'CONFIG', 'INTEGER', 3,      'MEDIUM', false],
            ['ai.user_daily_cap',    'CONFIG', 'INTEGER', 10,     'MEDIUM', false],
            ['ai.need_daily_cap',    'CONFIG', 'INTEGER', 3,      'MEDIUM', false],

            // ── Payment (CRITICAL) ──
            ['payments.provider', 'SECURITY', 'STRING', null, 'CRITICAL', false],

            // ── Uploads/Exports (MEDIUM) ──
            ['uploads.max_bytes', 'CONFIG', 'INTEGER', 5242880, 'MEDIUM', false],
            ['exports.max_rows',  'CONFIG', 'INTEGER', 10000,   'MEDIUM', false],

            // ── Content (LOW) ──
            // ── BoostPackages (multi-row JSON, from control-registry) ──
            // Per DFM-FDS-1.4 §8.2 #25: durations 1/3/7 days, prices are
            // Admin-published (seed values are illustrative, not final).
            // The actual seeding of value_json is done by a dedicated
            // migration to keep the JSON shape explicit.
            ['boostPackages', 'CONFIG', 'JSON', [
                ['duration_days' => 1, 'amount_minor' => 2500, 'currency' => 'ETB'],
                ['duration_days' => 3, 'amount_minor' => 4900, 'currency' => 'ETB'],
                ['duration_days' => 7, 'amount_minor' => 9900, 'currency' => 'ETB'],
            ], 'HIGH', false],

            ['content.welcome_am', 'CONTENT', 'STRING', null, 'LOW', false],
            ['content.welcome_en', 'CONTENT', 'STRING', null, 'LOW', false],

            // ── Privacy (MEDIUM) ──
            ['privacy.temp_file_days', 'CONFIG', 'INTEGER', 7, 'MEDIUM', false],
            ['privacy.export_days',    'CONFIG', 'INTEGER', 7, 'MEDIUM', false],

            // ── System (CRITICAL) ──
            ['system.safe_mode', 'SECURITY', 'BOOLEAN', false, 'CRITICAL', false],
        ];

        $created = 0;
        $updated = 0;

        foreach ($controls as [$key, $group, $type, $default, $risk, $secret]) {
            $existing = Setting::find($key);

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group'          => $group,
                    'type'           => $type,
                    'value_json'     => ['value' => $default],
                    'default_json'   => ['value' => $default],
                    'risk'           => $risk,
                    'is_secret'      => $secret,
                    'version_number' => $existing?->version_number ?? 1,
                    'dependencies'   => $this->dependenciesFor($key),
                ],
            );

            if ($existing) {
                $updated++;
            } else {
                $created++;
            }
        }

        $total = count($controls);
        $this->command->info("Control registry seeded: {$total} settings ({$created} created, {$updated} updated).");
    }

    /**
     * J (audit L276) — dependency map per DFM §8.2.
     * Each entry: [setting_key => [[dep_key, required_value, message], ...]]
     */
    private function dependenciesMap(): array
    {
        return [
            'feature.boosts' => [
                [
                    'key'     => 'feature.payments',
                    'value'   => true,
                    'message' => 'Boosts require Payments to be enabled first.',
                ],
            ],
            'feature.ai_compare' => [
                [
                    'key'     => 'ai.model_id',
                    'value'   => 'non_empty',
                    'message' => 'AI Compare requires ai.model_id to be configured.',
                ],
            ],
        ];
    }

    /**
     * Convert the map into the canonical array shape stored in the
     * settings.dependencies JSON column.
     */
    private function dependenciesFor(string $key): ?array
    {
        $map = $this->dependenciesMap();
        if (! isset($map[$key])) {
            return null;
        }
        $out = [];
        foreach ($map[$key] as $dep) {
            $out[] = [
                'key'     => $dep['key'],
                'value'   => $dep['value'],
                'message' => $dep['message'],
            ];
        }
        return $out;
    }
}

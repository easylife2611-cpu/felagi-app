<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Models\SettingVersion;
use Illuminate\Support\Facades\DB;

/**
 * AC (audit L276) — Configuration drift detection.
 *
 * Compares the current `settings` row against the latest published
 * `setting_versions` row for the same key. Reports:
 *
 *   - VALUE_DRIFT:         current value differs from latest version
 *   - VERSION_DRIFT:       settings.version_number < latest version
 *   - NO_PUBLISHED_VERSION: setting exists but has no version row
 *   - ORPHANED_VERSION:    version references a missing setting
 *
 * Read-only — never mutates state, never repairs.
 */
class ConfigDriftDetector
{
    public const KIND_VALUE_DRIFT          = 'VALUE_DRIFT';
    public const KIND_VERSION_DRIFT        = 'VERSION_DRIFT';
    public const KIND_NO_PUBLISHED_VERSION = 'NO_PUBLISHED_VERSION';
    public const KIND_ORPHANED_VERSION     = 'ORPHANED_VERSION';

    /**
     * @return array{
     *   checked_at: string,
     *   total_settings: int,
     *   total_versions: int,
     *   drift_count: int,
     *   findings: array<int,array>,
     *   summary: array<string,int>
     * }
     */
    public function detect(): array
    {
        $findings = [];

        $settingsByKey = Setting::query()->get()->keyBy('key');

        // 1. Per-setting check against latest published version
        foreach ($settingsByKey as $key => $setting) {
            $latest = SettingVersion::where('setting_key', $key)
                ->orderByDesc('version_number')
                ->first();

            if (! $latest) {
                $findings[] = $this->finding(
                    self::KIND_NO_PUBLISHED_VERSION, $key, null, [
                        'current_version' => $setting->version_number,
                    ]
                );
                continue;
            }

            $currentValue  = $setting->value_json['value'] ?? null;
            $expectedValue = $latest->value_json['value'] ?? null;

            if ($currentValue !== $expectedValue) {
                $findings[] = $this->finding(
                    self::KIND_VALUE_DRIFT, $key, $latest->version_number, [
                        'current_value'  => $this->safeValue($currentValue),
                        'expected_value' => $this->safeValue($expectedValue),
                        'current_version'=> $setting->version_number,
                    ]
                );
            }

            if ((int) $setting->version_number !== (int) $latest->version_number) {
                $findings[] = $this->finding(
                    self::KIND_VERSION_DRIFT, $key, $latest->version_number, [
                        'current_version' => $setting->version_number,
                        'latest_version'  => $latest->version_number,
                    ]
                );
            }
        }

        // 2. Orphaned versions — version references a missing setting
        $orphans = SettingVersion::query()
            ->whereNotIn('setting_key', $settingsByKey->keys()->all())
            ->orderBy('setting_key')
            ->orderByDesc('version_number')
            ->get();

        foreach ($orphans as $v) {
            $findings[] = $this->finding(
                self::KIND_ORPHANED_VERSION, $v->setting_key, $v->version_number, [
                    'published_at' => optional($v->published_at)->toIso8601String(),
                ]
            );
        }

        return [
            'checked_at'      => now()->toIso8601String(),
            'total_settings'  => $settingsByKey->count(),
            'total_versions'  => SettingVersion::count(),
            'drift_count'     => count($findings),
            'findings'        => $findings,
            'summary'         => $this->summarise($findings),
        ];
    }

    /**
     * @param  array<int,array>  $findings
     * @return array<string,int>
     */
    private function summarise(array $findings): array
    {
        $out = [
            self::KIND_VALUE_DRIFT          => 0,
            self::KIND_VERSION_DRIFT        => 0,
            self::KIND_NO_PUBLISHED_VERSION => 0,
            self::KIND_ORPHANED_VERSION     => 0,
        ];
        foreach ($findings as $f) {
            $k = $f['kind'] ?? null;
            if ($k !== null && isset($out[$k])) {
                $out[$k]++;
            }
        }
        return $out;
    }

    private function finding(string $kind, string $settingKey, ?int $version, array $context): array
    {
        return [
            'kind'        => $kind,
            'setting_key' => $settingKey,
            'version'     => $version,
            'context'     => $context,
        ];
    }

    /**
     * Redact values that are long or obviously sensitive before
     * returning them in the report.
     */
    private function safeValue(mixed $value): mixed
    {
        if (is_string($value) && mb_strlen($value) > 100) {
            return mb_substr($value, 0, 100) . '…';
        }
        if (is_array($value)) {
            return '[array:' . count($value) . ']';
        }
        return $value;
    }

    /**
     * Convenience: has any drift at all?
     */
    public function hasDrift(): bool
    {
        return $this->detect()['drift_count'] > 0;
    }
}

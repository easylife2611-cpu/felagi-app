<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Models\SettingDraft;

/**
 * AE — Change diff view.
 *
 * Computes a field-by-field diff between the current setting value and
 * a draft's proposed value. Purely read-only. The diff is what the UI
 * uses to show operators exactly what will change before publish.
 *
 * Rules:
 *   - Deep diff for array values (recursive per key)
 *   - Scalars only in the output (arrays are summarised)
 *   - UNCHANGED rows omitted from `rows`, counted in `counts`
 *   - Long strings masked to 200 chars
 *   - Secret settings: values are redacted, only presence shown
 */
class SettingDiffService
{
    public const CHANGE_ADDED     = 'ADDED';
    public const CHANGE_REMOVED   = 'REMOVED';
    public const CHANGE_MODIFIED  = 'MODIFIED';
    public const CHANGE_UNCHANGED = 'UNCHANGED';

    public const MASK_THRESHOLD = 200;

    public function diff(SettingDraft $draft): array
    {
        $setting = Setting::findOrFail($draft->setting_key);

        $before = $setting->value_json['value']
                ?? $setting->default_json['value']
                ?? null;
        $after  = $draft->proposed_value_json['value'] ?? null;

        $rows = $this->diffValue('value', $before, $after);

        $isSecret = (bool) $setting->is_secret;

        return [
            'draft_id'                => $draft->id,
            'setting_key'             => $setting->key,
            'setting_group'           => $setting->group,
            'setting_type'            => $setting->type,
            'setting_risk'            => $setting->risk,
            'is_secret'               => $isSecret,
            'requires_reauth'         => $setting->requiresReauth(),
            'requires_second_factor'  => $setting->requiresSecondFactor(),
            'draft_status'            => $draft->status,
            'current_version'         => $setting->version_number,
            'before_summary'          => $this->summary($before),
            'after_summary'           => $this->summary($after),
            'rows'                    => $rows,
            'counts'                  => $this->counts($rows),
            'changed'                 => count($rows) > 0,
            'generated_at'            => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<int,array{path:string,change:string,before:mixed,after:mixed}>
     */
    private function diffValue(string $path, mixed $before, mixed $after): array
    {
        // Both arrays — deep diff
        if (is_array($before) && is_array($after)) {
            $rows = [];
            $keys = array_unique(array_merge(
                array_keys($before),
                array_keys($after)
            ));
            foreach ($keys as $key) {
                $b = $before[$key] ?? null;
                $a = $after[$key] ?? null;
                $rows = array_merge(
                    $rows,
                    $this->diffValue($path . '.' . $key, $b, $a)
                );
            }
            return $rows;
        }

        // One array, other scalar — MODIFIED at this path
        if (is_array($before) || is_array($after)) {
            return [[
                'path'   => $path,
                'change' => self::CHANGE_MODIFIED,
                'before' => $this->mask($before),
                'after'  => $this->mask($after),
            ]];
        }

        // Both scalar — classify
        $change = match (true) {
            $before === null && $after !== null => self::CHANGE_ADDED,
            $before !== null && $after === null => self::CHANGE_REMOVED,
            $before === $after                  => self::CHANGE_UNCHANGED,
            default                             => self::CHANGE_MODIFIED,
        };

        if ($change === self::CHANGE_UNCHANGED) {
            return []; // omit — counted via counts.total vs counts.changed
        }

        return [[
            'path'   => $path,
            'change' => $change,
            'before' => $this->mask($before),
            'after'  => $this->mask($after),
        ]];
    }

    private function mask(mixed $value): mixed
    {
        if (is_array($value)) {
            return '[array:' . count($value) . ']';
        }
        if (is_string($value) && mb_strlen($value) > self::MASK_THRESHOLD) {
            return mb_substr($value, 0, self::MASK_THRESHOLD) . '…';
        }
        return $value;
    }

    private function summary(mixed $value): string
    {
        return match (true) {
            $value === null                  => 'null',
            is_bool($value)                  => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string) $value,
            is_string($value)                => 'string(' . mb_strlen($value) . ')',
            is_array($value)                 => 'array(' . count($value) . ')',
            default                          => gettype($value),
        };
    }

    private function counts(array $rows): array
    {
        $c = [
            self::CHANGE_ADDED    => 0,
            self::CHANGE_REMOVED  => 0,
            self::CHANGE_MODIFIED => 0,
        ];
        foreach ($rows as $r) {
            if (isset($c[$r['change']])) {
                $c[$r['change']]++;
            }
        }
        $c['total'] = count($rows);
        return $c;
    }
}

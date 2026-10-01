<?php

namespace App\Services\Admin;

use App\Models\Setting;

/**
 * J (audit L276) — Dependency-aware controls.
 *
 * Reads the `dependencies` JSON column on a Setting and reports
 * whether each dependency is currently satisfied. Never mutates state.
 *
 * Dependency shape (JSON):
 *   [ { "key": "feature.payments", "value": true, "message": "..." } ]
 *
 * `value` supports:
 *   - a scalar (bool / int / string) → exact match against the
 *     dependency's current value
 *   - the string "non_empty" → dependency value must be a non-empty
 *     string or a non-null value
 */
class ControlDependencyService
{
    /**
     * @return array{
     *   setting_key:string,
     *   dependencies:array<int,array{key:string,required:mixed,current:mixed,status:string,message:?string}>,
     *   satisfied:bool,
     *   violations:array<int,string>
     * }
     */
    public function inspect(Setting $setting): array
    {
        $deps = $setting->dependencies ?? [];

        $rows = [];
        $violations = [];

        foreach ($deps as $dep) {
            $key      = $dep['key'] ?? null;
            $required = $dep['value'] ?? null;
            $message  = $dep['message'] ?? null;

            if (! $key) {
                continue;
            }

            $target  = Setting::find($key);
            $current = $target?->value_json['value'] ?? null;
            $status  = $this->evaluate($required, $current);

            $rows[] = [
                'key'      => $key,
                'required' => $required,
                'current'  => $current,
                'status'   => $status,
                'message'  => $status === 'SATISFIED' ? null : $message,
            ];

            if ($status !== 'SATISFIED') {
                $violations[] = $message ?? "Dependency {$key} is not satisfied.";
            }
        }

        return [
            'setting_key'  => $setting->key,
            'dependencies' => $rows,
            'satisfied'    => empty($violations),
            'violations'   => $violations,
        ];
    }

    private function evaluate(mixed $required, mixed $current): string
    {
        if ($required === 'non_empty') {
            return ($current !== null && $current !== '')
                ? 'SATISFIED'
                : 'VIOLATED';
        }

        return $required === $current ? 'SATISFIED' : 'VIOLATED';
    }

    /**
     * Bulk inspection for a list of settings — used by A004.
     *
     * @param  iterable<Setting>  $settings
     * @return array<int,array>
     */
    public function inspectMany(iterable $settings): array
    {
        $out = [];
        foreach ($settings as $s) {
            if (empty($s->dependencies)) {
                continue;
            }
            $out[] = $this->inspect($s);
        }
        return $out;
    }
}

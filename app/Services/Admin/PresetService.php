<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Models\SettingPreset;
use App\Models\User;

/**
 * AH (audit L276) — Safe presets / operation modes.
 *
 * A preset is a named bundle of setting values. Applying a preset
 * delegates to BulkActionService's safety machinery (frozen selection
 * digest + per-item execution + item-level succeeded/failed/unknown),
 * so all bulk protections apply automatically.
 */
class PresetService
{
    public function __construct(
        private readonly BulkActionService $bulk,
    ) {}

    /**
     * List presets.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int,SettingPreset>
     */
    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return SettingPreset::query()
            ->where('status', SettingPreset::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();
    }

    /**
     * Preview a preset application.
     *
     * @return array{
     *   preset:string,
     *   missing_keys:array<int,string>,
     *   applicable_keys:array<int,string>,
     *   selection_digest:string,
     *   selection_ids:array<int,string>,
     *   max_batch:int
     * }
     */
    public function preview(SettingPreset $preset): array
    {
        $keys = array_keys($preset->values_json ?? []);

        $missing = [];
        $applicable = [];
        foreach ($keys as $key) {
            if (Setting::find($key)) {
                $applicable[] = $key;
            } else {
                $missing[] = $key;
            }
        }

        sort($applicable);
        $digest = hash('sha256', json_encode([
            'preset' => $preset->name,
            'keys'   => $applicable,
            'count'  => count($applicable),
        ], JSON_UNESCAPED_UNICODE));

        return [
            'preset'             => $preset->name,
            'missing_keys'       => $missing,
            'applicable_keys'    => $applicable,
            'selection_digest'   => $digest,
            'selection_ids'      => $applicable,
            'max_batch'          => BulkActionService::MAX_BATCH,
            'values'             => $preset->values_json,
        ];
    }

    /**
     * Apply a preset by expanding it into a bulk action.
     *
     * Uses action `setting.set` which is not implemented here — for
     * this iteration the preset delegates to a dedicated bulk action
     * `preset.apply` registered in BulkActionService.
     */
    public function apply(User $actor, SettingPreset $preset): \App\Models\BulkAction
    {
        $preview = $this->preview($preset);

        return $this->bulk->applyPreset(
            $actor,
            $preset->name,
            $preview['applicable_keys'],
            $preset->values_json ?? [],
        );
    }
}

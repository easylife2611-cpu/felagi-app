<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Admin Settings Status Service — L308 (A017)
 */
class AdminSettingsStatusService
{
    public const FREEZE_CACHE_KEY  = 'felagi.settings.freeze';
    public const FREEZE_REASON_KEY = 'felagi.settings.freeze.reason';
    public const FREEZE_SINCE_KEY  = 'felagi.settings.freeze.since';

    public function status(): array
    {
        return [
            'total'       => Setting::count(),
            'freeze'      => $this->freezeState(),
            'settings'    => $this->listSettings(),
            'computed_at' => now()->toIso8601String(),
        ];
    }

    public function freezeState(): array
    {
        try {
            return [
                'enabled' => (bool) Cache::get(self::FREEZE_CACHE_KEY, false),
                'reason'  => Cache::get(self::FREEZE_REASON_KEY),
                'since'   => Cache::get(self::FREEZE_SINCE_KEY),
            ];
        } catch (\Throwable) {
            return ['enabled' => false, 'reason' => null, 'since' => null];
        }
    }

    private function listSettings(): array
    {
        return Setting::query()
            ->orderBy('key')
            ->get()
            ->map(function (Setting $s) {
                $mask = (bool) $s->is_secret;
                return [
                    'key'        => $s->key,
                    'group'      => $s->group,
                    'type'       => $s->type,
                    'effective'  => $mask ? '[secret]' : ($s->value_json['value'] ?? null),
                    'default'    => $mask ? '[secret]' : ($s->default_json['value'] ?? null),
                    'risk'       => $s->risk,
                    'version'    => $s->version_number,
                    'is_secret'  => $mask,
                    'updated_at' => $s->updated_at?->toIso8601String(),
                ];
            })
            ->all();
    }
}

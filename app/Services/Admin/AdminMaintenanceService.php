<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Cache;

/**
 * Admin Maintenance Status Service — L309 (A021)
 *
 * Real maintenance-mode state + cache status for A021 Maintenance screen.
 * Per WP-05c_LOCKED: columns = status, version, updated.
 * Controls: maintenance, maintenanceText, refreshCache.
 *
 * Cache-based (mirrors L307 AdminSafeModeService) — no settings-table
 * dependency required, so the endpoint reports reliable state even
 * when the settings seeder has not been run.
 */
class AdminMaintenanceService
{
    public const CACHE_KEY        = 'felagi.maintenance.enabled';
    public const MESSAGE_KEY      = 'felagi.maintenance.message';
    public const SINCE_KEY        = 'felagi.maintenance.since';
    public const LAST_REFRESH_KEY = 'felagi.maintenance.last_refresh';

    public function status(): array
    {
        return [
            'maintenance' => $this->maintenanceState(),
            'cache'       => $this->cacheState(),
            'computed_at' => now()->toIso8601String(),
        ];
    }

    public function maintenanceState(): array
    {
        try {
            return [
                'enabled' => (bool) Cache::get(self::CACHE_KEY, false),
                'message' => Cache::get(self::MESSAGE_KEY),
                'since'   => Cache::get(self::SINCE_KEY),
            ];
        } catch (\Throwable) {
            return ['enabled' => false, 'message' => null, 'since' => null];
        }
    }

    private function cacheState(): array
    {
        return [
            'config_cached'    => $this->fileExists(base_path('bootstrap/cache/config.php')),
            'route_cached'     => $this->fileExists(base_path('bootstrap/cache/routes-v7.php')),
            'view_cached'      => $this->countFiles(storage_path('framework/views')),
            'storage_writable' => is_writable(storage_path('framework')),
            'last_refresh'     => $this->lastRefresh(),
        ];
    }

    private function fileExists(string $path): bool
    {
        try { return file_exists($path); } catch (\Throwable) { return false; }
    }

    private function countFiles(string $dir): int
    {
        try {
            if (! is_dir($dir)) return 0;
            return count(glob($dir . '/*.php') ?: []);
        } catch (\Throwable) { return 0; }
    }

    private function lastRefresh(): ?string
    {
        try { return Cache::get(self::LAST_REFRESH_KEY); } catch (\Throwable) { return null; }
    }
}

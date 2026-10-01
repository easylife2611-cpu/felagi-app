<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Admin Integrity Service — L306
 * Real integrity checks for A014 Integrity screen.
 */
class AdminIntegrityService
{
    public function status(): array
    {
        return [
            'config_cache'  => $this->configCache(),
            'route_cache'   => $this->routeCache(),
            'view_cache'    => $this->viewCache(),
            'storage_dirs'  => $this->storageDirs(),
            'db_tables'     => $this->dbTables(),
            'log_errors'    => $this->logErrors(100),
            'computed_at'   => now()->toIso8601String(),
        ];
    }

    private function configCache(): array
    {
        $exists = file_exists(base_path('bootstrap/cache/config.php'));
        return [
            'status'  => $exists ? 'ok' : 'warn',
            'message' => $exists ? 'Config cached' : 'Config NOT cached',
        ];
    }

    private function routeCache(): array
    {
        $exists = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        return [
            'status'  => $exists ? 'ok' : 'warn',
            'message' => $exists ? 'Routes cached' : 'Routes NOT cached',
        ];
    }

    private function viewCache(): array
    {
        $dir = storage_path('framework/views');
        $count = is_dir($dir) ? count(File::glob($dir . '/*.php')) : 0;
        return [
            'status'  => 'ok',
            'message' => $count . ' compiled view(s)',
            'count'   => $count,
        ];
    }

    private function storageDirs(): array
    {
        $required = [
            'storage/app'         => storage_path('app'),
            'storage/framework'   => storage_path('framework'),
            'storage/logs'        => storage_path('logs'),
        ];

        $results = [];
        foreach ($required as $name => $path) {
            $exists   = is_dir($path);
            $writable = $exists && is_writable($path);
            $results[$name] = [
                'status'   => $exists && $writable ? 'ok' : ($exists ? 'warn' : 'fail'),
                'exists'   => $exists,
                'writable' => $writable,
            ];
        }
        return $results;
    }

    private function dbTables(): array
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $count = count($tables);
            return [
                'status'  => $count > 0 ? 'ok' : 'fail',
                'count'   => $count,
                'message' => $count . ' tables',
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'fail',
                'count'   => 0,
                'message' => substr($e->getMessage(), 0, 200),
            ];
        }
    }

    private function logErrors(int $lines): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (! file_exists($logPath)) {
            return ['count' => 0, 'status' => 'ok', 'message' => 'No log file'];
        }

        try {
            $content = file_get_contents($logPath);
            $count = preg_match_all('/\.ERROR:/', $content);
            return [
                'count'   => (int) $count,
                'status'  => $count === 0 ? 'ok' : ($count < 10 ? 'warn' : 'fail'),
                'message' => $count . ' total error(s) in log',
            ];
        } catch (\Throwable $e) {
            return ['count' => 0, 'status' => 'warn', 'message' => 'Log unreadable'];
        }
    }
}

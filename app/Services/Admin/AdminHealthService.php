<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin Health Service — L305
 * Real system status for A003 Health screen.
 */
class AdminHealthService
{
    public function status(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'queue'    => $this->checkQueue(),
            'storage'  => $this->checkStorage(),
            'telegram' => $this->checkTelegram(),
            'computed_at' => now()->toIso8601String(),
        ];
    }

    private function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $latency = (int) round((microtime(true) - $start) * 1000);
            return [
                'status'     => $latency < 500 ? 'ok' : 'warn',
                'latency_ms' => $latency,
                'message'    => 'Connected',
            ];
        } catch (\Throwable $e) {
            return [
                'status'     => 'fail',
                'latency_ms' => null,
                'message'    => $this->safeMessage($e),
            ];
        }
    }

    private function checkCache(): array
    {
        $start = microtime(true);
        try {
            $key = 'health_check_' . uniqid();
            Cache::put($key, 'ok', 5);
            $value = Cache::get($key);
            Cache::forget($key);

            $latency = (int) round((microtime(true) - $start) * 1000);
            return [
                'status'     => $value === 'ok' ? 'ok' : 'warn',
                'latency_ms' => $latency,
                'message'    => $value === 'ok' ? 'Working' : 'Read/Write mismatch',
                'driver'     => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'     => 'fail',
                'latency_ms' => null,
                'message'    => $this->safeMessage($e),
                'driver'     => config('cache.default'),
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $driver = config('queue.default');
            $size = null;
            if ($driver === 'database') {
                $size = DB::table('jobs')->count();
            }
            return [
                'status'     => 'ok',
                'latency_ms' => null,
                'message'    => $size !== null ? "Pending: {$size}" : 'Driver: ' . $driver,
                'driver'     => $driver,
                'size'       => $size,
            ];
        } catch (\Throwable $e) {
            return [
                'status'     => 'warn',
                'latency_ms' => null,
                'message'    => $this->safeMessage($e),
                'driver'     => config('queue.default'),
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $disk = config('filesystems.default', 'local');
            $root = storage_path('app');

            $free  = @disk_free_space($root);
            $total = @disk_total_space($root);

            if ($free === false || $total === false || $total === 0) {
                return [
                    'status'     => 'warn',
                    'latency_ms' => null,
                    'message'    => 'Disk metrics unavailable',
                    'disk'       => $disk,
                ];
            }

            $usedPct = (int) round((($total - $free) / $total) * 100);
            return [
                'status'      => $usedPct < 90 ? 'ok' : 'warn',
                'latency_ms'  => null,
                'message'     => "{$usedPct}% used",
                'disk'        => $disk,
                'used_pct'    => $usedPct,
                'free_bytes'  => (int) $free,
                'total_bytes' => (int) $total,
            ];
        } catch (\Throwable $e) {
            return [
                'status'     => 'warn',
                'latency_ms' => null,
                'message'    => $this->safeMessage($e),
                'disk'       => config('filesystems.default', 'local'),
            ];
        }
    }

    private function checkTelegram(): array
    {
        $configured = ! empty(config('services.telegram.bot_token'))
                   && ! empty(config('services.telegram.bot_username'));

        return [
            'status'     => $configured ? 'ok' : 'unknown',
            'latency_ms' => null,
            'message'    => $configured
                ? 'Configured: ' . config('services.telegram.bot_username')
                : 'Not configured',
        ];
    }

    private function safeMessage(\Throwable $e): string
    {
        return substr($e->getMessage(), 0, 200);
    }
}

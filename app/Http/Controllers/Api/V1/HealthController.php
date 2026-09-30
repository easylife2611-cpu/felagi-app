<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Public health check — no auth required.
 * Used by uptime monitors, load balancers, and ops tooling.
 */
class HealthController extends BaseApiController
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'storage'  => $this->checkStorage(),
            'queue'    => $this->checkQueue(),
        ];

        $healthy = collect($checks)->every(fn ($c) => $c['status'] === 'ok');
        $status  = $healthy ? 200 : 503;

        return $this->success(
            data: [
                'status'    => $healthy ? 'healthy' : 'degraded',
                'app'       => 'felagi',
                'version'   => config('app.version', '1.4.2'),
                'checks'    => $checks,
                'timestamp' => now()->toIso8601String(),
            ],
            message: $healthy ? 'All systems operational.' : 'One or more checks failed.',
            status: $status
        );
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return ['status' => 'ok'];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => 'db_unreachable'];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . uniqid('', true);
            Cache::put($key, 'ok', 5);
            $value = Cache::get($key);
            Cache::forget($key);
            return $value === 'ok'
                ? ['status' => 'ok', 'driver' => config('cache.default')]
                : ['status' => 'fail', 'error' => 'cache_roundtrip'];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => 'cache_unreachable'];
        }
    }

    private function checkStorage(): array
    {
        try {
            $path = storage_path('app');
            if (!is_dir($path)) {
                return ['status' => 'fail', 'error' => 'storage_missing'];
            }
            $testFile = $path . '/.health_check';
            file_put_contents($testFile, 'ok');
            $read = file_get_contents($testFile);
            @unlink($testFile);
            return $read === 'ok'
                ? ['status' => 'ok']
                : ['status' => 'fail', 'error' => 'storage_not_writable'];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => 'storage_unreachable'];
        }
    }

    private function checkQueue(): array
    {
        // Note: no jobs table in current schema — report driver only
        try {
            return ['status' => 'ok', 'driver' => config('queue.default')];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => 'queue_unreachable'];
        }
    }
}

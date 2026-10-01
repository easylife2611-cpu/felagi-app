<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

/**
 * Admin Jobs Service — L306
 * Real queue status for A012 Jobs screen.
 */
class AdminJobsService
{
    public function status(): array
    {
        return [
            'driver'    => config('queue.default'),
            'pending'   => $this->countPending(),
            'failed'    => $this->countFailed(),
            'processed' => $this->countProcessed(),
            'recent_failed' => $this->recentFailed(5),
            'computed_at'   => now()->toIso8601String(),
        ];
    }

    private function countPending(): int
    {
        try {
            if (config('queue.default') === 'database') {
                return (int) DB::table('jobs')->count();
            }
            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function countFailed(): int
    {
        try {
            if (config('queue.default') === 'database') {
                return (int) DB::table('failed_jobs')->count();
            }
            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function countProcessed(): int
    {
        try {
            if (config('queue.default') === 'database') {
                return (int) DB::table('jobs')->whereNotNull('reserved_at')->count();
            }
            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function recentFailed(int $limit): array
    {
        try {
            if (config('queue.default') !== 'database') return [];

            return DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit($limit)
                ->get(['id', 'connection', 'queue', 'failed_at'])
                ->map(fn($j) => [
                    'id'         => $j->id,
                    'connection' => $j->connection,
                    'queue'      => $j->queue,
                    'failed_at'  => $j->failed_at,
                ])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

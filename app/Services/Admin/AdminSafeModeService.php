<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin Safe Mode Service — L307
 * Real safe-mode status for A019 Safe Mode screen.
 */
class AdminSafeModeService
{
    public const CACHE_KEY = 'felagi.safe_mode.enabled';
    public const REASON_KEY = 'felagi.safe_mode.reason';

    public function status(): array
    {
        return [
            'enabled'         => $this->isEnabled(),
            'reason'          => $this->reason(),
            'affected'        => $this->affectedFeatures(),
            'checklist'       => $this->checklist(),
            'since'           => $this->since(),
            'computed_at'     => now()->toIso8601String(),
        ];
    }

    public function isEnabled(): bool
    {
        try {
            return (bool) Cache::get(self::CACHE_KEY, false);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function reason(): ?string
    {
        try {
            return Cache::get(self::REASON_KEY);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function since(): ?string
    {
        try {
            return Cache::get(self::REASON_KEY . '.since');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function enable(string $reason): bool
    {
        try {
            Cache::forever(self::CACHE_KEY, true);
            Cache::forever(self::REASON_KEY, $reason);
            Cache::forever(self::REASON_KEY . '.since', now()->toIso8601String());
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function disable(): bool
    {
        try {
            Cache::forget(self::CACHE_KEY);
            Cache::forget(self::REASON_KEY);
            Cache::forget(self::REASON_KEY . '.since');
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function affectedFeatures(): array
    {
        return [
            ['feature' => 'New Need creation',       'blocked' => true],
            ['feature' => 'New Offer submission',    'blocked' => true],
            ['feature' => 'AI Comparison',           'blocked' => true],
            ['feature' => 'Sponsored Ads delivery',  'blocked' => true],
            ['feature' => 'Telegram notifications',  'blocked' => true],
            ['feature' => 'Existing user login',     'blocked' => false],
            ['feature' => 'Read existing content',   'blocked' => false],
            ['feature' => 'Admin access',            'blocked' => false],
        ];
    }

    private function checklist(): array
    {
        return [
            ['item' => 'Database connection',   'status' => $this->checkDb()],
            ['item' => 'Cache operational',     'status' => $this->checkCache()],
            ['item' => 'Queue operational',     'status' => $this->checkQueue()],
            ['item' => 'Storage writable',      'status' => $this->checkStorage()],
        ];
    }

    private function checkDb(): string
    {
        try { DB::select('SELECT 1'); return 'ok'; }
        catch (\Throwable $e) { return 'fail'; }
    }

    private function checkCache(): string
    {
        try {
            Cache::put('safe_mode_check', 'ok', 5);
            $v = Cache::get('safe_mode_check');
            Cache::forget('safe_mode_check');
            return $v === 'ok' ? 'ok' : 'warn';
        } catch (\Throwable $e) { return 'fail'; }
    }

    private function checkQueue(): string
    {
        try {
            $driver = config('queue.default');
            if ($driver === 'database') {
                DB::table('jobs')->count();
            }
            return 'ok';
        } catch (\Throwable $e) { return 'warn'; }
    }

    private function checkStorage(): string
    {
        return is_writable(storage_path('app')) ? 'ok' : 'fail';
    }
}

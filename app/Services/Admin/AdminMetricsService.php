<?php

namespace App\Services\Admin;

use App\Models\Comparison;
use App\Models\Need;
use App\Models\Offer;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin Metrics Service — L305
 * Real aggregated metrics for A001 Dashboard.
 * Cached 60s to avoid hammering DB on every page load.
 */
class AdminMetricsService
{
    public const CACHE_TTL = 60;

    public function summary(): array
    {
        return Cache::remember('admin.metrics.summary', self::CACHE_TTL, function () {
            return [
                'users'     => $this->countUsers(),
                'needs'     => $this->countNeeds(),
                'offers'    => $this->countOffers(),
                'reports'   => $this->countReports(),
                'computed_at' => now()->toIso8601String(),
            ];
        });
    }

    public function recentActivity(int $limit = 10): array
    {
        return [
            'recent_users'   => $this->recentUsers($limit),
            'recent_needs'   => $this->recentNeeds($limit),
            'recent_offers'  => $this->recentOffers($limit),
        ];
    }

    private function countUsers(): int
    {
        try {
            return (int) User::whereNull('deleted_at')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function countNeeds(): int
    {
        try {
            return (int) Need::count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function countOffers(): int
    {
        try {
            return (int) Offer::count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function countReports(): int
    {
        try {
            return (int) Report::count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function recentUsers(int $limit): array
    {
        try {
            return User::orderByDesc('created_at')->limit($limit)
                ->get(['id', 'name', 'full_name', 'email', 'telegram_subject', 'created_at'])
                ->map(fn($u) => [
                    'id'         => $u->id,
                    'name'       => $u->name ?? $u->full_name ?? $u->email ?? 'User',
                    'created_at' => $u->created_at?->toIso8601String(),
                ])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function recentNeeds(int $limit): array
    {
        try {
            return Need::orderByDesc('created_at')->limit($limit)
                ->get()
                ->map(fn($n) => [
                    'id'         => $n->id,
                    'title'      => $n->title ?? 'Need',
                    'created_at' => $n->created_at?->toIso8601String(),
                ])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function recentOffers(int $limit): array
    {
        try {
            return Offer::orderByDesc('created_at')->limit($limit)
                ->get()
                ->map(fn($o) => [
                    'id'         => $o->id,
                    'created_at' => $o->created_at?->toIso8601String(),
                ])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

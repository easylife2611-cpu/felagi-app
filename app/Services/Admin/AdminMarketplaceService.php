<?php

namespace App\Services\Admin;

use App\Models\Report;
use App\Models\Setting;

/**
 * Admin Marketplace Status Service — L312 (A005)
 *
 * Real marketplace state for A005 Marketplace screen.
 * Per WP-05c_LOCKED: endpoint = /admin/marketplace, control = maxOffers.
 *
 * Settings keys come from ControlRegistrySeeder with documented defaults
 * (mirrors L310/L311 DB-or-default pattern).
 */
class AdminMarketplaceService
{
    public const SETTING_MAX_OFFERS        = 'marketplace.max_offers_per_comparison';
    public const SETTING_MAX_OPEN_NEEDS    = 'marketplace.max_open_needs_per_user';
    public const SETTING_OFFER_DEADLINE    = 'marketplace.offer_deadline_max_days';

    public const DEFAULT_MAX_OFFERS     = 20;
    public const DEFAULT_MAX_OPEN_NEEDS = 20;
    public const DEFAULT_OFFER_DEADLINE = 90;

    public function status(): array
    {
        return [
            'settings'       => $this->settings(),
            'report_stats'   => $this->reportStats(),
            'recent_reports' => $this->recentReports(),
            'computed_at'    => now()->toIso8601String(),
        ];
    }

    private function settings(): array
    {
        $maxOffers   = Setting::find(self::SETTING_MAX_OFFERS);
        $maxOpen     = Setting::find(self::SETTING_MAX_OPEN_NEEDS);
        $deadline    = Setting::find(self::SETTING_OFFER_DEADLINE);

        return [
            'max_offers_per_comparison' => (int) ($maxOffers?->value_json['value'] ?? self::DEFAULT_MAX_OFFERS),
            'max_open_needs_per_user'   => (int) ($maxOpen?->value_json['value'] ?? self::DEFAULT_MAX_OPEN_NEEDS),
            'offer_deadline_max_days'   => (int) ($deadline?->value_json['value'] ?? self::DEFAULT_OFFER_DEADLINE),
            'source'                    => $maxOffers ? 'db' : 'default',
        ];
    }

    private function reportStats(): array
    {
        return [
            'open'      => $this->safe(fn () => Report::where('status', Report::STATUS_OPEN)->count()),
            'in_review' => $this->safe(fn () => Report::where('status', Report::STATUS_IN_REVIEW)->count()),
            'resolved'  => $this->safe(fn () => Report::where('status', Report::STATUS_RESOLVED)->count()),
            'dismissed' => $this->safe(fn () => Report::where('status', Report::STATUS_DISMISSED)->count()),
            'total'     => $this->safe(fn () => Report::count()),
        ];
    }

    private function recentReports(): array
    {
        return $this->safe(fn () => Report::orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'entity_type', 'reason_code', 'status', 'assigned_to', 'resolution_code', 'created_at'])
            ->map(fn ($r) => [
                'id'          => $r->id,
                'entity_type' => $r->entity_type,
                'reason_code' => $r->reason_code,
                'status'      => $r->status,
                'assigned'    => $r->assigned_to ? '[assigned]' : null,
                'resolution'  => $r->resolution_code,
                'created_at'  => $r->created_at?->toIso8601String(),
            ])->all(), []);
    }

    private function safe(callable $fn, mixed $fallback = 0): mixed
    {
        try { return $fn(); } catch (\Throwable) { return $fallback; }
    }
}

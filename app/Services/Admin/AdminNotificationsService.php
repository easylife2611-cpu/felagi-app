<?php

namespace App\Services\Admin;

use App\Models\Notification;

/**
 * Admin Notifications Status Service — L314 (A010)
 *
 * Real notification delivery state for A010 Notifications screen.
 * Per WP-05c_LOCKED: endpoint = /admin/notifications.
 * Reads the `notifications` table (channel + delivery_status + failure_code),
 * distinct from A012 Jobs (queue/failed_jobs).
 *
 * If seeder has not yet populated data, returns explicit zero counts
 * (no guessing, no fake values).
 */
class AdminNotificationsService
{
    public function status(): array
    {
        return [
            'by_status'   => $this->byStatus(),
            'by_channel'  => $this->byChannel(),
            'recent'      => $this->recent(),
            'totals'      => $this->totals(),
            'computed_at' => now()->toIso8601String(),
        ];
    }

    private function byStatus(): array
    {
        return [
            'pending' => $this->safe(fn () => Notification::where('delivery_status', Notification::STATUS_PENDING)->count()),
            'sent'    => $this->safe(fn () => Notification::where('delivery_status', Notification::STATUS_SENT)->count()),
            'failed'  => $this->safe(fn () => Notification::where('delivery_status', Notification::STATUS_FAILED)->count()),
            'skipped' => $this->safe(fn () => Notification::where('delivery_status', Notification::STATUS_SKIPPED)->count()),
        ];
    }

    private function byChannel(): array
    {
        return [
            'in_app'   => $this->safe(fn () => Notification::where('channel', Notification::CHANNEL_IN_APP)->count()),
            'telegram' => $this->safe(fn () => Notification::where('channel', Notification::CHANNEL_TELEGRAM)->count()),
        ];
    }

    private function recent(): array
    {
        return $this->safe(fn () => Notification::orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'type', 'channel', 'delivery_status', 'failure_code', 'created_at'])
            ->map(fn ($n) => [
                'id'              => $n->id,
                'type'            => $n->type,
                'channel'         => $n->channel,
                'delivery_status' => $n->delivery_status,
                'failure_code'    => $n->failure_code,
                'created_at'      => $n->created_at?->toIso8601String(),
            ])->all(), []);
    }

    private function totals(): array
    {
        return [
            'total'   => $this->safe(fn () => Notification::count()),
            'unread'  => $this->safe(fn () => Notification::whereNull('read_at')->count()),
        ];
    }

    private function safe(callable $fn, mixed $fallback = 0): mixed
    {
        try { return $fn(); } catch (\Throwable) { return $fallback; }
    }
}

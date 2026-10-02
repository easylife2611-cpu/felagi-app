<?php

namespace App\Services\Admin;

use App\Models\AuthAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Admin Security Service — L307
 * Real security status for A015 Security screen.
 */
class AdminSecurityService
{
    public function status(): array
    {
        return [
            'total_users'         => $this->totalUsers(),
            'totp_enabled'        => $this->totpEnabled(),
            'suspended'           => $this->suspended(),
            'admin_count'         => $this->adminCount(),
            'recent_attempts'     => $this->recentAttempts(10),
            'attempts_24h'        => $this->attempts24h(),
            'computed_at'         => now()->toIso8601String(),
        ];
    }

    private function totalUsers(): int
    {
        try { return (int) User::whereNull('deleted_at')->count(); }
        catch (\Throwable $e) { return 0; }
    }

    private function totpEnabled(): int
    {
        try { return (int) User::whereNotNull('totp_enabled_at')->count(); }
        catch (\Throwable $e) { return 0; }
    }

    private function suspended(): int
    {
        try { return (int) User::where('status', 'SUSPENDED')->count(); }
        catch (\Throwable $e) { return 0; }
    }

    private function adminCount(): int
    {
        try {
            return (int) DB::table('user_roles')
                ->whereIn('role', ['MAIN_ADMIN', 'ADMIN', 'MODERATOR'])
                ->whereNull('revoked_at')
                ->count();
        } catch (\Throwable $e) { return 0; }
    }

    private function attempts24h(): int
    {
        try {
            return (int) AuthAttempt::where('created_at', '>=', now()->subHours(24))->count();
        } catch (\Throwable $e) { return 0; }
    }

    private function recentAttempts(int $limit): array
    {
        try {
            return AuthAttempt::orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(fn($a) => [
                    'id'          => $a->id,
                    'return_uri'  => $a->return_uri_allowlisted,
                    'consumed'    => $a->consumed_at !== null,
                    'expired'     => $a->expires_at && $a->expires_at->isPast(),
                    'created_at'  => $a->created_at?->toIso8601String(),
                ])
                ->toArray();
        } catch (\Throwable $e) { return []; }
    }
}

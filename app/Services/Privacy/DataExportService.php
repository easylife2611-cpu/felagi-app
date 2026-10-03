<?php

namespace App\Services\Privacy;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Data Portability — Proclamation 1321/2024, Art. 38
 */
class DataExportService
{
    public function build(User $user): array
    {
        return [
            'exported_at' => now()->toIso8601String(),
            'format'      => 'json',
            'version'     => '1.0',
            'subject'     => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'email'      => $user->email,
                'created_at' => $user->created_at,
            ],
            'needs'    => $this->safeQuery('needs', $user->id),
            'offers'   => $this->safeQuery('offers', $user->id),
            'consents' => $this->safeQuery('consent_logs', $user->id),
        ];
    }

    /** Column map — single source of truth for user-ownership columns. */
    private const USER_COLUMN = [
        'needs'        => 'requester_id',
        'offers'       => 'provider_id',
        'consent_logs' => 'user_id',
    ];

    private function safeQuery(string $table, string $userId): array
    {
        try {
            $column = self::USER_COLUMN[$table] ?? 'user_id';
            return DB::table($table)
                ->where($column, $userId)
                ->limit(1000)
                ->get()
                ->map(fn($row) => (array) $row)
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

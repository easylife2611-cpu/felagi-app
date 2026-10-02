<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\File;

/**
 * Admin Recovery Service — L307
 * Real recovery status for A018 Recovery screen.
 */
class AdminRecoveryService
{
    public function status(): array
    {
        $backupDir = storage_path('app/backups');

        return [
            'available_restore_points' => $this->countRestorePoints($backupDir),
            'last_backup'              => $this->lastBackup($backupDir),
            'recent_restore_points'    => $this->recentRestorePoints($backupDir, 10),
            'recovery_steps'           => $this->recoverySteps(),
            'computed_at'              => now()->toIso8601String(),
        ];
    }

    private function countRestorePoints(string $dir): int
    {
        if (! is_dir($dir)) return 0;
        return count(File::glob($dir . '/*.{zip,sql,gz,tar}', GLOB_BRACE));
    }

    private function lastBackup(string $dir): ?array
    {
        if (! is_dir($dir)) return null;
        $files = File::glob($dir . '/*.{zip,sql,gz,tar}', GLOB_BRACE);
        if (empty($files)) return null;

        usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
        $latest = $files[0];

        return [
            'name' => basename($latest),
            'size' => filesize($latest),
            'date' => date('c', filemtime($latest)),
        ];
    }

    private function recentRestorePoints(string $dir, int $limit): array
    {
        if (! is_dir($dir)) return [];
        $files = File::glob($dir . '/*.{zip,sql,gz,tar}', GLOB_BRACE);
        usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));

        return array_map(fn($f) => [
            'name' => basename($f),
            'size' => filesize($f),
            'date' => date('c', filemtime($f)),
        ], array_slice($files, 0, $limit));
    }

    private function recoverySteps(): array
    {
        return [
            ['step' => 1, 'label' => 'Select restore point', 'status' => 'pending'],
            ['step' => 2, 'label' => 'Preview affected data', 'status' => 'pending'],
            ['step' => 3, 'label' => 'Confirm & backup current state', 'status' => 'pending'],
            ['step' => 4, 'label' => 'Execute restore', 'status' => 'pending'],
            ['step' => 5, 'label' => 'Verify integrity', 'status' => 'pending'],
        ];
    }
}

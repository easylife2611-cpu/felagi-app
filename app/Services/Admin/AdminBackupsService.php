<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\File;

/**
 * Admin Backups Service — L306
 * Real backup status for A013 Backups screen.
 */
class AdminBackupsService
{
    public function status(): array
    {
        $dir = storage_path('app/backups');

        return [
            'directory'      => $dir,
            'exists'         => is_dir($dir),
            'writable'       => is_dir($dir) ? is_writable($dir) : false,
            'total_backups'  => $this->countBackups($dir),
            'total_size'     => $this->totalSize($dir),
            'last_backup'    => $this->lastBackup($dir),
            'recent_backups' => $this->recentBackups($dir, 10),
            'computed_at'    => now()->toIso8601String(),
        ];
    }

    private function countBackups(string $dir): int
    {
        if (! is_dir($dir)) return 0;
        return count(File::glob($dir . '/*.{zip,sql,gz,tar}', GLOB_BRACE));
    }

    private function totalSize(string $dir): int
    {
        if (! is_dir($dir)) return 0;
        $total = 0;
        foreach (File::glob($dir . '/*.{zip,sql,gz,tar}', GLOB_BRACE) as $f) {
            $total += filesize($f);
        }
        return $total;
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

    private function recentBackups(string $dir, int $limit): array
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
}

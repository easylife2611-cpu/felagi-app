<?php

namespace App\Console\Commands;

use App\Services\Admin\BoostPackageSyncService;
use Illuminate\Console\Command;

class SyncBoostPackages extends Command
{
    protected $signature = 'boost-packages:sync';
    protected $description = 'Sync boost_packages table from the canonical boostPackages Setting (A020)';

    public function handle(BoostPackageSyncService $service): int
    {
        $result = $service->sync();

        if (!empty($result['error'])) {
            $this->error('Sync failed: ' . $result['error']);
            return self::FAILURE;
        }

        $this->info("Created:      {$result['created']}");
        $this->info("Updated:      {$result['updated']}");
        $this->info("Deactivated:  {$result['deactivated']}");
        $this->info("Canonical:    " . implode(', ', $result['canonical_durations']) . ' day(s)');
        return self::SUCCESS;
    }
}

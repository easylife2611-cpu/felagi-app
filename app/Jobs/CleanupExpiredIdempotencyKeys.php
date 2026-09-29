<?php

namespace App\Jobs;

use App\Services\Admin\IdempotencyRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Clean up expired idempotency keys.
 *
 * Runs via scheduler (every 1 hour). Uses the registry's cleanup()
 * method which deletes keys where expires_at < now().
 */
class CleanupExpiredIdempotencyKeys implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 60;

    public function handle(IdempotencyRegistry $registry): void
    {
        $deleted = $registry->cleanup();
        Log::info("Idempotency cleanup: {$deleted} rows deleted");
    }
}

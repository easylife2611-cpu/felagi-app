<?php

use App\Jobs\CleanupExpiredIdempotencyKeys;
use App\Jobs\ProcessOutboxEvent;
use App\Models\OutboxEvent;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── WP-13b Scheduler ───

// Process pending outbox events (bounded, short-lived)
Schedule::call(function () {
    OutboxEvent::query()
        ->where('status', OutboxEvent::STATUS_PENDING)
        ->where('available_at', '<=', now())
        ->where(function ($q) {
            $q->whereNull('locked_until')
              ->orWhere('locked_until', '<', now());
        })
        ->orderBy('available_at')
        ->limit(50)
        ->get()
        ->each(fn ($event) => ProcessOutboxEvent::dispatch($event->id));
})->everyMinute()->name('outbox-dispatch')->withoutOverlapping();

// Cleanup expired idempotency keys
Schedule::job(new CleanupExpiredIdempotencyKeys())
    ->hourly()
    ->name('idempotency-cleanup');

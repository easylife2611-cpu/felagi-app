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



// ─── AG (audit L276) — Scheduled admin changes ───

Artisan::command('settings:apply-scheduled', function () {
    $due = \App\Models\SettingDraft::query()
        ->where('scheduled_status', \App\Models\SettingDraft::SCHEDULE_PENDING)
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->orderBy('scheduled_at')
        ->limit(50)
        ->get();

    if ($due->isEmpty()) {
        $this->info('No scheduled changes due.');
        return 0;
    }

    $this->info("Applying {$due->count()} scheduled change(s)...");

    foreach ($due as $draft) {
        \App\Jobs\ApplyScheduledChange::dispatchSync($draft->id);
        $this->line("  applied draft {$draft->id} ({$draft->setting_key})");
    }

    return 0;
})->purpose('Apply any due scheduled admin changes');

// Dispatch the scheduler every minute
Schedule::command('settings:apply-scheduled')
    ->everyMinute()
    ->name('scheduled-admin-changes')
    ->withoutOverlapping();

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

// ─── AC (audit L276) — Config drift detection ───

Artisan::command('config:drift {--json}', function () {
    /** @var \App\Services\Admin\ConfigDriftDetector $detector */
    $detector = app(\App\Services\Admin\ConfigDriftDetector::class);
    $report   = $detector->detect();

    if ($this->option('json')) {
        $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return 0;
    }

    $this->info("Config drift report — " . $report['checked_at']);
    $this->line("Settings: {$report['total_settings']}   Versions: {$report['total_versions']}");
    $this->line("Findings: {$report['drift_count']}");
    $this->newLine();

    if ($report['drift_count'] === 0) {
        $this->info('No drift detected. Configuration matches published versions.');
        return 0;
    }

    $this->warn('Summary:');
    foreach ($report['summary'] as $kind => $count) {
        if ($count > 0) {
            $this->line("  {$kind}: {$count}");
        }
    }
    $this->newLine();

    $this->warn('Findings:');
    foreach ($report['findings'] as $f) {
        $this->line(sprintf(
            '  [%s] %s (v%s)',
            $f['kind'],
            $f['setting_key'],
            $f['version'] ?? '—'
        ));
    }

    return $report['drift_count'] > 0 ? 1 : 0;
})->purpose('Detect config drift between settings and their latest published versions');


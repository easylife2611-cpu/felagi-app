<?php

namespace App\Console\Commands;

use App\Models\BreachIncident;
use App\Services\Privacy\BreachNotificationService;
use Illuminate\Console\Command;

/**
 * Check Breach Deadlines — Proclamation 1321/2024, Art. 30
 * Alerts on incidents approaching or past 72-hour deadline
 */
class CheckBreachDeadlines extends Command
{
    protected $signature = 'breach:check-deadlines';
    protected $description = 'Check breach notification deadlines (72h)';

    public function handle(BreachNotificationService $service): int
    {
        $overdue = $service->overdueIncidents();

        if ($overdue->isEmpty()) {
            $this->info('No overdue breach incidents.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  {$overdue->count()} overdue incident(s):");
        $this->newLine();

        foreach ($overdue as $incident) {
            $this->line(sprintf(
                '  [%s] #%d %s — detected %s (%d hours ago)',
                $incident->severity,
                $incident->id,
                $incident->title,
                $incident->detected_at->format('Y-m-d H:i'),
                $incident->detected_at->diffInHours(now())
            ));
        }

        return self::FAILURE;
    }
}

<?php

namespace App\Services\Privacy;

use App\Mail\BreachNoticeMail;
use App\Models\BreachIncident;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Breach Notification — Proclamation 1321/2024, Art. 30
 * 72-hour deadline to ECA + affected users
 */
class BreachNotificationService
{
    public function report(array $data, ?int $reportedBy = null): BreachIncident
    {
        return BreachIncident::create([
            'title'            => $data['title'],
            'description'      => $data['description'],
            'severity'         => $data['severity'],
            'status'           => BreachIncident::STATUS_DETECTED,
            'data_categories'  => $data['data_categories'] ?? [],
            'affected_count'   => $data['affected_count'] ?? 0,
            'detected_at'      => $data['detected_at'] ?? now(),
            'reported_by'      => $reportedBy,
        ]);
    }

    public function markContained(BreachIncident $incident): BreachIncident
    {
        $incident->update([
            'status'       => BreachIncident::STATUS_CONTAINED,
            'contained_at' => now(),
        ]);
        return $incident->fresh();
    }

    public function notifyEca(BreachIncident $incident): BreachIncident
    {
        // In production: send to ECA via registered channel
        Log::info('ECA notified of breach', [
            'incident_id' => $incident->id,
            'severity'    => $incident->severity,
            'detected_at' => $incident->detected_at->toIso8601String(),
        ]);

        $incident->update([
            'status'          => BreachIncident::STATUS_NOTIFIED,
            'eca_notified_at' => now(),
        ]);

        return $incident->fresh();
    }

    public function notifyAffectedUsers(BreachIncident $incident, array $userIds = []): int
    {
        $count = 0;
        $query = User::query();

        if (! empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        $query->chunkById(100, function ($users) use ($incident, &$count) {
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(
                        new BreachNoticeMail($incident, $user->name ?? 'User')
                    );
                    $count++;
                } catch (\Throwable $e) {
                    Log::error('Breach notice failed', [
                        'user_id' => $user->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        });

        $incident->update([
            'users_notified_at' => now(),
            'affected_count'    => max($incident->affected_count, $count),
        ]);

        return $count;
    }

    public function resolve(BreachIncident $incident, string $remediation): BreachIncident
    {
        $incident->update([
            'status'      => BreachIncident::STATUS_RESOLVED,
            'resolved_at' => now(),
            'remediation' => $remediation,
        ]);
        return $incident->fresh();
    }

    public function overdueIncidents()
    {
        return BreachIncident::whereNull('eca_notified_at')
            ->where('detected_at', '<', now()->subHours(BreachIncident::HOURS_DEADLINE))
            ->get();
    }
}

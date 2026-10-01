<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Breach Incident — Proclamation 1321/2024, Art. 30
 */
class BreachIncident extends Model
{
    protected $fillable = [
        'title', 'description', 'severity', 'status',
        'data_categories', 'affected_count',
        'detected_at', 'contained_at',
        'eca_notified_at', 'users_notified_at', 'resolved_at',
        'reported_by', 'remediation',
    ];

    protected $casts = [
        'data_categories'    => 'array',
        'affected_count'     => 'integer',
        'detected_at'        => 'datetime',
        'contained_at'       => 'datetime',
        'eca_notified_at'    => 'datetime',
        'users_notified_at'  => 'datetime',
        'resolved_at'        => 'datetime',
    ];

    public const SEV_P1 = 'P1';
    public const SEV_P2 = 'P2';
    public const SEV_P3 = 'P3';
    public const SEV_P4 = 'P4';

    public const STATUS_DETECTED    = 'detected';
    public const STATUS_CONTAINED   = 'contained';
    public const STATUS_NOTIFIED    = 'notified';
    public const STATUS_RESOLVED    = 'resolved';

    public const HOURS_DEADLINE = 72;

    public function isDeadlineExceeded(): bool
    {
        if ($this->eca_notified_at) {
            return false;
        }
        return $this->detected_at->diffInHours(now()) > self::HOURS_DEADLINE;
    }

    public function hoursUntilDeadline(): int
    {
        $elapsed = $this->detected_at->diffInHours(now());
        return max(0, self::HOURS_DEADLINE - $elapsed);
    }

    public function requiresEcaNotification(): bool
    {
        return in_array($this->severity, [self::SEV_P1, self::SEV_P2], true);
    }

    public function requiresUserNotification(): bool
    {
        return $this->severity === self::SEV_P1;
    }
}

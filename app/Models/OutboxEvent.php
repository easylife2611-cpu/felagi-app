<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OutboxEvent extends Model
{
    use HasFactory;

    use HasUuids;

    /** outbox_events has created_at + completed_at, no updated_at */
    public $timestamps = false;

    protected $fillable = [
        'id', 'event_type', 'aggregate_type', 'aggregate_id',
        'event_key', 'payload_json', 'status', 'attempts',
        'available_at', 'locked_until', 'created_at', 'completed_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'attempts'     => 'integer',
        'available_at' => 'datetime',
        'locked_until' => 'datetime',
        'created_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING    = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_DONE       = 'DONE';
    public const STATUS_FAILED     = 'FAILED';

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                     ->where('available_at', '<=', now());
    }

    public function markDone(): void
    {
        $this->status = self::STATUS_DONE;
        $this->completed_at = now();
        $this->save();
    }

    public function markFailed(): void
    {
        $this->status = self::STATUS_FAILED;
        $this->attempts = $this->attempts + 1;
        $this->save();
    }
}

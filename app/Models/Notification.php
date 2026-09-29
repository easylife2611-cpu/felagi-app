<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'recipient_user_id',
        'type',
        'entity_type',
        'entity_id',
        'event_id',
        'title',
        'body',
        'channel',
        'delivery_status',
        'available_at',
        'sent_at',
        'read_at',
        'failure_code',
        'created_at',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public const CHANNEL_IN_APP = 'IN_APP';
    public const CHANNEL_TELEGRAM = 'TELEGRAM';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SENT = 'SENT';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_SKIPPED = 'SKIPPED';

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function outboxEvent()
    {
        return $this->belongsTo(OutboxEvent::class, 'event_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopePending($query)
    {
        return $query->where('delivery_status', self::STATUS_PENDING);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->read_at = now();
            $this->save();
        }
    }
}

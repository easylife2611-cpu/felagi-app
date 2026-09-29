<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TelegramPublication extends Model
{
    use HasUuids;

    protected $fillable = [
        'need_id', 'destination_id', 'need_publication_version',
        'content_hash', 'payload_snapshot', 'state',
        'telegram_message_id', 'attempts', 'next_attempt_at',
        'posted_at', 'removed_at', 'last_error_code',
    ];

    protected $casts = [
        'payload_snapshot' => 'array',
        'attempts'         => 'integer',
        'next_attempt_at'  => 'datetime',
        'posted_at'        => 'datetime',
        'removed_at'       => 'datetime',
        'telegram_message_id' => 'integer',
    ];

    public const STATE_QUEUED           = 'QUEUED';
    public const STATE_SENDING          = 'SENDING';
    public const STATE_POSTED           = 'POSTED';
    public const STATE_RETRY            = 'RETRY';
    public const STATE_UNCERTAIN        = 'UNCERTAIN';
    public const STATE_FAILED           = 'FAILED';
    public const STATE_SKIPPED          = 'SKIPPED';
    public const STATE_REMOVAL_PENDING  = 'REMOVAL_PENDING';
    public const STATE_REMOVED          = 'REMOVED';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function destination()
    {
        return $this->belongsTo(TelegramDestination::class, 'destination_id');
    }

    public function events()
    {
        return $this->hasMany(TelegramPublicationEvent::class, 'publication_id');
    }

    public function scopePending($query)
    {
        return $query->whereIn('state', [
            self::STATE_QUEUED,
            self::STATE_RETRY,
        ]);
    }

    public function scopeUncertain($query)
    {
        return $query->where('state', self::STATE_UNCERTAIN);
    }
}

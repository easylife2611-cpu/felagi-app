<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TelegramPublicationEvent extends Model
{
    use HasUuids;

    /** Append-only — no updates or deletes. */
    public $timestamps = false;

    protected $fillable = [
        'publication_id', 'event_type', 'request_id',
        'telegram_response_code', 'safe_metadata', 'occurred_at',
    ];

    protected $casts = [
        'safe_metadata'           => 'array',
        'telegram_response_code'  => 'integer',
        'occurred_at'             => 'datetime',
    ];

    public const TYPE_QUEUED     = 'QUEUED';
    public const TYPE_SENT       = 'SENT';
    public const TYPE_FAILED     = 'FAILED';
    public const TYPE_UNCERTAIN  = 'UNCERTAIN';
    public const TYPE_REMOVED    = 'REMOVED';
    public const TYPE_RECONCILED = 'RECONCILED';

    public function publication()
    {
        return $this->belongsTo(TelegramPublication::class, 'publication_id');
    }
}

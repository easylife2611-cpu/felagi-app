<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasUuids;

    protected $fillable = [
        'reporter_id',
        'entity_type',
        'entity_id',
        'reason_code',
        'details',
        'status',
        'assigned_to',
        'resolution_code',
    ];

    public const ENTITY_NEED = 'NEED';
    public const ENTITY_OFFER = 'OFFER';
    public const ENTITY_MESSAGE = 'MESSAGE';
    public const ENTITY_USER = 'USER';

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_IN_REVIEW = 'IN_REVIEW';
    public const STATUS_RESOLVED = 'RESOLVED';
    public const STATUS_DISMISSED = 'DISMISSED';

    public const REASON_SAFETY = 'SAFETY';
    public const REASON_FRAUD = 'FRAUD';
    public const REASON_SPAM = 'SPAM';
    public const REASON_OTHER = 'OTHER';

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}

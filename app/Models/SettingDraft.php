<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingDraft extends Model
{
    use HasFactory, HasUuids;

    /** setting_drafts HAS standard timestamps */
    public $timestamps = true;

    protected $fillable = [
        'id', 'setting_key', 'proposed_value_json', 'proposed_by',
        'status', 'validation_report', 'impact_preview',
        'scheduled_at', 'scheduled_status', 'scheduled_processed_at',
    ];

    protected $casts = [
        'proposed_value_json' => 'array',
        'validation_report'   => 'array',
        'impact_preview'      => 'array',
        'scheduled_at'        => 'datetime',
        'scheduled_processed_at' => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public const STATUS_DRAFT     = 'DRAFT';
    public const STATUS_VALIDATED = 'VALIDATED';
    public const STATUS_REJECTED  = 'REJECTED';
    public const STATUS_PUBLISHED = 'PUBLISHED';

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_key', 'key');
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_VALIDATED], true);
    }

    public const SCHEDULE_NONE      = 'NONE';
    public const SCHEDULE_PENDING   = 'PENDING';
    public const SCHEDULE_APPLIED   = 'APPLIED';
    public const SCHEDULE_CANCELLED = 'CANCELLED';
    public const SCHEDULE_FAILED    = 'FAILED';

    public function isScheduled(): bool
    {
        return $this->scheduled_status === self::SCHEDULE_PENDING
            && $this->scheduled_at !== null;
    }

    public function isDue(): bool
    {
        return $this->isScheduled()
            && $this->scheduled_at->lte(now());
    }
}

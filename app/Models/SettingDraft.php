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
    ];

    protected $casts = [
        'proposed_value_json' => 'array',
        'validation_report'   => 'array',
        'impact_preview'      => 'array',
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
}

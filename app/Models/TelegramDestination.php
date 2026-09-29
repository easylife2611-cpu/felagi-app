<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TelegramDestination extends Model
{
    use HasUuids;

    protected $fillable = [
        'telegram_chat_id', 'public_username', 'type', 'name',
        'allowed_category_ids', 'permission_evidence',
        'granted_at', 'reviewed_at', 'expires_at',
        'bot_permission_checked_at', 'status', 'daily_cap',
        'quiet_hours', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'allowed_category_ids'        => 'array',
        'permission_evidence'         => 'array',
        'quiet_hours'                 => 'array',
        'granted_at'                  => 'datetime',
        'reviewed_at'                 => 'datetime',
        'expires_at'                  => 'datetime',
        'bot_permission_checked_at'   => 'datetime',
        'daily_cap'                   => 'integer',
    ];

    public const TYPE_OWNED_CHANNEL       = 'OWNED_CHANNEL';
    public const TYPE_PARTNER_CHANNEL     = 'PARTNER_CHANNEL';
    public const TYPE_PARTNER_SUPERGROUP  = 'PARTNER_SUPERGROUP';

    public const STATUS_DRAFT    = 'DRAFT';
    public const STATUS_ACTIVE   = 'ACTIVE';
    public const STATUS_PAUSED   = 'PAUSED';
    public const STATUS_REVOKED  = 'REVOKED';

    public function publications()
    {
        return $this->hasMany(TelegramPublication::class, 'destination_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canPublish(): bool
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }
}

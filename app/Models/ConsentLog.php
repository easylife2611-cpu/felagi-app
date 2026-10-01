<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentLog extends Model
{
    protected $fillable = [
        'user_id', 'consent_type', 'version', 'granted',
        'granted_at', 'revoked_at', 'ip_address',
        'user_agent', 'source', 'meta',
    ];

    protected $casts = [
        'granted'    => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta'       => 'array',
    ];

    public const TYPE_MARKETING    = 'marketing';
    public const TYPE_ADS          = 'ads';
    public const TYPE_AI_COMPARE   = 'ai_compare';
    public const TYPE_TELEGRAM     = 'telegram';
    public const TYPE_CROSS_BORDER = 'cross_border';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('granted', true)->whereNull('revoked_at');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'advertiser_id', 'campaign_name', 'status',
        'start_at', 'end_at', 'timezone',
        'creative_id', 'placement_ids', 'targeting_policy', 'frequency_policy',
        'priority', 'commercial_reference',
        'destination_type', 'destination_value',
        'created_by', 'approved_by',
        'published_at', 'paused_at', 'ended_at', 'version',
    ];

    protected $casts = [
        'placement_ids'    => 'array',
        'targeting_policy' => 'array',
        'frequency_policy' => 'array',
        'start_at'         => 'datetime',
        'end_at'           => 'datetime',
        'published_at'     => 'datetime',
        'paused_at'        => 'datetime',
        'ended_at'         => 'datetime',
        'priority'         => 'integer',
        'version'          => 'integer',
    ];

    // Canonical states — LOCKED by advertising-contract.json
    public const STATUS_DRAFT      = 'DRAFT';
    public const STATUS_VALIDATED  = 'VALIDATED';
    public const STATUS_SCHEDULED  = 'SCHEDULED';
    public const STATUS_ACTIVE     = 'ACTIVE';
    public const STATUS_PAUSED     = 'PAUSED';
    public const STATUS_ENDED      = 'ENDED';
    public const STATUS_ARCHIVED   = 'ARCHIVED';
    public const STATUS_REJECTED   = 'REJECTED';
    public const STATUS_EXPIRED    = 'EXPIRED';
    public const STATUS_BLOCKED    = 'BLOCKED';

    public const ALL_STATUSES = [
        self::STATUS_DRAFT, self::STATUS_VALIDATED, self::STATUS_SCHEDULED,
        self::STATUS_ACTIVE, self::STATUS_PAUSED, self::STATUS_ENDED,
        self::STATUS_ARCHIVED, self::STATUS_REJECTED, self::STATUS_EXPIRED,
        self::STATUS_BLOCKED,
    ];

    // Registered placements — LOCKED
    public const PLACEMENT_BROWSE      = 'AD_BROWSE_INLINE_01';
    public const PLACEMENT_SEARCH      = 'AD_SEARCH_RESULTS_INLINE_01';
    public const PLACEMENT_NEED_DETAIL = 'AD_NEED_DETAIL_BOTTOM_01';

    public const PLACEMENTS = [
        self::PLACEMENT_BROWSE,
        self::PLACEMENT_SEARCH,
        self::PLACEMENT_NEED_DETAIL,
    ];

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function creative()
    {
        return $this->belongsTo(AdCreative::class, 'creative_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deliveries()
    {
        return $this->hasMany(AdDelivery::class, 'campaign_id');
    }

    public function scopeActive($q)
    {
        return $q->where('status', self::STATUS_ACTIVE);
    }

    public function scopeServable($q)
    {
        $now = now();
        return $q->where('status', self::STATUS_ACTIVE)
                 ->where('start_at', '<=', $now)
                 ->where('end_at', '>', $now);
    }
}

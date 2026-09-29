<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Boost extends Model
{
    use HasUuids;

    protected $fillable = [
        'need_id',
        'requester_id',
        'package_id',
        'payment_id',
        'price_snapshot',
        'currency',
        'duration_days',
        'status',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
        'duration_days' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function package()
    {
        return $this->belongsTo(BoostPackage::class, 'package_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('starts_at', '<=', now())
                     ->where('expires_at', '>', now());
    }
}

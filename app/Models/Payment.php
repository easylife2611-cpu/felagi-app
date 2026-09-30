<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'payer_id',
        'need_id',
        'purpose',
        'provider',
        'provider_reference',
        'idempotency_key',
        'amount',
        'currency',
        'status',
        'confirmed_at',
        'failed_at',
        'failure_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public const PURPOSE_BOOST = 'BOOST';
    public const PURPOSE_OFFER_UNLOCK = 'OFFER_UNLOCK';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_REVIEW_REQUIRED = 'REVIEW_REQUIRED';

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function boost()
    {
        return $this->hasOne(Boost::class);
    }

    public function events()
    {
        return $this->hasMany(PaymentEvent::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
}

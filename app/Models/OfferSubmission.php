<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferSubmission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'offer_submissions';

    protected $fillable = [
        'need_id',
        'provider_id',
        'status',
        'payment_id',
        'unlocked_at',
        'expires_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public const STATUS_PENDING_PAYMENT = 'PENDING_PAYMENT';
    public const STATUS_UNLOCKED        = 'UNLOCKED';
    public const STATUS_EXPIRED         = 'EXPIRED';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}

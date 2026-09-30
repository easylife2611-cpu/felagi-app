<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'need_id',
        'provider_id',
        'offered_price',
        'currency',
        'proposal_message',
        'delivery_time_text',
        'availability_text',
        'additional_notes',
        'status',
        'version',
        'accepted_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
        'version' => 'integer',
        'accepted_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_WITHDRAWN = 'WITHDRAWN';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function award()
    {
        return $this->hasOne(NeedAward::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->provider_id === $user->id;
    }
}

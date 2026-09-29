<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Need extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'requester_id',
        'category_id',
        'title',
        'description',
        'location_text',
        'budget_min',
        'budget_max',
        'currency',
        'quantity',
        'deadline_at',
        'offer_deadline_at',
        'status',
        'version',
        'telegram_publication_consent_at',
        'telegram_publication_version',
        'telegram_publication_stopped_at',
        'completed_at',
        'cancelled_at',
        'archived_at',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'quantity' => 'decimal:2',
        'deadline_at' => 'datetime',
        'offer_deadline_at' => 'datetime',
        'telegram_publication_consent_at' => 'datetime',
        'telegram_publication_stopped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'archived_at' => 'datetime',
        'version' => 'integer',
        'telegram_publication_version' => 'integer',
    ];

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function award()
    {
        return $this->hasOne(NeedAward::class);
    }

    public function comparisons()
    {
        return $this->hasMany(Comparison::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function boosts()
    {
        return $this->hasMany(Boost::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopePublished($query)
    {
        return $query->whereNull('archived_at')->whereNull('deleted_at');
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->requester_id === $user->id;
    }
}

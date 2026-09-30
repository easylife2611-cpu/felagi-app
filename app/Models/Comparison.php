<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'need_id',
        'version_number',
        'triggered_by',
        'status',
        'criteria_version',
        'prompt_version',
        'output_schema_version',
        'ai_provider',
        'model_id',
        'need_snapshot',
        'snapshot_hash',
        'eligible_offer_count',
        'included_offer_count',
        'input_token_count',
        'output_token_count',
        'estimated_cost',
        'failure_code',
        'attempt_count',
        'requested_at',
        'started_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'need_snapshot' => 'array',
        'eligible_offer_count' => 'integer',
        'included_offer_count' => 'integer',
        'input_token_count' => 'integer',
        'output_token_count' => 'integer',
        'estimated_cost' => 'decimal:4',
        'attempt_count' => 'integer',
        'version_number' => 'integer',
        'requested_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function comparisonOffers()
    {
        return $this->hasMany(ComparisonOffer::class);
    }

    public function results()
    {
        return $this->hasMany(ComparisonResult::class);
    }

    public function attempts()
    {
        return $this->hasMany(ComparisonAttempt::class);
    }

    public function exports()
    {
        return $this->hasMany(Export::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}

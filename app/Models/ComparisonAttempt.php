<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ComparisonAttempt extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'comparison_id',
        'attempt_number',
        'started_at',
        'finished_at',
        'status',
        'provider_request_id',
        'failure_code',
        'token_usage',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'token_usage' => 'array',
    ];

    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_SUCCEEDED = 'SUCCEEDED';
    public const STATUS_FAILED = 'FAILED';

    public function comparison()
    {
        return $this->belongsTo(Comparison::class);
    }
}

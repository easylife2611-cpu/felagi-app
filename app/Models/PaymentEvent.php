<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    /**
     * Canonical "created" column.
     *
     * DB schema uses `received_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
     * instead of `created_at` — see migration 2026_09_29_000016.
     *
     * Eloquent's `latest()` / `oldest()` use this constant to determine
     * the ordering column. `$timestamps = false` prevents auto-management
     * on insert/update (DB `useCurrent()` fills the value).
     */
    public const CREATED_AT = 'received_at';

    protected $fillable = [
        'payment_id',
        'provider',
        'provider_event_id',
        'payload_digest',
        'received_at',
        'signature_valid',
        'event_type',
        'processing_status',
        'sanitized_metadata',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'received_at' => 'datetime',
        'sanitized_metadata' => 'array',
    ];

    public const STATUS_RECEIVED = 'RECEIVED';
    public const STATUS_APPLIED = 'APPLIED';
    public const STATUS_DUPLICATE = 'DUPLICATE';
    public const STATUS_REVIEW_REQUIRED = 'REVIEW_REQUIRED';
    public const STATUS_REJECTED = 'REJECTED';

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

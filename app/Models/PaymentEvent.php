<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
    use HasUuids;

    public $timestamps = false;

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

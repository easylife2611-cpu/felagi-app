<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * S023 — Offer Submission Unlock.
 * Canonical state machine per System_Specification/Monetization_Payment_Specification.md.
 */
class OfferSubmission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'offer_submissions';

    protected $fillable = [
        'need_id',
        'provider_id',
        'state',
        'draft_id',
        'draft_version',
        'draft_hash',
        'idempotency_key',
        'policy_version',
        'amount_minor',
        'currency',
        'payment_id',
        'offer_id',
        'refund_reference',
        'unlocked_at',
        'expires_at',
    ];

    protected $casts = [
        'draft_version' => 'integer',
        'amount_minor'  => 'integer',
        'unlocked_at'   => 'datetime',
        'expires_at'    => 'datetime',
    ];

    public const STATE_FREE                = 'free';
    public const STATE_PAYMENT_REQUIRED    = 'payment-required';
    public const STATE_PENDING             = 'pending';
    public const STATE_PAYMENT_VERIFIED    = 'payment-verified';
    public const STATE_SUBMISSION_RECOVERY = 'submission-recovery';
    public const STATE_SUBMITTED           = 'submitted';
    public const STATE_REFUND_PENDING      = 'refund-pending';
    public const STATE_FAILED              = 'failed';
    public const STATE_UNKNOWN             = 'unknown';

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('state', [
            self::STATE_FREE,
            self::STATE_PAYMENT_REQUIRED,
            self::STATE_PENDING,
            self::STATE_PAYMENT_VERIFIED,
            self::STATE_SUBMISSION_RECOVERY,
        ]);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('state', self::STATE_SUBMITTED);
    }

    public function isSubmitted(): bool
    {
        return $this->state === self::STATE_SUBMITTED;
    }

    public function isPending(): bool
    {
        return in_array($this->state, [
            self::STATE_PENDING,
            self::STATE_PAYMENT_VERIFIED,
            self::STATE_SUBMISSION_RECOVERY,
        ], true);
    }
}
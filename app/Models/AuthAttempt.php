<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * AuthAttempt — OIDC authorization-code flow with PKCE.
 *
 * Per DFM-FDS-1.4.md §218 (LOCKED):
 *   id, state_hash UNIQUE, nonce_hash, encrypted PKCE verifier,
 *   handoff_hash UNIQUE NULL, return_uri_allowlisted,
 *   expires_at, consumed_at NULL, created_at.
 */
class AuthAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'state_hash',
        'nonce_hash',
        'pkce_verifier_encrypted',
        'handoff_hash',
        'return_uri_allowlisted',
        'expires_at',
        'consumed_at',
        'created_at',
    ];

    protected $casts = [
        'pkce_verifier_encrypted' => 'encrypted',
        'expires_at'              => 'datetime',
        'consumed_at'             => 'datetime',
        'created_at'              => 'datetime',
    ];

    /** Not yet expired + not yet consumed. */
    public function scopeActive($query)
    {
        return $query->whereNull('consumed_at')
                     ->where('expires_at', '>', now());
    }

    /** Is this attempt still usable? */
    public function isActive(): bool
    {
        return $this->consumed_at === null && $this->expires_at->isFuture();
    }

    /** Is this attempt expired? */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /** Mark consumed (single-use). */
    public function markConsumed(): void
    {
        $this->consumed_at = now();
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Email Verification Token — L304
 */
class EmailVerificationToken extends Model
{
    protected $fillable = [
        'user_id', 'token_hash', 'expires_at', 'consumed_at', 'ip_address',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public const TTL_HOURS = 24;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isUsable(): bool
    {
        return $this->consumed_at === null && $this->expires_at->isFuture();
    }

    public function consume(): void
    {
        $this->update(['consumed_at' => now()]);
    }

    public static function hashToken(string $plaintext): string
    {
        return hash('sha256', $plaintext);
    }
}

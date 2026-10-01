<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EmailOtp extends Model
{
    protected $fillable = [
        'email', 'code_hash', 'attempts',
        'expires_at', 'consumed_at', 'ip_address',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'consumed_at' => 'datetime',
        'attempts'    => 'integer',
    ];

    public const MAX_ATTEMPTS = 5;
    public const TTL_MINUTES  = 10;

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function isUsable(): bool
    {
        return ! $this->isExpired()
            && ! $this->isConsumed()
            && $this->attempts < self::MAX_ATTEMPTS;
    }

    public function verify(string $code): bool
    {
        if (! $this->isUsable()) {
            return false;
        }

        if (Hash::check($code, $this->code_hash)) {
            $this->update(['consumed_at' => now()]);
            return true;
        }

        $this->increment('attempts');
        return false;
    }
}

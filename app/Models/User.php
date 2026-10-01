<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasUuids, SoftDeletes;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'username',
        'email',
        'telegram_subject',
        'full_name',
        'phone_number',
        'profile_photo_url',
        'status',
        'rating_score',
        'rating_count',
        'version',
        'last_login_at',
        'recently_authenticated_at',
        'totp_secret',
        'totp_enabled_at',
        'totp_recovery_codes',
    ];

    protected $hidden = [
        'remember_token',
        'phone_number',
    ];

    protected $casts = [
        'rating_score' => 'decimal:2',
        'rating_count' => 'integer',
        'version' => 'integer',
        'last_login_at' => 'datetime',
        'recently_authenticated_at' => 'datetime',
        'totp_enabled_at' => 'datetime',
        'totp_secret' => 'encrypted',
        'totp_recovery_codes' => 'encrypted:array',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_SUSPENDED = 'SUSPENDED';
    public const STATUS_BANNED = 'BANNED';

    // Relationships
    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function needs()
    {
        return $this->hasMany(Need::class, 'requester_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'provider_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('role', $role)->whereNull('revoked_at');
        });
    }
}

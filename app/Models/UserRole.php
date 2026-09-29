<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'role',
        'granted_by',
        'granted_at',
        'revoked_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public const ROLE_MAIN_ADMIN = 'MAIN_ADMIN';
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_MODERATOR = 'MODERATOR';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at');
    }
}

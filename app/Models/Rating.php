<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'need_id',
        'from_user_id',
        'to_user_id',
        'score',
        'review',
        'created_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'created_at' => 'datetime',
    ];

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeValid($query)
    {
        return $query->whereBetween('score', [1, 5]);
    }
}

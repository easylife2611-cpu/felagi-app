<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'request_id',
        'reason',
        'before_digest',
        'after_digest',
        'safe_metadata',
        'occurred_at',
        'prev_hash',
        'hash',
    ];

    protected $casts = [
        'safe_metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeForEntity($query, string $type, string $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeByActor($query, string $actorId)
    {
        return $query->where('actor_id', $actorId);
    }
}

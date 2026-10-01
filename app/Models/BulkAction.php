<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkAction extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_id', 'action_type', 'entity_type', 'scope',
        'selection_ids', 'selection_digest', 'expected_count',
        'status', 'items', 'retry_of_id',
        'created_at', 'executed_at', 'completed_at',
    ];

    protected $casts = [
        'selection_ids' => 'array',
        'items'         => 'array',
        'expected_count'=> 'integer',
        'created_at'    => 'datetime',
        'executed_at'   => 'datetime',
        'completed_at'  => 'datetime',
    ];

    public const STATUS_PREVIEWED = 'PREVIEWED';
    public const STATUS_EXECUTED  = 'EXECUTED';
    public const STATUS_PARTIAL   = 'PARTIAL';
    public const STATUS_FAILED    = 'FAILED';

    public const ITEM_SUCCEEDED = 'SUCCEEDED';
    public const ITEM_FAILED    = 'FAILED';
    public const ITEM_UNKNOWN   = 'UNKNOWN';

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function retryOf()
    {
        return $this->belongsTo(BulkAction::class, 'retry_of_id');
    }

    public function failedItems(): array
    {
        return collect($this->items ?? [])
            ->where('status', self::ITEM_FAILED)
            ->values()->all();
    }

    public function isFullySucceeded(): bool
    {
        return $this->status === self::STATUS_EXECUTED;
    }
}

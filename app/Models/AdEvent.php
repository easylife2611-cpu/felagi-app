<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id', 'delivery_id', 'type', 'observed_at',
        'coverage', 'dedupe_key', 'ingestion_outcome',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'coverage'    => 'decimal:4',
    ];

    public const TYPE_IMPRESSION = 'IMPRESSION';
    public const TYPE_CLICK      = 'CLICK';

    public const TYPES = [self::TYPE_IMPRESSION, self::TYPE_CLICK];

    public function delivery()
    {
        return $this->belongsTo(AdDelivery::class);
    }
}

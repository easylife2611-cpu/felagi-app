<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ComparisonOffer extends Model
{
    use HasUuids;

    protected $fillable = [
        'comparison_id',
        'offer_id',
        'provider_id',
        'offer_snapshot',
        'credibility_snapshot',
        'offer_snapshot_hash',
    ];

    protected $casts = [
        'offer_snapshot' => 'array',
        'credibility_snapshot' => 'array',
    ];

    public function comparison()
    {
        return $this->belongsTo(Comparison::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function result()
    {
        return $this->hasOne(ComparisonResult::class, 'comparison_offer_id');
    }
}

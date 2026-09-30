<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'campaign_id', 'campaign_version', 'placement_id', 'slot_id',
        'creative_version', 'policy_version', 'session_ref',
        'eligible_at', 'expires_at',
    ];

    protected $casts = [
        'campaign_version'  => 'integer',
        'creative_version'  => 'integer',
        'eligible_at'       => 'datetime',
        'expires_at'        => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function events()
    {
        return $this->hasMany(AdEvent::class);
    }
}

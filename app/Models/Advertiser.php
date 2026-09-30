<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'display_name', 'contact_reference', 'status', 'notes',
    ];

    public const STATUS_ACTIVE  = 'ACTIVE';
    public const STATUS_BLOCKED = 'BLOCKED';

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class);
    }
}

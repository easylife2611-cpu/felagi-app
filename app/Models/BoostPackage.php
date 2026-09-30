<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostPackage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'duration_days',
        'price',
        'currency',
        'active',
        'published_settings_version',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function boosts()
    {
        return $this->hasMany(Boost::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

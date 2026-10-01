<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingPreset extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'display_name', 'description',
        'values_json', 'status', 'created_by',
    ];

    protected $casts = [
        'values_json' => 'array',
    ];

    public const STATUS_ACTIVE   = 'ACTIVE';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

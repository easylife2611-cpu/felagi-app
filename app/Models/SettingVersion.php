<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SettingVersion extends Model
{
    use HasFactory;

    use HasUuids;

    /** Immutable — append-only, no timestamps auto-set */
    public $timestamps = false;

    protected $fillable = [
        'id', 'setting_key', 'version_number', 'value_json',
        'published_by', 'published_at', 'reason', 'source_draft_id',
    ];

    protected $casts = [
        'value_json'     => 'array',
        'version_number' => 'integer',
        'published_at'   => 'datetime',
    ];

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_key', 'key');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}

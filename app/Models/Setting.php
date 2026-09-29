<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $incrementing = false;

    /** settings table has NO created_at — only updated_at */
    public $timestamps = false;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'key', 'group', 'type', 'value_json', 'default_json',
        'schema_json', 'description', 'risk', 'is_secret',
        'version_number', 'updated_by',
    ];

    protected $casts = [
        'value_json'   => 'array',
        'default_json' => 'array',
        'schema_json'  => 'array',
        'is_secret'    => 'boolean',
        'version_number' => 'integer',
        'updated_at'   => 'datetime',
    ];

    public const GROUP_FEATURE  = 'FEATURE';
    public const GROUP_CONFIG   = 'CONFIG';
    public const GROUP_CONTENT  = 'CONTENT';
    public const GROUP_SECURITY = 'SECURITY';

    public const RISK_LOW      = 'LOW';
    public const RISK_MEDIUM   = 'MEDIUM';
    public const RISK_HIGH     = 'HIGH';
    public const RISK_CRITICAL = 'CRITICAL';

    public const TYPE_BOOLEAN = 'BOOLEAN';
    public const TYPE_INTEGER = 'INTEGER';
    public const TYPE_DECIMAL = 'DECIMAL';
    public const TYPE_STRING  = 'STRING';
    public const TYPE_JSON    = 'JSON';

    public function versions()
    {
        return $this->hasMany(SettingVersion::class, 'setting_key', 'key');
    }

    public function drafts()
    {
        return $this->hasMany(SettingDraft::class, 'setting_key', 'key');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function requiresReauth(): bool
    {
        return in_array($this->risk, [self::RISK_HIGH, self::RISK_CRITICAL], true);
    }

    public function requiresSecondFactor(): bool
    {
        return $this->risk === self::RISK_CRITICAL;
    }
}

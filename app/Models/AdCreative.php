<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCreative extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'version', 'format', 'media_asset_id',
        'copy_am_title', 'copy_am_body', 'copy_am_cta', 'copy_am_alt',
        'copy_en_title', 'copy_en_body', 'copy_en_cta', 'copy_en_alt',
        'validation_receipt',
    ];

    protected $casts = [
        'version'            => 'integer',
        'validation_receipt' => 'array',
    ];

    public const FORMAT_CARD    = 'CARD';
    public const FORMAT_BANNER  = 'BANNER';
    public const FORMAT_COMPACT = 'COMPACT';

    public const FORMATS = [
        self::FORMAT_CARD,
        self::FORMAT_BANNER,
        self::FORMAT_COMPACT,
    ];

    public function campaigns()
    {
        return $this->hasMany(AdCampaign::class, 'creative_id');
    }
}

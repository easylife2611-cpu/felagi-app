<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonFeedback extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'comparison_id', 'provider_id', 'rating', 'comment', 'status',
    ];

    public const RATING_FAIR       = 'FAIR';
    public const RATING_INACCURATE = 'INACCURATE';
    public const RATING_UNCLEAR    = 'UNCLEAR';
    public const RATING_OTHER      = 'OTHER';

    public const RATINGS = [
        self::RATING_FAIR,
        self::RATING_INACCURATE,
        self::RATING_UNCLEAR,
        self::RATING_OTHER,
    ];

    public function comparison()
    {
        return $this->belongsTo(Comparison::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}

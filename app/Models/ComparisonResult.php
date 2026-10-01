<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonResult extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'comparison_id',
        'comparison_offer_id',
        'score',
        'criterion_scores',
        'strengths',
        'weaknesses',
        'missing_information',
        'risk_notes',
        'fit_explanation',
        'completeness',
        'missing_criteria',
        'uncertain_criteria',
        'result_hash',
        'created_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'criterion_scores' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'missing_information' => 'array',
        'risk_notes' => 'array',
        'missing_criteria' => 'array',
        'uncertain_criteria' => 'array',
        'created_at' => 'datetime',
    ];

    public function comparison()
    {
        return $this->belongsTo(Comparison::class);
    }

    public function comparisonOffer()
    {
        return $this->belongsTo(ComparisonOffer::class);
    }
}

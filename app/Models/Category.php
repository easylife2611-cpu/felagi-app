<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug',
        'name_am',
        'name_en',
        'active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function needs()
    {
        return $this->hasMany(Need::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('slug');
    }

    public function name(string $locale = 'am'): string
    {
        return $locale === 'am' ? $this->name_am : $this->name_en;
    }
}

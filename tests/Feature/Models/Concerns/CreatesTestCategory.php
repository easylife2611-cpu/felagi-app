<?php

namespace Tests\Feature\Models\Concerns;

use App\Models\Category;
use Illuminate\Support\Str;

trait CreatesTestCategory
{
    protected function makeCategory(): string
    {
        return Category::create([
            'name_am' => 'ምድብ ' . Str::random(4),
            'name_en' => 'Category ' . Str::random(4),
            'slug' => 'test-cat-' . Str::lower(Str::random(8)),
            'active' => true,
        ])->id;
    }
}

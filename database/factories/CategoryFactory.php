<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $suffix = Str::lower(Str::random(8));

        return [
            'slug'       => 'cat-' . $suffix,
            'name_am'    => 'ምድብ ' . $suffix,
            'name_en'    => 'Category ' . $suffix,
            'active'     => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }

    public function ordered(int $order): static
    {
        return $this->state(fn () => ['sort_order' => $order]);
    }
}

<?php

namespace Database\Factories;

use App\Models\AdCreative;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdCreativeFactory extends Factory
{
    protected $model = AdCreative::class;

    public function definition(): array
    {
        return [
            'version'       => 1,
            'format'        => AdCreative::FORMAT_BANNER,
            'media_asset_id'=> null,
            'copy_am_title' => 'ሙከራ ማስታወቂያ',
            'copy_am_body'  => 'ሙሉ የሙከራ ማስታወቂያ አካል።',
            'copy_am_cta'   => 'ተጨማሪ ይመልከቱ',
            'copy_am_alt'   => null,
            'copy_en_title' => 'Test advertisement',
            'copy_en_body'  => 'Full test advertisement body.',
            'copy_en_cta'   => 'Learn more',
            'copy_en_alt'   => null,
            'validation_receipt' => null,
        ];
    }

    public function card(): static
    {
        return $this->state(fn () => ['format' => AdCreative::FORMAT_CARD]);
    }

    public function compact(): static
    {
        return $this->state(fn () => ['format' => AdCreative::FORMAT_COMPACT]);
    }
}

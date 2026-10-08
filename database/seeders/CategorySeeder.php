<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Transport',      'name_am' => 'ትራንስፖርት'],
            ['name_en' => 'Construction',   'name_am' => 'ግንባታ'],
            ['name_en' => 'IT & Software',  'name_am' => 'አይቲ እና ሶፍትዌር'],
            ['name_en' => 'Agriculture',    'name_am' => 'ግብርና'],
            ['name_en' => 'Manufacturing',  'name_am' => 'ማምረቻ'],
            ['name_en' => 'Services',       'name_am' => 'አገልግሎቶች'],
            ['name_en' => 'Education',      'name_am' => 'ትምህርት'],
            ['name_en' => 'Healthcare',     'name_am' => 'ጤና'],
            ['name_en' => 'Logistics',      'name_am' => 'ሎጂስቲክስ'],
            ['name_en' => 'Consulting',     'name_am' => 'ምክር'],
        ];

        $created = 0;
        foreach ($categories as $cat) {
            $result = Category::updateOrCreate(
                ['slug' => Str::slug($cat['name_en'])],
                [
                    'name_en' => $cat['name_en'],
                    'name_am' => $cat['name_am'],
                ]
            );
            if ($result->wasRecentlyCreated) $created++;
        }

        $this->command->info("✅ Categories: {$created} new, " . count($categories) . " total processed");
    }
}

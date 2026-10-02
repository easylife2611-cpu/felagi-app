<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Seeds the 8 canonical Ethiopian marketplace categories.
 *
 * Idempotent — uses updateOrCreate on `slug`. Safe to re-run.
 * Never overwrites `created_by` if already set.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['construction', 'ኮንስትራክሽን እና ህንፃ',    'Construction & Building',  10],
            ['transport',    'ትራንስፖርት እና ሎጅስቲክስ', 'Transport & Logistics',    20],
            ['food',         'ምግብ እና ኬተሪንግ',       'Food & Catering',          30],
            ['it-services',  'አይቲ እና ሶፍትዌር',       'IT & Software',            40],
            ['home-services','የቤት አገልግሎቶች',        'Home Services',            50],
            ['events',       'ዝግጅቶች እና መዝናኛ',     'Events & Entertainment',   60],
            ['agriculture',  'ግብርና እና እርሻ',         'Agriculture',              70],
            ['retail',       'ችርቻሮ እና ንግድ',         'Retail & Trade',           80],
        ];

        $created = 0;
        $updated = 0;

        foreach ($categories as [$slug, $nameAm, $nameEn, $order]) {
            $existing = Category::where('slug', $slug)->first();

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name_am'    => $nameAm,
                    'name_en'    => $nameEn,
                    'active'     => true,
                    'sort_order' => $order,
                ],
            );

            if ($existing) {
                $updated++;
            } else {
                $created++;
            }
        }

        $this->command->info(
            "Categories seeded: " . count($categories) .
            " ({$created} created, {$updated} updated)."
        );
    }
}

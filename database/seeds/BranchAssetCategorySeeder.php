<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchAssetCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // ── Top-level categories from assets.md ───────────────────────────
            // These are the 8 Item Category values used in the asset register.
            // They act as the primary grouping layer.
            ['name' => 'Motor Vehicle',          'icon' => 'fa-car',               'allows_individual_tracking' => true],
            ['name' => 'Furniture',              'icon' => 'fa-table',             'allows_individual_tracking' => false],
            ['name' => 'Office Equipment',       'icon' => 'fa-print',             'allows_individual_tracking' => false],
            ['name' => 'ICT Equipment',          'icon' => 'fa-desktop',           'allows_individual_tracking' => true],
            ['name' => 'Communication Equipment','icon' => 'fa-phone',             'allows_individual_tracking' => true],
            ['name' => 'Office Supplies',        'icon' => 'fa-inbox',             'allows_individual_tracking' => false],
            ['name' => 'Safety Equipment',       'icon' => 'fa-shield',            'allows_individual_tracking' => false],
            ['name' => 'Other Equipment',        'icon' => 'fa-cog',               'allows_individual_tracking' => false],

            // ── Granular sub-categories from Goa.md ───────────────────────────
            // These exist for fine-grained branch inventory tracking.
            ['name' => 'Microwave',              'icon' => 'fa-cog',               'allows_individual_tracking' => false],
            ['name' => 'Water Dispenser',        'icon' => 'fa-tint',              'allows_individual_tracking' => false],
            ['name' => 'LC Office Desks',        'icon' => 'fa-table',             'allows_individual_tracking' => false],
            ['name' => 'LC Office Tables',       'icon' => 'fa-table',             'allows_individual_tracking' => false],
            ['name' => 'Visitors Chairs',        'icon' => 'fa-chair',             'allows_individual_tracking' => false],
            ["name" => "Manager's Chair",        'icon' => 'fa-chair',             'allows_individual_tracking' => false],
            ["name" => "Manager's Table",        'icon' => 'fa-table',             'allows_individual_tracking' => false],
            ['name' => 'Executive Chairs',       'icon' => 'fa-chair',             'allows_individual_tracking' => false],
            ['name' => 'Tablets',                'icon' => 'fa-tablet',            'allows_individual_tracking' => true],
            ['name' => 'Phones',                 'icon' => 'fa-phone',             'allows_individual_tracking' => true],
            ['name' => 'Fire Extinguishers',     'icon' => 'fa-fire-extinguisher', 'allows_individual_tracking' => false],
            ['name' => 'Office Trays',           'icon' => 'fa-inbox',             'allows_individual_tracking' => false],
            ['name' => 'Laptops',                'icon' => 'fa-laptop',            'allows_individual_tracking' => true],
            ['name' => 'Monitors',               'icon' => 'fa-desktop',           'allows_individual_tracking' => false],
            ['name' => 'Desktop Computers',      'icon' => 'fa-desktop',           'allows_individual_tracking' => true],
            ['name' => 'Wall Frames',            'icon' => 'fa-picture-o',         'allows_individual_tracking' => false],
            ['name' => 'Coffee Tables',          'icon' => 'fa-table',             'allows_individual_tracking' => false],
            ['name' => 'Office Cabinets',        'icon' => 'fa-archive',           'allows_individual_tracking' => false],
            ['name' => 'Electric Kettles',       'icon' => 'fa-coffee',            'allows_individual_tracking' => false],
        ];

        foreach ($categories as $cat) {
            DB::table('branch_asset_categories')->updateOrInsert(
                ['name' => $cat['name']],
                array_merge($cat, ['active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

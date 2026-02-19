<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PolicyCategory;

class PolicyCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Policies',
                'slug' => 'policies',
                'description' => 'Company policies and guidelines that all employees must follow.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Procedures',
                'slug' => 'procedures',
                'description' => 'Standard operating procedures and work instructions.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Training Manuals',
                'slug' => 'training-manuals',
                'description' => 'Training materials and educational resources for staff development.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Forms and Templates',
                'slug' => 'forms-templates',
                'description' => 'Downloadable forms, templates, and documents for daily operations.',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            PolicyCategory::create($category);
        }

        $this->command->info('Policy categories seeded successfully!');
    }
}

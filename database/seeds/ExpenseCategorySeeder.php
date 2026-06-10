<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Office Rent', 'type' => 'administration'],
            ['name' => 'Utilities', 'type' => 'administration'],
            ['name' => 'Staff Salaries', 'type' => 'administration'],
            ['name' => 'Office Supplies', 'type' => 'administration'],
            ['name' => 'Travel', 'type' => 'administration'],
            ['name' => 'Training', 'type' => 'administration'],
            ['name' => 'Insurance', 'type' => 'administration'],
            ['name' => 'Bank Charges', 'type' => 'bank_account'],
            ['name' => 'Interest', 'type' => 'bank_account'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
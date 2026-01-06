<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Disciplinary Case', 'priority_default' => 'High', 'sla_days' => 2],
            ['name' => 'Payroll Query', 'priority_default' => 'High', 'sla_days' => 1],
            ['name' => 'Transfer Request', 'priority_default' => 'Medium', 'sla_days' => 3],
            ['name' => 'Policy Clarification', 'priority_default' => 'Medium', 'sla_days' => 1],
            ['name' => 'General Admin', 'priority_default' => 'Low', 'sla_days' => 5],
        ];

        foreach ($categories as $c) {
            DB::table('ticket_categories')->updateOrInsert(
                ['name' => $c['name']],
                ['priority_default' => $c['priority_default'], 'sla_days' => $c['sla_days'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}

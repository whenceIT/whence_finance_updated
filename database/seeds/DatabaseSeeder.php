<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(CountryTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(LoanProvisioningCriteriaTableSeeder::class);
        $this->call(OfficeTableSeeder::class);
        $this->call(PayrollTemplateTableSeeder::class);
        // Ticket categories
        $this->call(TicketCategoriesSeeder::class);
        // Policy categories
        $this->call(PolicyCategoriesTableSeeder::class);
    }
}

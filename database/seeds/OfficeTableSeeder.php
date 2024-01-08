<?php

use Illuminate\Database\Seeder;

class OfficeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('offices')->insert([
            [

                'name' => 'Head Office',
                'opening_date' => date("Y-m-d"),
                'default_office' => '1',
            ]
        ]);
    }
}

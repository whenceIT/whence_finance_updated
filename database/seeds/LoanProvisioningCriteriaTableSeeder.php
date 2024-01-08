<?php

use Illuminate\Database\Seeder;

class LoanProvisioningCriteriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \Illuminate\Support\Facades\DB::table('loan_provisioning_criteria')->insert([
            [

                'name' => 'Current',
                'min' => '0',
                'max'=>'30',
                'percentage'=>'0',
            ],
            [

                'name' => 'Especially Mentioned',
                'min' => '31',
                'max'=>'60',
                'percentage'=>'5',
            ],
            [

                'name' => 'Substandard',
                'min' => '61',
                'max'=>'90',
                'percentage'=>'10',
            ],
            [

                'name' => 'Doubtful',
                'min' => '91',
                'max'=>'180',
                'percentage'=>'50',
            ],
            [

                'name' => 'Loss',
                'min' => '181',
                'max'=>'5000',
                'percentage'=>'100',
            ],
        ]);
    }
}

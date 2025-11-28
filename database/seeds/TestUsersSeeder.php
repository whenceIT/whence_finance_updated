<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\UserRole;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create employee user (role id 3 for loan-officer)
        $employeeCredentials = [
            "email" => 'employee@test.com',
            "password" => 'password123',
            "first_name" => 'John',
            "last_name" => 'Doe',
            "office_id" => 1, // Assuming office id 1 exists
        ];
        $employee = Sentinel::registerAndActivate($employeeCredentials);
        // Assign role - role id 3 is loan-officer
        $employeeRole = Sentinel::findRoleById(3);
        if ($employeeRole) {
            $employeeRole->users()->attach($employee);
        }

        // Create manager user (role id 4)
        $managerCredentials = [
            "email" => 'manager@test.com',
            "password" => 'password123',
            "first_name" => 'Jane',
            "last_name" => 'Smith',
            "office_id" => 1,
        ];
        $manager = Sentinel::registerAndActivate($managerCredentials);
        $managerRole = Sentinel::findRoleById(4);
        if ($managerRole) {
            $managerRole->users()->attach($manager);
        }

        // Admin is already created in UsersTableSeeder with email admin@webstudio.co.zw and password admin123
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserRole;

class UpdateUserPositionsByRole extends Command
{
    protected $signature = 'user:update-positions-2';
    protected $description = 'Update position_id for users with specific roles: role 4 → position 5, role 6 → position 2';

    public function handle()
    {
        $this->info('Starting to update user positions based on roles...');

        // Define role to position mappings
        $rolePositionMappings = [
            4 => 5,
            6 => 2
        ];

        // Process each role mapping
        foreach ($rolePositionMappings as $roleId => $positionId) {
            $users = User::whereHas('role', function($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->get();

            $this->info('Found ' . count($users) . ' users with role_id == ' . $roleId);

            foreach ($users as $user) {
                $user->position_id = $positionId;
                $user->save();
                $this->line('Updated user ID: ' . $user->id . ' - ' . $user->first_name . ' ' . $user->last_name . ' (role ' . $roleId . ' → position ' . $positionId . ')');
            }
        }

        $this->info('Position updates complete!');

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserRole;

class UpdateUserPosition extends Command
{
    protected $signature = 'user:update-position';
    protected $description = 'Update position_id for users with role == 3 to position_id == 21';

    public function handle()
    {
        $this->info('Starting to update user positions...');

        // Find all users with role == 3
        $users = User::whereHas('role', function($query) {
            $query->where('role_id', 3);
        })->get();

        $this->info('Found ' . count($users) . ' users with role_id == 3');

        // Update each user's position_id to 21
        foreach ($users as $user) {
            $user->position_id = 21;
            $user->save();
            $this->line('Updated user ID: ' . $user->id . ' - ' . $user->first_name . ' ' . $user->last_name);
        }

        $this->info('Position update complete!');

        return 0;
    }
}

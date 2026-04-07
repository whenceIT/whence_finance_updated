<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignUserDistrictIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-districts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assigns user->district_id based on their office->district_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting assignment of district_id for all users based on their office...');

        // Fetch users with their office relation
        $users = User::with('office')->get();
        $updatedCount = 0;

        foreach ($users as $user) {
            // Check if the user has an office and if that office has a district_id
            if ($user->office && $user->office->district_id) {
                // Only save if the district_id is not already matched
                if ($user->district_id !== $user->office->district_id) {
                    $user->district_id = $user->office->district_id;
                    $user->save();
                    $updatedCount++;
                }
            }
        }

        $this->info("Completed. Updated district_id for {$updatedCount} users.");

        return 0;
    }
}

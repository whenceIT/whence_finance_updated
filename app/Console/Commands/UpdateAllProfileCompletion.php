<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateAllProfileCompletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-all-profile-completion {status : The status to set (1 for completed, 0 for not completed)} {--dry-run : Log what would be changed without actually saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update has_completed_profile status for all users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->argument('status');
        
        // Validate status argument
        if (!in_array($status, ['0', '1'])) {
            $this->error('Status must be either 0 or 1');
            return Command::FAILURE;
        }
        
        $dryRun = $this->option('dry-run');
        
        // Get all users
        $users = \App\Models\User::all();
        
        $this->info("Found {$users->count()} users in total");
        
        if ($users->count() === 0) {
            $this->warn('No users found in the database');
            return Command::SUCCESS;
        }
        
        $updatedCount = 0;
        
        foreach ($users as $user) {
            $currentStatus = $user->has_completed_profile ? '1' : '0';
            
            if ($currentStatus != $status) {
                $this->line("Updating User ID: {$user->id} ({$user->first_name} {$user->last_name}) - Current: {$currentStatus}, New: {$status}");
                
                if (!$dryRun) {
                    $user->has_completed_profile = (int)$status;
                    $user->save();
                }
                
                $updatedCount++;
            } else {
                $this->line("Skipping User ID: {$user->id} ({$user->first_name} {$user->last_name}) - Already has status {$status}");
            }
        }
        
        if ($dryRun) {
            $this->info("Dry run complete. {$updatedCount} users would have been updated.");
        } else {
            $this->info("Update complete. {$updatedCount} users updated.");
        }
        
        return Command::SUCCESS;
    }
}

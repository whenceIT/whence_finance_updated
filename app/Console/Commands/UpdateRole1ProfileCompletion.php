<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateRole1ProfileCompletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-role1-profile-completion {status : The status to set (1 for completed, 0 for not completed)} {--dry-run : Log what would be changed without actually saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update has_completed_profile status for all users with role_id = 1';

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
        
        // Get all users with role_id = 1
        $users = \App\Models\User::where('role_id', 1)->get();
        
        $this->info("Found {$users->count()} users with role_id = 1");
        
        if ($users->count() === 0) {
            $this->warn('No users with role_id = 1 found in the database');
            return Command::SUCCESS;
        }
        
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($users as $user) {
            // Check if user has a related user
            if (!$user->user) {
                $this->warn("Skipping User ID: {$user->id} - No related user found");
                $skippedCount++;
                continue;
            }
            
            $currentStatus = $user->user->has_completed_profile ? '1' : '0';
            
            if ($currentStatus != $status) {
                $this->line("Updating User ID: {$user->id} ({$user->first_name} {$user->last_name}) - Related User ID: {$user->user->id} - Current: {$currentStatus}, New: {$status}");
                
                if (!$dryRun) {
                    $user->user->has_completed_profile = (int)$status;
                    $user->user->save();
                }
                
                $updatedCount++;
            } else {
                $this->line("Skipping User ID: {$user->id} ({$user->first_name} {$user->last_name}) - Related User ID: {$user->user->id} - Already has status {$status}");
                $skippedCount++;
            }
        }
        
        if ($dryRun) {
            $this->info("Dry run complete. {$updatedCount} users would have been updated, {$skippedCount} skipped.");
        } else {
            $this->info("Update complete. {$updatedCount} users updated, {$skippedCount} skipped.");
        }
        
        return Command::SUCCESS;
    }
}

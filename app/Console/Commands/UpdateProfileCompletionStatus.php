<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateProfileCompletionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-profile-completion {status : The status to set (1 for completed, 0 for not completed)} {--office-id= : The office ID to filter users (default: 34)} {--dry-run : Log what would be changed without actually saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update has_completed_profile status for users by office ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->argument('status');
        $officeId = $this->option('office-id') ?? 34;
        
        // Validate status argument
        if (!in_array($status, ['0', '1'])) {
            $this->error('Status must be either 0 or 1');
            return Command::FAILURE;
        }
        
        $dryRun = $this->option('dry-run');
        
        // Get users with specified office_id
        $users = \App\Models\User::where('office_id', $officeId)->get();
        
        $this->info("Found {$users->count()} users with office_id = {$officeId}");
        
        if ($users->count() === 0) {
            $this->warn("No users found with office_id = {$officeId}");
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

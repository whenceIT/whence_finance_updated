<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateUserProvinceFromOffice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-user-provinces {--dry-run : Log what would be changed without actually saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign user province_id by getting the value from user office province_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = \App\Models\User::whereNotNull('office_id')->with('office')->get();
        $dryRun = $this->option('dry-run');

        $this->info("Found {$users->count()} users with office_id.");

        $updatedCount = 0;
        foreach ($users as $user) {
            if ($user->office && $user->office->province_id) {
                if ($user->province_id != $user->office->province_id) {
                    $this->line("Updating User ID: {$user->id} ({$user->first_name} {$user->last_name}) - Office: {$user->office->name} (Province ID: {$user->office->province_id})");

                    if (!$dryRun) {
                        $user->province_id = $user->office->province_id;
                        $user->save();
                    }
                    $updatedCount++;
                }
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

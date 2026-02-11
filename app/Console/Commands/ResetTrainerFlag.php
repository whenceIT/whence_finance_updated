<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetTrainerFlagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-trainer {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all users istrainer flag to 0';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $usersCount = User::count();
        $trainersCount = User::where('istrainer', 1)->count();
        
        $this->info("Total users: {$usersCount}");
        $this->info("Users with istrainer = 1: {$trainersCount}");
        
        if ($dryRun) {
            $this->warn('DRY RUN - No changes will be made.');
            $usersToReset = User::where('istrainer', 1)->get();
            $this->table(
                ['ID', 'Name', 'Email', 'istrainer'],
                $usersToReset->map(fn($u) => [$u->id, $u->first_name . ' ' . $u->last_name, $u->email, $u->istrainer])
            );
            return 0;
        }
        
        if (!$this->confirm('Are you sure you want to reset all users istrainer flag to 0?')) {
            $this->info('Command cancelled.');
            return 1;
        }
        
        $updated = User::where('istrainer', 1)->update(['istrainer' => 0]);
        
        $this->info("Successfully reset {$updated} users istrainer flag to 0.");
        
        return 0;
    }
}

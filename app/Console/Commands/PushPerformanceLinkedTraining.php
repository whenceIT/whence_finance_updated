<?php

namespace App\Console\Commands;

use App\Services\TrainingHubPerformancePusher;
use Illuminate\Console\Command;

class PushPerformanceLinkedTraining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'training:push-performance-triggers
                            {--trigger= : Run a specific trigger (defaults, turnover, clients)}
                            {--dry-run : Show what would be done without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push training hub content based on performance indicators (loan defaults, staff turnover, declining clients)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $trigger  = $this->option('trigger');

        if ($isDryRun) {
            $this->warn('⚠  DRY RUN MODE — no notifications will be sent.');
        }

        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║   Performance-Linked Learning Triggers           ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->newLine();

        $summary = [];

        if (!$trigger || $trigger === 'defaults') {
            $this->line('▸ Checking high loan defaults...');
            $count = $isDryRun ? 0 : TrainingHubPerformancePusher::pushForHighLoanDefaults();
            $summary['High Loan Defaults'] = $count;
            $this->info("  → Notified {$count} user(s) with vetting & due-diligence content.");
        }

        if (!$trigger || $trigger === 'turnover') {
            $this->line('▸ Checking high staff turnover...');
            $count = $isDryRun ? 0 : TrainingHubPerformancePusher::pushForHighStaffTurnover();
            $summary['High Staff Turnover'] = $count;
            $this->info("  → Notified {$count} user(s) with leadership & management content.");
        }

        if (!$trigger || $trigger === 'clients') {
            $this->line('▸ Checking declining client base...');
            $count = $isDryRun ? 0 : TrainingHubPerformancePusher::pushForDecliningClientBase();
            $summary['Declining Client Base'] = $count;
            $this->info("  → Notified {$count} user(s) with client management content.");
        }

        $this->newLine();
        $this->table(['Trigger', 'Users Notified'], collect($summary)->map(function ($count, $label) {
            return [$label, $count];
        })->toArray());

        $total = array_sum($summary);
        $this->newLine();
        $this->info("✓ Complete. Total notifications sent: {$total}");

        return Command::SUCCESS;
    }
}

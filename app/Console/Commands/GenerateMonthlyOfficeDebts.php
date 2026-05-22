<?php

namespace App\Console\Commands;

use App\Services\OfficeDebtService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyOfficeDebts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debts:generate-monthly
                            {--as-of= : Anchor date YYYY-MM-DD (defaults to today)}
                            {--office= : Restrict to a single office by ID}
                            {--dry-run : Show what would be done without writing to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For every past month whose 28th has passed, check each office + deposit type and record any shortfall as OfficeDebt. Use --office to scope to a single branch.';

    /**
     * Execute the console command.
     */
    public function handle(OfficeDebtService $service): int
    {
        $asOf   = $this->option('as-of')
            ? Carbon::createFromFormat('Y-m-d', $this->option('as-of'))
            : Carbon::now();
        $office = $this->option('office')
            ? (int) $this->option('office')
            : null;

        $this->info("Anchor date: {$asOf->toDateString()}");
        if ($office !== null) {
            $this->info("Scoped to office ID: {$office}");
        }

        if ($this->option('dry-run')) {
            return Command::SUCCESS;
        }

        $result = $service->runMonthlyCheck($asOf, $office);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Months processed',       $result['months_processed']],
                ['Debt records created',   $result['debt_records_created']],
                ['Debt records updated',   $result['debt_records_updated']],
                ['Total shortfall',        'K' . number_format((float) $result['total_shortfall'], 2)],
            ]
        );

        return Command::SUCCESS;
    }


    // # all offices (existing behaviour)
    // php artisan debts:generate-monthly

    // # single office
    // php artisan debts:generate-monthly --office=13

    // # single office + dry-run
    // php artisan debts:generate-monthly --office=5 --dry-run

    // # single office + historical anchor
    // php artisan debts:generate-monthly --office=5 --as-of=2026-04-01
}

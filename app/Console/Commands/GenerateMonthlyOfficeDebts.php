<?php

namespace App\Console\Commands;

use App\Services\OfficeDebtService;
use Illuminate\Console\Command;

class GenerateMonthlyOfficeDebts extends Command
{
    protected $signature = 'debts:generate-monthly
                            {--office= : Restrict to a single office by ID}
                            {--month= : Specific month in MM-YYYY format (defaults to current month)}';

    protected $description = 'Calculate and record annual deposit shortfalls as office debt. Defaults to current month/year. Use --office to scope to a single branch or --month for a specific month (MM-YYYY).';

    public function handle(OfficeDebtService $service): int
    {
        $office = $this->option('office') ? (int) $this->option('office') : null;
        $month = $this->option('month');

        $this->info("Processing debt calculation...");
        if ($month) {
            $this->info("Processing for month: {$month}");
        } elseif ($office !== null) {
            $this->info("Scoped to office ID: {$office}");
        }

        $result = $service->runMonthlyCheck($office, $month);

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
}
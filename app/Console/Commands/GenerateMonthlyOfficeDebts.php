<?php

namespace App\Console\Commands;

use App\Services\OfficeDebtService;
use Illuminate\Console\Command;

class GenerateMonthlyOfficeDebts extends Command
{
    protected $signature = 'debts:generate-monthly
                            {--office= : Restrict to a single office by ID}';

    protected $description = 'Calculate and record annual deposit shortfalls as office debt. Defaults to current year. Use --office to scope to a single branch.';

    public function handle(OfficeDebtService $service): int
    {
        $office = $this->option('office') ? (int) $this->option('office') : null;

        $this->info("Processing debt calculation...");
        if ($office !== null) {
            $this->info("Scoped to office ID: {$office}");
        }

        $result = $service->runMonthlyCheck($office);

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
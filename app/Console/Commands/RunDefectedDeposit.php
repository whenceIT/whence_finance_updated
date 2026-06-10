<?php

namespace App\Console\Commands;

use App\Models\BankDepositLog;
use App\Models\Deposit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunDefectedDeposit extends Command
{
    protected $signature = 'run:defect-deposits {--office_id= : Restrict to a single office by ID}';

    protected $description = 'Find deposits that are not in the bank deposit log';

    public function handle(): int
    {
        $officeId = $this->option('office_id') ? (int) $this->option('office_id') : null;

        $query = Deposit::query()
            ->withoutGlobalScope('approved')
            ->leftJoin('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
            ->whereNull('bank_deposit_log.deposit_id')
            ->where('deposits.status', 1);

        if ($officeId !== null) {
            $query->where('deposits.office', $officeId);
        }

        $defectedDeposits = $query->select(['deposits.*'])->get();

        $this->table(
            ['ID', 'Deposit Type', 'Office', 'Amount', 'Date'],
            $defectedDeposits->map(fn ($deposit) => [
                $deposit->id,
                $deposit->deposit_type,
                $deposit->office,
                $deposit->amount,
                $deposit->date,
            ])->toArray()
        );

        $this->info("Total defected deposits: " . $defectedDeposits->count());

        if ($defectedDeposits->isNotEmpty()) {
            $this->info("Defected deposit IDs: " . json_encode($defectedDeposits->pluck('id')->values()->toArray()));
        }

        $defectedDepositIds = $defectedDeposits->pluck('id')->toArray();
        if (!empty($defectedDepositIds)) {
            Deposit::whereIn('id', $defectedDepositIds)->delete();
            $this->info("Deleted defected deposits with IDs: " . json_encode($defectedDepositIds));
        }

        $bankDepositLogEntries = BankDepositLog::query()
            ->leftJoin('deposits', 'bank_deposit_log.deposit_id', '=', 'deposits.id')
            ->whereNull('deposits.id')
            ->select('bank_deposit_log.*')
            ->get();

        foreach ($bankDepositLogEntries as $entry) {
            $newDeposit = Deposit::create([
                'deposit_type' => $entry->deposit_type,
                'office' => $entry->office_id,
                'amount' => $entry->amount,
                'date' => $entry->created_date,
                'status' => 1,
            ]);

            $entry->update(['deposit_id' => $newDeposit->id]);
            $this->info("Created new deposit with ID: " . $newDeposit->id . " for bank deposit log entry ID: " . $entry->id);
        }

        return Command::SUCCESS;
    }
}
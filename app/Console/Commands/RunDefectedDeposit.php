<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunDefectedDeposit extends Command
{
    protected $signature = 'run:defect-deposits {--office_id= : Restrict to a single office by ID}';

    protected $description = 'Find deposits that are not in the bank deposit log';

    public function handle(): int
    {
        $officeId = $this->option('office_id') ? (int) $this->option('office_id') : null;

        $query = DB::table('deposits')
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
            DB::table('deposits')->whereIn('id', $defectedDepositIds)->delete();
            $this->info("Deleted defected deposits with IDs: " . json_encode($defectedDepositIds));
        }

        $bankDepositLogEntries = DB::table('bank_deposit_log')
            ->leftJoin('deposits', 'bank_deposit_log.deposit_id', '=', 'deposits.id')
            ->whereNull('deposits.id');

        if ($officeId !== null) {
            $bankDepositLogEntries->where('bank_deposit_log.office_id', $officeId);
        }

        $bankDepositLogEntries = $bankDepositLogEntries->select('bank_deposit_log.*')->get();

        foreach ($bankDepositLogEntries as $entry) {
            $newDepositId = DB::table('deposits')->insertGetId([
                'deposit_type' => $entry->deposit_type,
                'office' => $entry->office_id,
                'amount' => $entry->amount,
                'date' => \Carbon\Carbon::parse($entry->created_date)->format('Y-m-d'),
                'status' => 1,
            ]);

            DB::table('bank_deposit_log')->where('id', $entry->id)->update(['deposit_id' => $newDepositId]);
            $this->info("Created new deposit with ID: " . $newDepositId . " for bank deposit log entry ID: " . $entry->id);
        }

        return Command::SUCCESS;
    }
}
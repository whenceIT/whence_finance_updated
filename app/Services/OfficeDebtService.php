<?php

namespace App\Services;

use App\Models\Office;
use App\Models\DepositType;
use App\Models\Deposit;
use App\Models\OfficeDebt;
use Carbon\Carbon;

class OfficeDebtService
{
    /**
     * For the year, and office_id:
     *   1. Group deposits by (office_id, deposit_type) for that year. Then:
     *      a. Count/cap the total number of months to process based on the current date.
     *      b. total_expected = DepositType.monthly_amount x total months passed in the year (up to current month).
     *      c. total_deposits = SUM(amount) for that office + deposit type + year (up to current month).
     *      d. If debt > 0 then create or update an OfficeDebt record for the office + deposit type + month.
     *         - If creating new: set original_amount to the debt.
     *         - If updating: add the debt to original_amount.
     *
     * @param  int|null     $officeId  
     * @param  string|null  $month     Format: MM-YYYY (e.g., 04-2026)
     * @return array  ['months_processed'=>int, 'debt_records_created'=>int, 'debt_records_updated'=>int, 'total_shortfall'=>float]
     */
    public function runMonthlyCheck(?int $officeId = null, ?string $month = null): array
    {
        $today = Carbon::now();
        $currentYear = $today->year;
        $currentMonth = $today->month;

        if ($month) {
            $parts = explode('-', $month);
            $currentMonth = (int) $parts[0];
            $currentYear = (int) $parts[1];
        }

        $monthsToProcess = $currentMonth;

        $types = DepositType::orderBy('sort_order')->get();
        $officesQuery = Office::where('active', 1)->where('id', '!=', 67)->orderBy('name');
        if ($officeId !== null) {
            $officesQuery->where('id', $officeId);
        }
        $offices = $officesQuery->get();

        $validDeposits = Deposit::query() 
            ->whereYear('date', $currentYear)
            ->whereMonth('date', '<=', $currentMonth)
            ->get();

        $depsIdx = [];
        foreach ($validDeposits as $dep) {
            $key = $dep->office . '|' . $dep->deposit_type;
            $depsIdx[$key] = (float) ($depsIdx[$key] ?? 0) + (float) $dep->amount;
        }

        $created = 0;
        $updated = 0;
        $totalSf = 0.0;

        foreach ($types as $type) {
            $monthlyRequired = (float) ($type->monthly_amount ?? 0);
            $totalExpected = $monthlyRequired;

            // dd($type->name, $monthlyRequired, $monthsToProcess, $totalExpected);
            foreach ($offices as $office) {
                $key = $office->id . '|' . $type->id;
                $received = $depsIdx[$key] ?? 0.0;

                $debt = $totalExpected - $received;
                if ($debt <= 0) {
                    continue;
                }

                OfficeDebt::where('office_id', $office->id)
                    ->where('deposit_type_id', $type->id)
                    ->where('debt_month', $currentMonth)
                    ->where('debt_year', $currentYear)
                    ->delete();

                OfficeDebt::create([
                    'office_id' => $office->id,
                    'deposit_type_id' => $type->id,
                    'debt_month' => $currentMonth,
                    'debt_year' => $currentYear,
                    'debt_status' => 'owing',
                    'original_amount' => (int) round($debt),
                    'outstanding_amount' => (int) round($debt),
                    'notes' => null,
                    'is_setup_debt' => 'false',
                ]);
                $created++;

                $totalSf += $debt;
            }
        }

        return [
            'months_processed' => $monthsToProcess,
            'debt_records_created' => $created,
            'debt_records_updated' => $updated,
            'total_shortfall' => $totalSf,
        ];
    }
}
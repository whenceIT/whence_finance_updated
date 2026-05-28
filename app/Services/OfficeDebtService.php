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
     * Run the monthly shortfall check for all offices, or a single office when $officeId is given.
     *
     * For every month whose 28th has already passed:
     *   1. Group deposits by (office_id, deposit_type) for that month.
     *   2. Compare the received total against DepositType.monthly_amount.
     *   3. If received < monthly_amount → upsert an OfficeDebt record that
     *      accumulates the shortfall into original_amount / outstanding_amount.
     *
     * @param  Carbon|null  $asOfDate  anchor date; defaults to today
     * @param  int|null     $officeId  when set, only process this office
     * @return array  ['months_processed'=>int, 'debt_records_created'=>int, 'debt_records_updated'=>int, 'total_shortfall'=>float]
     */
    public function runMonthlyCheck(?Carbon $asOfDate = null, ?int $officeId = null): array
    {
        $today   = $asOfDate ?? Carbon::now();
        $months  = $this->pastMonthsWithDeadlinePassed($today);

        $types         = DepositType::orderBy('sort_order')->get();
        $officesQuery  = Office::where('active', 1)->orderBy('name');
        if ($officeId !== null) {
            $officesQuery->where('id', $officeId);
        }
        $offices     = $officesQuery->get();
        $allDeposits = Deposit::all();   // small table; filter in PHP

        // Index deposits by month+office+deposit_type
        $depsIdx = [];
        foreach ($allDeposits as $dep) {
            $dt   = Carbon::parse((string) $dep->date);
            $key  = $dt->format('Y-m') . '|' . $dep->office . '|' . $dep->deposit_type;
            $depsIdx[$key] = (float) ($depsIdx[$key] ?? 0) + (float) $dep->amount;
        }

        $created  = 0;
        $updated  = 0;
        $totalSf  = 0.0;

        foreach ($months as $monthYear) {
            [$year, $month] = explode('-', $monthYear);

            foreach ($types as $type) {
                $required = (float) $type->monthly_amount;

                foreach ($offices as $office) {
                    $key    = $monthYear . '|' . $office->id . '|' . $type->id;
                    $recv   = $depsIdx[$key] ?? 0.0;

                    if ($recv >= $required) {
                        // Sufficient — no debt
                        continue;
                    }

                    $shortfall  = $required - $recv;

                    // firstOrCreate on the unique business key ensures no duplicates
                    // on re-runs; when the row already exists we accumulate shortfall.
                    $record = OfficeDebt::firstOrCreate(
                        [
                            'office_id'       => $office->id,
                            'deposit_type_id' => $type->id,
                            'debt_month'      => (int) $month,
                            'debt_year'       => (int) $year,
                        ],
                        [
                            'debt_status'        => 'owing',
                            'original_amount'    => (int) round($shortfall),
                            'outstanding_amount' => (int) round($shortfall),
                            'notes'              => null,
                            'is_setup_debt'      => 'false',
                        ]
                    );

                    if ($record->wasRecentlyCreated) {
                        $created++;
                    } else {
                        // Accumulate shortfall into existing record
                        $record->original_amount    += (int) round($shortfall);
                        $record->outstanding_amount += (int) round($shortfall);
                        $record->debt_status         = 'owing';
                        $record->save();
                        $updated++;
                    }

                    $totalSf += $shortfall;
                }
            }
        }

        return [
            'months_processed'       => count($months),
            'debt_records_created'   => $created,
            'debt_records_updated'   => $updated,
            'total_shortfall'        => $totalSf,
        ];
    }

    /**
     * Return formatted month strings (Y-m) for every past month whose
     * 28th has already passed, newest first.
     * Caps at January of the current year — no prior years are touched.
     */
    private function pastMonthsWithDeadlinePassed(Carbon $today): array
    {
        $months = [];

        // This month — only include if the 28th has passed
        if ($today->day >= 28) {
            $months[] = $today->format('Y-m');
        }

        // Previous months — stop at Jan of the current year (no prior years)
        $cursor = $today->copy()->subMonth();
        while ($cursor->year === $today->year && $cursor->month >= 1) {
            $months[] = $cursor->format('Y-m');
            $cursor->subMonth();
        }

        return $months;
    }
}

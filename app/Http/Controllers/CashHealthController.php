<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Office;

class CashHealthController extends Controller
{
    public function show(Request $request,$id)
    {
        $apiUrl = 'https://lms2backend.whencefinancesystem.com';

        /*
        |--------------------------------------------------------------------------
        | FIND CURRENT CYCLE
        |--------------------------------------------------------------------------
        |
        | Cash cycle runs:
        |
        | 25th -> 24th
        |
        */

       $officeName = Office::where('id', $id)->value('name');

        $today = Carbon::today();

        if ($today->day >= 25) {

            // Example:
            // 25 August -> current cycle starts 25 August

            $currentCycleStart = $today
                ->copy()
                ->startOfMonth()
                ->day(25);

        } else {

            // Example:
            // 20 August -> current cycle started 25 July

            $currentCycleStart = $today
                ->copy()
                ->subMonth()
                ->startOfMonth()
                ->day(25);
        }


        /*
        |--------------------------------------------------------------------------
        | GET SELECTED CYCLE
        |--------------------------------------------------------------------------
        |
        | If the user hasn't selected anything, use the current cycle.
        |
        */

        $cycleStart = $request->query(
            'cycle_start',
            $currentCycleStart->format('Y-m-d')
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE CYCLE START
        |--------------------------------------------------------------------------
        */

        try {

            $selectedCycleStart = Carbon::createFromFormat(
                'Y-m-d',
                $cycleStart
            );

        } catch (\Exception $e) {

            abort(
                400,
                'Invalid cycle start date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MAKE SURE IT IS THE 25TH
        |--------------------------------------------------------------------------
        */

        if ($selectedCycleStart->day !== 25) {

            abort(
                400,
                'Cash cycle must start on the 25th.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CALCULATE CYCLE END
        |--------------------------------------------------------------------------
        */

        $cycleEnd = $selectedCycleStart
            ->copy()
            ->addMonth()
            ->subDay();


        /*
        |--------------------------------------------------------------------------
        | CALL CASH HEALTH API
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(120)
            ->get(
                $apiUrl . '/cash-health/'.
            $id,
                [
                    'cycle_start' => $cycleStart
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | HANDLE API ERROR
        |--------------------------------------------------------------------------
        */

        if (!$response->successful()) {

            abort(
                $response->status(),
                'Unable to retrieve Cash Health data.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET API DATA
        |--------------------------------------------------------------------------
        */

        $cashHealth = $response->json();


        /*
        |--------------------------------------------------------------------------
        | GENERATE AVAILABLE CYCLES
        |--------------------------------------------------------------------------
        |
        | We'll show the current cycle plus the previous
        | 11 cycles in the dropdown.
        |
        */

        $availableCycles = [];

        $cycle = $currentCycleStart->copy();

        for ($i = 0; $i < 12; $i++) {

            $start = $cycle->copy();

            $end = $start
                ->copy()
                ->addMonth()
                ->subDay();

            $availableCycles[] = [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ];

            $cycle->subMonth();
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'cash-health.show',
            compact(
                'cashHealth',
                'cycleStart',
                'cycleEnd',
                'availableCycles',
                'id',
                'officeName'
            )
        );
    }


public function contributionHistory($id)
{
    $apiUrl = 'https://lms2backend.whencefinancesystem.com';

    try {

        $response = Http::timeout(60)
            ->get(
                $apiUrl . '/cash-health/' . $id . '/contributions/'
            );

        if (!$response->successful()) {

            \Log::error('Cash health contribution API failed', [
                'office_id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Unable to retrieve contribution history',
                'status' => $response->status(),
            ], $response->status());
        }

        return response()->json(
            $response->json()
        );

    } catch (\Throwable $e) {

        \Log::error('Cash health contribution exception', [
            'office_id' => $id,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'error' => 'Unable to retrieve contribution history',
            'message' => $e->getMessage(),
        ], 500);
    }
}

      public function district(Request $request, $district)
    {
        $apiUrl = 'https://lms2backend.whencefinancesystem.com';


        /*
        |--------------------------------------------------------------------------
        | CURRENT CYCLE
        |--------------------------------------------------------------------------
        */

        
        $offices = Office::get()->keyBy('id');

        $today = Carbon::today();

        if ($today->day >= 25) {

            $currentCycleStart = $today
                ->copy()
                ->startOfMonth()
                ->day(25);

        } else {

            $currentCycleStart = $today
                ->copy()
                ->subMonth()
                ->startOfMonth()
                ->day(25);
        }


        /*
        |--------------------------------------------------------------------------
        | SELECTED CYCLE
        |--------------------------------------------------------------------------
        */

        $cycleStart = $request->query(
            'cycle_start',
            $currentCycleStart->format('Y-m-d')
        );


        /*
        |--------------------------------------------------------------------------
        | CALL DISTRICT API
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(60)
            ->get(
                $apiUrl . '/cash-health/district/' . $district,
                [
                    'cycle_start' => $cycleStart
                ]
            );


        if (!$response->successful()) {

            abort(
                $response->status(),
                'Unable to retrieve District Cash Health data.'
            );
        }


        $districtHealth = $response->json();


        /*
        |--------------------------------------------------------------------------
        | CYCLE END
        |--------------------------------------------------------------------------
        */

        $selectedCycleStart = Carbon::createFromFormat(
            'Y-m-d',
            $cycleStart
        );

        $cycleEnd = $selectedCycleStart
            ->copy()
            ->addMonth()
            ->subDay();


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE CYCLES
        |--------------------------------------------------------------------------
        */

        $availableCycles = [];

        $cycle = $currentCycleStart->copy();

        for ($i = 0; $i < 12; $i++) {

            $start = $cycle->copy();

            $end = $start
                ->copy()
                ->addMonth()
                ->subDay();

            $availableCycles[] = [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ];

            $cycle->subMonth();
        }


        return view(
            'cash-health.district',
            compact(
                'districtHealth',
                'cycleStart',
                'cycleEnd',
                'availableCycles',
                'offices'
            )
        );
    }

    public function province(Request $request, $province)
{
    $apiUrl = 'https://lms2backend.whencefinancesystem.com';

    $offices = Office::get()->keyBy('id');

    /*
    |--------------------------------------------------------------------------
    | CURRENT CYCLE
    |--------------------------------------------------------------------------
    */

    $today = Carbon::today();

    if ($today->day >= 25) {

        $currentCycleStart = $today
            ->copy()
            ->startOfMonth()
            ->day(25);

    } else {

        $currentCycleStart = $today
            ->copy()
            ->subMonth()
            ->startOfMonth()
            ->day(25);
    }


    /*
    |--------------------------------------------------------------------------
    | SELECTED CYCLE
    |--------------------------------------------------------------------------
    */

    $cycleStart = $request->query(
        'cycle_start',
        $currentCycleStart->format('Y-m-d')
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDATE DATE
    |--------------------------------------------------------------------------
    */

    try {

        $selectedCycleStart = Carbon::createFromFormat(
            'Y-m-d',
            $cycleStart
        );

    } catch (\Exception $e) {

        abort(
            400,
            'Invalid cycle start date.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE CYCLE END
    |--------------------------------------------------------------------------
    */

    $cycleEnd = $selectedCycleStart
        ->copy()
        ->addMonth()
        ->subDay();


    /*
    |--------------------------------------------------------------------------
    | CALL PROVINCIAL CASH HEALTH API
    |--------------------------------------------------------------------------
    */

    $response = Http::timeout(15)
        ->get(
            $apiUrl . '/cash-health/province/' . $province,
            [
                'cycle_start' => $cycleStart
            ]
        );


    if (!$response->successful()) {

        abort(
            $response->status(),
            'Unable to retrieve Provincial Cash Health data.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | API RESPONSE
    |--------------------------------------------------------------------------
    */

    $provinceHealth = $response->json();


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE CYCLES
    |--------------------------------------------------------------------------
    */

    $availableCycles = [];

    $cycle = $currentCycleStart->copy();

    for ($i = 0; $i < 12; $i++) {

        $start = $cycle->copy();

        $end = $start
            ->copy()
            ->addMonth()
            ->subDay();

        $availableCycles[] = [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];

        $cycle->subMonth();
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'cash-health.province',
        compact(
            'provinceHealth',
            'cycleStart',
            'cycleEnd',
            'availableCycles',
            'offices'
        )
    );
}


/**
 * Replace the existing national() method with this version.
 * Assumes Office, Carbon, Http and Request are already imported in the controller.
 */
public function national(Request $request)
{
    $apiUrl = 'https://lms2backend.whencefinancesystem.com';
    $offices = Office::get()->keyBy('id');
    $today = Carbon::today();

    // Resolve the current 25th-to-24th cycle.
    if ($today->day >= 25) {
        $currentCycleStart = $today->copy()->startOfMonth()->day(25);
    } else {
        $currentCycleStart = $today->copy()->subMonthNoOverflow()->startOfMonth()->day(25);
    }

    $cycleStart = $request->query('cycle_start', $currentCycleStart->format('Y-m-d'));

    try {
        $selectedCycleStart = Carbon::createFromFormat('!Y-m-d', $cycleStart);
    } catch (\Exception $e) {
        abort(400, 'Invalid cycle start date.');
    }

    if (!$selectedCycleStart || $selectedCycleStart->day !== 25) {
        abort(400, 'Cycle start must be the 25th of a month.');
    }

    $cycleEnd = $selectedCycleStart->copy()->addMonthNoOverflow()->subDay();
    $precedingCycleStart = $selectedCycleStart->copy()->subMonthNoOverflow();
    $precedingCycleEnd = $selectedCycleStart->copy()->subDay();

    // Fetch national Cash Health data (existing behaviour).
    $response = Http::timeout(120)->get($apiUrl . '/cash-health/national', [
        'cycle_start' => $cycleStart,
    ]);

    if (!$response->successful()) {
        abort($response->status(), 'Unable to retrieve National Cash Health data.');
    }

    // Get the current institution balance from the company summary endpoint.
    $summaryResponse = Http::withHeaders([
        'x-user-email' => 'chikwetihenry@gmail.com',
    ])->get('https://withinheremobileapi.com/api/v1/business-dashboard/company/CMP-35230338/summary');

    $totalBalance = 0.0;
    if ($summaryResponse->successful()) {
        $totalBalance = (float) data_get($summaryResponse->json(), 'data.summary.total_balance', 0);
    }

    // Transactions are needed from the earliest comparison date through today.
    // Historical balances are estimates reconstructed backwards from today's balance.
    // The ledger and company summary must cover the same wallet/account scope for this to be reliable.
    $todayCycleStart = $currentCycleStart->copy();
    $todayLastCycleStart = $todayCycleStart->copy()->subMonthNoOverflow();
    $fetchStart = $precedingCycleStart->lt($todayLastCycleStart)
        ? $precedingCycleStart->copy()
        : $todayLastCycleStart->copy();

    $officesWithWallets = Office::whereNotNull('withinhere_wallet_id')->get();
    $walletIds = $officesWithWallets->pluck('withinhere_wallet_id')->filter()->values()->all();
    $transactions = [];

    if (!empty($walletIds)) {
        $ledgerResponse = Http::timeout(120)->post(
            'https://withinheremobileapi.com/api/v1/lmsuser/executive_ledger',
            [
                'wallet_ids' => $walletIds,
                'start_date' => $fetchStart->format('Y-m-d'),
                'end_date' => $today->format('Y-m-d'),
            ]
        );

        if ($ledgerResponse->successful() && is_array(data_get($ledgerResponse->json(), 'data'))) {
            foreach (data_get($ledgerResponse->json(), 'data', []) as $wallet) {
                foreach (($wallet['transactions'] ?? []) as $transaction) {
                    $transactions[] = $transaction;
                }
            }
        }
    }

    // Work out the cash movement represented by each transaction.
    // total_amount is used because a K215 withdrawal with K11 charges removes K226.
    // Only completed transactions affect balances; unrecognized transaction types
    // are skipped and should be checked against real ledger data before trusting totals.
    $signedTransactions = [];
    $unclassifiedTransactionCount = 0;
    foreach ($transactions as $transaction) {
        // Do not treat missing statuses as completed: that can count pending or
        // incomplete records and materially distort reconstructed balances.
        $status = strtolower(trim((string) ($transaction['status'] ?? '')));
        $transferStatus = strtolower(trim((string) ($transaction['transfer_status'] ?? '')));
        if ($status !== 'completed') {
            continue;
        }
        if ($transferStatus !== '' && !in_array($transferStatus, ['completed', 'n/a'], true)) {
            continue;
        }

        // Cash movement rules requested for this report:
        // payment = money received; withdraw/withdrawal = money paid out;
        // transfer = money leaving the institution, regardless of payout_type.
        // The API sample uses "withdrawal", so support it as an alias for "withdraw".
        $type = strtolower(trim((string) ($transaction['type'] ?? '')));
        $amount = (float) ($transaction['total_amount'] ?? $transaction['amount'] ?? 0);
        $signedAmount = null;

        if ($type === 'payment' || $type === 'wallet_deposit') {
            // Both transaction types increase the wallet balance.
            $signedAmount = abs($amount);
        } elseif (in_array($type, ['withdraw', 'withdrawal'], true)) {
            $signedAmount = -abs($amount);
        } elseif ($type === 'transfer') {
            $signedAmount = -abs($amount);
        }

        // Skip unknown transaction types instead of guessing their cash impact.
        if ($signedAmount === null) {
            $unclassifiedTransactionCount++;
            continue;
        }

        $createdAt = $transaction['created_at'] ?? null;
        if (!$createdAt) {
            continue;
        }

        try {
            // created_at is returned as UTC (often ending in Z); use the app's
            // local timezone so transactions land on the correct Lusaka calendar day.
            $transactionDate = Carbon::parse($createdAt)
                ->setTimezone(config('app.timezone', 'Africa/Lusaka'))
                ->toDateString();
        } catch (\Exception $e) {
            continue;
        }

        $signedTransactions[] = [
            'date' => $transactionDate,
            'type' => in_array($type, ['withdraw', 'withdrawal'], true) ? 'withdrawal' : $type,
            'signed_amount' => $signedAmount,
        ];
    }

    // Reconstruct a historical end-of-day balance BACKWARDS from today's balance.
    // Reverse rules requested:
    // - A withdrawal reduced cash at the time, so ADD it back when going backwards.
    // - A payment increased cash at the time, so SUBTRACT it when going backwards.
    // - A transfer reduced cash at the time, so ADD it back when going backwards.
    // Transactions on the target date are already part of that date's closing balance;
    // only transactions strictly AFTER the target date are reversed.
    $balanceAtEndOfDay = function (Carbon $date) use ($totalBalance, $signedTransactions, $today) {
        if ($date->greaterThanOrEqualTo($today)) {
            return $totalBalance;
        }

        $reverseAdjustment = 0.0;
        $dateString = $date->toDateString();

        foreach ($signedTransactions as $transaction) {
            if ($transaction['date'] <= $dateString) {
                continue;
            }

            $amount = abs((float) $transaction['signed_amount']);
            $type = $transaction['type'];

            if (in_array($type, ['withdraw', 'withdrawal', 'transfer'], true)) {
                // Cash went out after the target date: add it back to recover the
                // earlier balance.
                $reverseAdjustment += $amount;
            } elseif (in_array($type, ['payment', 'wallet_deposit'], true)) {
                // Cash came in after the target date: remove it to recover the
                // earlier balance.
                $reverseAdjustment -= $amount;
            }
        }

        return $totalBalance + $reverseAdjustment;
    };

    $cycleMetrics = function (Carbon $start, Carbon $end) use ($today, $balanceAtEndOfDay) {
        $effectiveEnd = $end->greaterThan($today) ? $today->copy() : $end->copy();
        if ($effectiveEnd->lt($start)) {
            return [
                'closing_balance' => 0.0,
                'average_balance' => 0.0,
                'highest_balance' => 0.0,
                'highest_date' => null,
            ];
        }

        $dailyBalances = [];
        $day = $start->copy();
        while ($day->lte($effectiveEnd)) {
            $dailyBalances[] = [
                'date' => $day->toDateString(),
                'balance' => $balanceAtEndOfDay($day),
            ];
            $day->addDay();
        }

        $values = array_column($dailyBalances, 'balance');
        $highestIndex = array_search(max($values), $values, true);
        $closingDate = $end->greaterThan($today) ? $today->copy() : $end->copy();

        return [
            'closing_balance' => $balanceAtEndOfDay($closingDate),
            'average_balance' => array_sum($values) / count($values),
            'highest_balance' => max($values),
            'highest_date' => $dailyBalances[$highestIndex]['date'] ?? null,
        ];
    };

    $precedingMetrics = $cycleMetrics($precedingCycleStart, $precedingCycleEnd);
    $currentMetrics = $cycleMetrics($selectedCycleStart, $cycleEnd);

    // Compare today's balance with the equivalent elapsed day in the current cycle
    // immediately before this one (25th-to-24th cycle comparison).
    $elapsedCycleDays = $todayCycleStart->diffInDays($today);
    $lastCycleComparisonDate = $todayLastCycleStart->copy()->addDays($elapsedCycleDays);
    if ($lastCycleComparisonDate->greaterThan($todayLastCycleStart->copy()->addMonthNoOverflow()->subDay())) {
        $lastCycleComparisonDate = $todayLastCycleStart->copy()->addMonthNoOverflow()->subDay();
    }

    $cashBalanceMetrics = [
        // Diagnostic values help verify whether the ledger can explain the balance.
        'diagnostics' => [
            'ledger_transaction_count' => count($transactions),
            'classified_transaction_count' => count($signedTransactions),
            'unclassified_transaction_count' => $unclassifiedTransactionCount,
            'reconstruction_note' => 'Historical balances are reconstructed backwards from today: add withdrawals and transfers after the target date; subtract payments and wallet deposits after the target date.',
        ],
        'preceding' => $precedingMetrics,
        'current' => $currentMetrics,
        'today' => [
            'balance' => $totalBalance,
            'last_cycle_balance' => $balanceAtEndOfDay($lastCycleComparisonDate),
            'last_cycle_date' => $lastCycleComparisonDate->toDateString(),
        ],
    ];

    // Keep the existing 12-cycle selector.
    $availableCycles = [];
    $cycle = $currentCycleStart->copy();
    for ($i = 0; $i < 12; $i++) {
        $start = $cycle->copy();
        $end = $start->copy()->addMonthNoOverflow()->subDay();
        $availableCycles[] = [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];
        $cycle->subMonthNoOverflow();
    }

    $nationalHealth = $response->json();
    $nationalHealth['provinces'] = $nationalHealth['provinces'] ?? [];

    return view('cash-health.national_adjusted', compact(
        'nationalHealth',
        'cycleStart',
        'cycleEnd',
        'availableCycles',
        'offices',
        'totalBalance',
        'cashBalanceMetrics'
    ));
}




public function nationalBalances()
{
    $offices = Office::with(['province', 'district'])
        ->get(['id', 'name', 'province_id', 'district_id', 'withinhere_wallet_id']);

    $balances = [];

    foreach ($offices as $office) {

        $balances[] = [
            'office_id' => $office->id,
            'office_name' => $office->name,
            'province_id' => $office->province_id,
            'district_id' => $office->district_id,
            'province_name' => optional($office->province)->name ?? 'Unknown Province',
            'district_name' => optional($office->district)->name ?? 'Unknown District',
            'wallet_id' => $office->withinhere_wallet_id
        ];
    }

    return response()->json([
        'success' => true,
        'offices' => $balances
    ]);
}



public function nationalOfficeBalance($officeId)
{
    $office = Office::find($officeId);

    if (!$office) {

        return response()->json([
            'success' => false,
            'message' => 'Office not found.'
        ], 404);
    }

    if (!$office->withinhere_wallet_id) {

        return response()->json([
            'success' => false,
            'office_id' => $office->id,
            'balance' => null,
            'message' => 'Please verify withinhere wallet.'
        ]);
    }

    $cashBalance = null;

    try {

        /*
         * ---------------------------------------------------------
         * SAME METHOD USED IN transactions()
         * ---------------------------------------------------------
         */

        $response = Http::timeout(180)
            ->connectTimeout(30)
            ->retry(2, 100)
            ->post(
                'https://withinheremobileapi.com/api/v1/lmsuser/branch_ledger',
                [
                    'wallet_id' => $office->withinhere_wallet_id,
                    'start_date' => '2026-01-01',
                    'end_date' => now()->format('Y-m-d')
                ]
            );

        if ($response->successful()) {

            $data = $response->json();

            $cashBalance =
                $data['user']['cash_balance'] ?? null;
        }

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'office_id' => $office->id,
            'balance' => null,
            'message' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'office_id' => $office->id,
        'province_id' => $office->province_id,
        'district_id' => $office->district_id,
        'balance' => $cashBalance
    ]);
}



}
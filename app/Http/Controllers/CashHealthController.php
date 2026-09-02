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

        $response = Http::timeout(10)
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

        $response = Http::timeout(15)
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
                'availableCycles'
            )
        );
    }

    public function province(Request $request, $province)
{
    $apiUrl = 'https://lms2backend.whencefinancesystem.com';


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
            'availableCycles'
        )
    );
}


public function national(Request $request)
{
    $apiUrl = 'https://lms2backend.whencefinancesystem.com';


    $offices = Office::get()->keyBy('id');
    /*
    |--------------------------------------------------------------------------
    | CURRENT CYCLE
    |--------------------------------------------------------------------------
    |
    | National cycles in your example start on the 27th.
    |
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
    | CALL NATIONAL API
    |--------------------------------------------------------------------------
    */

    $response = Http::timeout(60)
        ->get(
            $apiUrl . '/cash-health/national',
            [
                'cycle_start' => $cycleStart
            ]
        );


    if (!$response->successful()) {

        abort(
            $response->status(),
            'Unable to retrieve National Cash Health data.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NATIONAL DATA
    |--------------------------------------------------------------------------
    */

    $nationalHealth = $response->json();

    $nationalHealth['provinces'] =
    $nationalHealth['provinces'] ?? [];


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE CYCLES
    |--------------------------------------------------------------------------
    |
    | Show the previous 12 cycles in the selector.
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
        'cash-health.national',
        compact(
            'nationalHealth',
            'cycleStart',
            'cycleEnd',
            'availableCycles',
            'offices'
        )
    );
}





}
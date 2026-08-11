<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleInsurance;
use App\Models\VehicleDocument;
use App\Models\VehiclePhoto;
use App\Models\Client;
use App\Models\VehicleInspection;
use App\Models\VehicleInspectionPhoto;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use App\Models\Loan;
use App\Models\VehicleCustody;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Office;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;


class PayrollloanController extends Controller
{


public function dashboard(Request $request)
{
    // Default dates: beginning of year to today
    $start_date = $request->start_date
        ?? Carbon::now()->startOfYear()->format('Y-m-d');

    $end_date = $request->end_date
        ?? Carbon::now()->format('Y-m-d');


    try {

        /*
        |--------------------------------------------------------------------------
        | Payroll Loan Information
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(60)->get(
            'https://lms2backend.whencefinancesystem.com/payroll-loans-info',
            [
                'start_date' => $start_date,
                'end_date'   => $end_date
            ]
        );


        if (!$response->successful()) {

            return back()->with(
                'error',
                'Unable to load payroll dashboard data'
            );

        }


        $data = $response->json();


        /*
        |--------------------------------------------------------------------------
        | Payroll Loan Consultant Information
        |--------------------------------------------------------------------------
        */

        $consultantResponse = Http::timeout(60)->get(
            'https://lms2backend.whencefinancesystem.com/payroll-loan-consultant-info',
            [
                'start_date' => $start_date,
                'end_date'   => $end_date
            ]
        );


        if (!$consultantResponse->successful()) {

            \Log::error(
                'Payroll Consultant Endpoint Error: ' .
                $consultantResponse->body()
            );


            $consultantData = [
                'success' => false,
                'consultants' => [],
                'national' => []
            ];

        } else {

            $consultantData =
                $consultantResponse->json();

        }


        /*
        |--------------------------------------------------------------------------
        | Make sure consultants always exists
        |--------------------------------------------------------------------------
        */

        $consultants =
            $consultantData['consultants'] ?? [];


        /*
        |--------------------------------------------------------------------------
        | Return Payroll Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'payroll_loans.dashboard',
            compact(
                'data',
                'consultantData',
                'consultants',
                'start_date',
                'end_date'
            )
        );


    } catch (\Exception $e) {

        \Log::error(
            'Payroll Dashboard Error: ' .
            $e->getMessage()
        );


        return back()->with(
            'error',
            'Error loading payroll dashboard'
        );

    }
}


}
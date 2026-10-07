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
    $PAYROLL = 1; // loan_product_id for payroll loans

    // ─── 1. STATS CARDS ────────────────────────────────────────────────────────

    // All payroll loans (any status)
    $allLoans = \DB::table('loans')
        ->where('loan_product_id', $PAYROLL)
        ->whereNull('deleted_at')
        ->select(
            \DB::raw('COUNT(*) as total_count'),
            \DB::raw('SUM(principal) as total_given_out')
        )
        ->first();

    $total_count     = $allLoans->total_count ?? 0;
    $total_given_out = $allLoans->total_given_out ?? 0;
    $expected_interest = $total_given_out * 0.40; // 40% of principal

    // Transaction totals — only for payroll loans
    $txTotals = \DB::table('loan_transactions as lt')
        ->join('loans as l', 'l.id', '=', 'lt.loan_id')
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->select(
            \DB::raw('SUM(lt.debit) as expected_collections'),
            \DB::raw('SUM(lt.credit) as total_collections')
        )
        ->first();

    $expected_collections = $txTotals->expected_collections ?? 0;
    $total_collections    = $txTotals->total_collections    ?? 0;
    $total_uncollected    = $expected_collections - $total_collections;

    // Loan Portfolio = principal of active (disbursed) loans
    $loan_portfolio = \DB::table('loans')
        ->where('loan_product_id', $PAYROLL)
        ->where('status', 'disbursed')
        ->whereNull('deleted_at')
        ->sum('principal');

    $stats = compact(
        'total_count',
        'loan_portfolio',
        'expected_collections',
        'total_collections',
        'total_given_out',
        'expected_interest',
        'total_uncollected'
    );

    // ─── 2. CONSULTANT PERFORMANCE ─────────────────────────────────────────────

    $consultants = \DB::table('loans as l')
        ->join('users as u', 'u.id', '=', 'l.loan_consultant_id')
        ->leftJoin('loan_transactions as lt', 'lt.loan_id', '=', 'l.id')
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->groupBy('l.loan_consultant_id', 'u.first_name', 'u.last_name')
        ->select(
            'l.loan_consultant_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id) as loans'),
            \DB::raw('SUM(l.principal) as total_principal'),
            \DB::raw('SUM(l.principal) * 0.40 as expected_interest'),
            \DB::raw('SUM(lt.debit) as expected_collections'),
            \DB::raw('SUM(lt.credit) as collections'),
            \DB::raw('SUM(lt.debit) - SUM(lt.credit) as uncollected')
        )
        ->orderByDesc('loans')
        ->get();

    // ─── 3. PROVINCE → OFFICE → CONSULTANT DRILLDOWN ───────────────────────────

    $drilldown = \DB::table('loans as l')
        ->join('offices as o', 'o.id', '=', 'l.office_id')
        ->join('province as p', 'p.id', '=', 'o.province_id')
        ->join('users as u', 'u.id', '=', 'l.loan_consultant_id')
        ->leftJoin('loan_transactions as lt', 'lt.loan_id', '=', 'l.id')
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->groupBy('p.id', 'p.name', 'o.id', 'o.name', 'l.loan_consultant_id', 'u.first_name', 'u.last_name')
        ->select(
            'p.id as province_id',
            'p.name as province_name',
            'o.id as office_id',
            'o.name as office_name',
            'l.loan_consultant_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id) as loans'),
            \DB::raw('SUM(l.principal) * 0.40 as expected_interest'),
            \DB::raw('SUM(lt.debit) as expected_collections'),
            \DB::raw('SUM(lt.credit) as collections'),
            \DB::raw('SUM(lt.debit) - SUM(lt.credit) as uncollected')
        )
        ->orderBy('p.name')
        ->orderBy('o.name')
        ->orderBy('consultant_name')
        ->get()
        // Group: province -> offices -> consultants
        ->groupBy('province_name')
        ->map(function ($provinceRows) {
            return $provinceRows->groupBy('office_name');
        });

    return view('payroll_loans.dashboard', compact('stats', 'consultants', 'drilldown'));
}


public function activeLoans(Request $request)
{
    $loans = Loan::with(['client', 'office', 'loan_officer'])
        ->where('loan_product_id', 1)
        ->where('status', 'disbursed')
        ->latest()
        ->get();

    return view('payroll_loans.active', compact('loans'));
}


public function pendingLoans(Request $request)
{
    $loans = Loan::with(['client', 'office', 'loan_officer'])
        ->where('loan_product_id', 1)
        ->where('status', 'pending')
        ->latest()
        ->get();

    return view('payroll_loans.pending', compact('loans'));
}


public function pendingDisbursementLoans(Request $request)
{
    $loans = Loan::with(['client', 'office', 'loan_officer'])
        ->where('loan_product_id', 1)
        ->where('status', 'approved')
        ->latest()
        ->get();

    return view('payroll_loans.pending_disbursement', compact('loans'));
}


public function closedLoans(Request $request)
{
    $loans = Loan::with(['client', 'office', 'loan_officer'])
        ->where('loan_product_id', 1)
        ->where('status', 'closed')
        ->latest()
        ->get();

    return view('payroll_loans.closed', compact('loans'));
}


}
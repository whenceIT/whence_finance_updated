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
    try {

        // ── Date range ────────────────────────────────────────────────────────
        $start_date = $request->input('start_date', date('Y-01-01'));
        $end_date   = $request->input('end_date',   date('Y-m-d'));

        // ── Fetch all payroll loans (product 1) disbursed within the period ──
        // Eager-load repayment transactions only to avoid pulling every type
        $loans = Loan::with([
                'loan_officer',   // users: first_name, last_name
                'office',         // offices: name, province_id
                'office.province',// province: name
                'client',         // clients: firstname, lastname
                'transactions' => function ($q) {
                    // Only approved, non-reversed repayment transactions
                    $q->where('transaction_type', 'repayment')
                      ->where('status', 'approved')
                      ->where('reversed', 0);
                },
            ])
            ->where('loan_product_id', 1)
            ->whereIn('status', ['disbursed', 'closed'])
            ->whereBetween('disbursement_date', [
                $start_date . ' 00:00:00',
                $end_date   . ' 23:59:59',
            ])
            ->get();

        // ── Per-loan helpers ──────────────────────────────────────────────────
        // Expected Collections = SUM(loan_transactions.debit)
        // Total Collections    = SUM(loan_transactions.credit)
        // Total Uncollected    = SUM(debit - credit)
        // Total Given Out      = loans.principal
        // Expected Interest    = loans.principal * 0.40
        $txDebit  = fn($loan) => (float) $loan->transactions->sum('debit');
        $txCredit = fn($loan) => (float) $loan->transactions->sum('credit');

        // ── Accumulator factory ───────────────────────────────────────────────
        $emptyBucket = fn() => [
            'number_of_loans'      => 0,
            'expected_collections' => 0.0,   // SUM(debit)
            'expected_interest'    => 0.0,   // SUM(principal * 0.40)
            'total_collections'    => 0.0,   // SUM(credit)
            'total_uncollected'    => 0.0,   // SUM(debit - credit)
        ];

        // ── Build consultant map & province hierarchy in one pass ─────────────
        $consultantMap = [];
        $provinceMap   = [];

        foreach ($loans as $loan) {
            $officerId   = $loan->loan_officer_id ?? 0;
            $officer     = $loan->loan_officer;
            $office      = $loan->office;
            $province    = $office ? $office->province : null;

            $provinceId   = $province ? $province->id   : 0;
            $provinceName = $province ? $province->name : 'Unknown';
            $officeId     = $office   ? $office->id     : 0;
            $officeName   = $office   ? $office->name   : 'Unknown';
            $officerName  = $officer
                ? trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''))
                : 'Unknown';
            $clientName   = $loan->client
                ? trim(($loan->client->firstname ?? '') . ' ' . ($loan->client->lastname ?? ''))
                : 'Unknown';

            $principal       = (float) ($loan->principal ?? 0);
            $expInterest     = round($principal * 0.40, 4);
            $expCollections  = $txDebit($loan);
            $totalCollected  = $txCredit($loan);
            $uncollected     = max(0, $expCollections - $totalCollected);

            // ── Consultant map ────────────────────────────────────────────────
            if (!isset($consultantMap[$officerId])) {
                $consultantMap[$officerId] = array_merge($emptyBucket(), [
                    'consultant_name' => $officerName,
                    'branch_name'     => $officeName,
                    'province_name'   => $provinceName,
                    'loans_list'      => [],
                ]);
            }

            $consultantMap[$officerId]['number_of_loans']++;
            $consultantMap[$officerId]['expected_collections'] += $expCollections;
            $consultantMap[$officerId]['expected_interest']    += $expInterest;
            $consultantMap[$officerId]['total_collections']    += $totalCollected;
            $consultantMap[$officerId]['total_uncollected']    += $uncollected;

            // Loan detail row attached to consultant
            $consultantMap[$officerId]['loans_list'][] = [
                'loan_id'              => $loan->id,
                'client_name'          => $clientName,
                'principal'            => $principal,
                'expected_interest'    => $expInterest,
                'expected_collections' => $expCollections,
                'total_collections'    => $totalCollected,
                'total_uncollected'    => $uncollected,
                'status'               => $loan->status,
                'date'                 => $loan->disbursement_date
                                            ? Carbon::parse($loan->disbursement_date)->format('d/m/Y')
                                            : '—',
                'due_date'             => $loan->expected_maturity_date
                                            ? Carbon::parse($loan->expected_maturity_date)->format('d/m/Y')
                                            : '—',
            ];

            // ── Province map ──────────────────────────────────────────────────
            if (!isset($provinceMap[$provinceId])) {
                $provinceMap[$provinceId] = array_merge($emptyBucket(), [
                    'province_name' => $provinceName,
                    'branches'      => [],
                ]);
            }
            $provinceMap[$provinceId]['number_of_loans']++;
            $provinceMap[$provinceId]['expected_collections'] += $expCollections;
            $provinceMap[$provinceId]['expected_interest']    += $expInterest;
            $provinceMap[$provinceId]['total_collections']    += $totalCollected;
            $provinceMap[$provinceId]['total_uncollected']    += $uncollected;

            // Branch level
            if (!isset($provinceMap[$provinceId]['branches'][$officeId])) {
                $provinceMap[$provinceId]['branches'][$officeId] = array_merge($emptyBucket(), [
                    'branch_name'  => $officeName,
                    'consultants'  => [],
                ]);
            }
            $provinceMap[$provinceId]['branches'][$officeId]['number_of_loans']++;
            $provinceMap[$provinceId]['branches'][$officeId]['expected_collections'] += $expCollections;
            $provinceMap[$provinceId]['branches'][$officeId]['expected_interest']    += $expInterest;
            $provinceMap[$provinceId]['branches'][$officeId]['total_collections']    += $totalCollected;
            $provinceMap[$provinceId]['branches'][$officeId]['total_uncollected']    += $uncollected;

            // Consultant inside branch
            if (!isset($provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId])) {
                $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId] = array_merge($emptyBucket(), [
                    'consultant_name' => $officerName,
                ]);
            }
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['number_of_loans']++;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['expected_collections'] += $expCollections;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['expected_interest']    += $expInterest;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['total_collections']    += $totalCollected;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['total_uncollected']    += $uncollected;
        }

        // Re-index to plain arrays for the blade
        $consultants = array_values($consultantMap);
        foreach ($provinceMap as &$prov) {
            foreach ($prov['branches'] as &$branch) {
                $branch['consultants'] = array_values($branch['consultants']);
            }
            $prov['branches'] = array_values($prov['branches']);
        }
        unset($prov, $branch);

        // ── National totals ───────────────────────────────────────────────────
        $totalPrincipal      = (float) $loans->sum('principal');
        $totalExpCollections = (float) $loans->sum(fn($l) => $txDebit($l));
        $totalCollections    = (float) $loans->sum(fn($l) => $txCredit($l));

        $national = [
            'number_of_loans'      => $loans->count(),
            'total_given_out'       => $totalPrincipal,
            'expected_interest'     => round($totalPrincipal * 0.40, 2),
            'expected_collections'  => $totalExpCollections,
            'total_collections'     => $totalCollections,
            'total_uncollected'     => max(0, $totalExpCollections - $totalCollections),
        ];

        $data = [
            'national'  => $national,
            'provinces' => array_values($provinceMap),
        ];

        return view('payroll_loans.dashboard', compact(
            'data',
            'consultants',
            'start_date',
            'end_date'
        ));

    } catch (\Exception $e) {
        Log::error('Payroll Dashboard Error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
        return back()->withErrors(['error' => 'Failed to load dashboard: ' . $e->getMessage()]);
    }
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
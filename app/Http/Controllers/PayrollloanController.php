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
        $loans = Loan::with([
                'loan_officer',          // users (first_name, last_name)
                'office',                // offices (name, province_id)
                'office.province',       // provinces (name)
                'client',                // clients (firstname, lastname)
                'transactions',          // loan_transactions
            ])
            ->where('loan_product_id', 1)
            ->whereIn('status', ['disbursed', 'closed'])
            ->whereBetween('disbursement_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->get();

        // ── Helper: sum approved repayment transactions for a loan ───────────
        $getCollections = function ($loan) {
            return $loan->transactions
                ->where('transaction_type', 'repayment')
                ->where('status', 'approved')
                ->where('reversed', 0)
                ->sum('amount');
        };

        // ── Build per-consultant rows ─────────────────────────────────────────
        $consultantMap = [];
        $today = \Carbon\Carbon::today();

        foreach ($loans as $loan) {
            $officerId  = $loan->loan_officer_id ?? 0;
            $officer    = $loan->loan_officer;
            $office     = $loan->office;
            $province   = $office ? $office->province : null;

            $givenOut          = (float) ($loan->approved_amount ?? 0);
            $expectedInterest  = (float) ($loan->interest_derived ?? 0);
            $expectedCollect   = $givenOut + $expectedInterest;
            $collected         = $getCollections($loan);
            $uncollected       = max(0, $expectedCollect - $collected);

            $dueDate   = $loan->expected_maturity_date ?? $loan->final_due_date ?? null;
            $daysDefault = 0;
            if ($dueDate && \Carbon\Carbon::parse($dueDate)->lt($today) && $uncollected > 0) {
                $daysDefault = \Carbon\Carbon::parse($dueDate)->diffInDays($today);
            }

            // Loan detail row
            $loanRow = [
                'loan_id'              => $loan->id,
                'client_name'          => $loan->client
                                            ? trim(($loan->client->firstname ?? '') . ' ' . ($loan->client->lastname ?? ''))
                                            : 'Unknown',
                'referrer_name'        => $officer
                                            ? trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''))
                                            : 'Unknown',
                'given_out'            => $givenOut,
                'expected_interest'    => $expectedInterest,
                'expected_collections' => $expectedCollect,
                'total_collections'    => $collected,
                'total_uncollected'    => $uncollected,
                'status'               => $loan->status,
                'date'                 => $loan->disbursement_date
                                            ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d/m/Y')
                                            : '',
                'due_date'             => $dueDate
                                            ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y')
                                            : '',
                'days_in_default'      => $daysDefault,
            ];

            if (!isset($consultantMap[$officerId])) {
                $consultantMap[$officerId] = [
                    'consultant_name'      => $officer
                                                ? trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''))
                                                : 'Unknown',
                    'branch_name'          => $office   ? $office->name         : 'Unknown',
                    'province_name'        => $province ? $province->name        : 'Unknown',
                    'number_of_loans'      => 0,
                    'given_out'            => 0,
                    'expected_interest'    => 0,
                    'expected_collections' => 0,
                    'total_collections'    => 0,
                    'total_uncollected'    => 0,
                    'loans_list'           => [],
                ];
            }

            $consultantMap[$officerId]['number_of_loans']++;
            $consultantMap[$officerId]['given_out']            += $givenOut;
            $consultantMap[$officerId]['expected_interest']    += $expectedInterest;
            $consultantMap[$officerId]['expected_collections'] += $expectedCollect;
            $consultantMap[$officerId]['total_collections']    += $collected;
            $consultantMap[$officerId]['total_uncollected']    += $uncollected;
            $consultantMap[$officerId]['loans_list'][]          = $loanRow;
        }

        $consultants = array_values($consultantMap);

        // ── Build province → branch → consultant hierarchy ────────────────────
        $provinceMap = [];

        foreach ($loans as $loan) {
            $officerId = $loan->loan_officer_id ?? 0;
            $office    = $loan->office;
            $province  = $office ? $office->province : null;

            $provinceId  = $province  ? $province->id   : 0;
            $provinceName = $province ? $province->name  : 'Unknown';
            $officeId    = $office    ? $office->id      : 0;
            $officeName  = $office    ? $office->name    : 'Unknown';

            $givenOut         = (float) ($loan->approved_amount ?? 0);
            $expectedInterest = (float) ($loan->interest_derived ?? 0);
            $expectedCollect  = $givenOut + $expectedInterest;
            $collected        = $getCollections($loan);
            $uncollected      = max(0, $expectedCollect - $collected);

            $officer      = $loan->loan_officer;
            $officerName  = $officer
                ? trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''))
                : 'Unknown';

            // Province level
            if (!isset($provinceMap[$provinceId])) {
                $provinceMap[$provinceId] = [
                    'province_name'        => $provinceName,
                    'number_of_loans'      => 0,
                    'expected_collections' => 0,
                    'expected_interest'    => 0,
                    'total_collections'    => 0,
                    'total_uncollected'    => 0,
                    'branches'             => [],
                ];
            }

            $provinceMap[$provinceId]['number_of_loans']++;
            $provinceMap[$provinceId]['expected_collections'] += $expectedCollect;
            $provinceMap[$provinceId]['expected_interest']    += $expectedInterest;
            $provinceMap[$provinceId]['total_collections']    += $collected;
            $provinceMap[$provinceId]['total_uncollected']    += $uncollected;

            // Branch level
            if (!isset($provinceMap[$provinceId]['branches'][$officeId])) {
                $provinceMap[$provinceId]['branches'][$officeId] = [
                    'branch_name'          => $officeName,
                    'number_of_loans'      => 0,
                    'expected_collections' => 0,
                    'expected_interest'    => 0,
                    'total_collections'    => 0,
                    'total_uncollected'    => 0,
                    'consultants'          => [],
                ];
            }

            $provinceMap[$provinceId]['branches'][$officeId]['number_of_loans']++;
            $provinceMap[$provinceId]['branches'][$officeId]['expected_collections'] += $expectedCollect;
            $provinceMap[$provinceId]['branches'][$officeId]['expected_interest']    += $expectedInterest;
            $provinceMap[$provinceId]['branches'][$officeId]['total_collections']    += $collected;
            $provinceMap[$provinceId]['branches'][$officeId]['total_uncollected']    += $uncollected;

            // Consultant level inside branch
            if (!isset($provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId])) {
                $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId] = [
                    'consultant_name'      => $officerName,
                    'number_of_loans'      => 0,
                    'given_out'            => 0,
                    'expected_collections' => 0,
                    'expected_interest'    => 0,
                    'total_collections'    => 0,
                    'total_uncollected'    => 0,
                ];
            }

            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['number_of_loans']++;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['given_out']            += $givenOut;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['expected_collections'] += $expectedCollect;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['expected_interest']    += $expectedInterest;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['total_collections']    += $collected;
            $provinceMap[$provinceId]['branches'][$officeId]['consultants'][$officerId]['total_uncollected']    += $uncollected;
        }

        // Re-index branches and consultants to plain arrays
        foreach ($provinceMap as &$prov) {
            foreach ($prov['branches'] as &$branch) {
                $branch['consultants'] = array_values($branch['consultants']);
            }
            $prov['branches'] = array_values($prov['branches']);
        }
        unset($prov, $branch);

        // ── National totals ───────────────────────────────────────────────────
        $national = [
            'number_of_loans'      => $loans->count(),
            'total_loan_portfolion' => $loans->sum('approved_amount'),
            'expected_collections'  => $loans->sum(fn($l) => (float)($l->approved_amount ?? 0) + (float)($l->interest_derived ?? 0)),
            'total_collections'     => $loans->sum(fn($l) => $getCollections($l)),
        ];
        $national['total_uncollected'] = max(0, $national['expected_collections'] - $national['total_collections']);

        $consultantNational = [
            'given_out'         => $loans->sum('approved_amount'),
            'expected_interest' => $loans->sum('interest_derived'),
            'total_uncollected' => $national['total_uncollected'],
        ];

        $data = [
            'national'  => $national,
            'provinces' => array_values($provinceMap),
        ];

        $consultantData = [
            'national' => $consultantNational,
        ];

        return view('payroll_loans.dashboard', compact(
            'data',
            'consultantData',
            'consultants',
            'start_date',
            'end_date'
        ));

    } catch (\Exception $e) {
        Log::error('Payroll Dashboard Error: ' . $e->getMessage());
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
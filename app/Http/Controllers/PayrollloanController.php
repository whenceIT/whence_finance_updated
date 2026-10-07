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
        ->join('users as u', 'u.id', '=', 'l.loan_officer_id')
        ->leftJoin(
            \DB::raw('(SELECT loan_id,
                              SUM(debit)             AS tx_debit,
                              SUM(credit)            AS tx_credit,
                              SUM(debit)-SUM(credit) AS tx_uncollected
                       FROM loan_transactions
                       GROUP BY loan_id) AS lt'),
            'lt.loan_id', '=', 'l.id'
        )
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->groupBy('l.loan_officer_id', 'u.first_name', 'u.last_name')
        ->select(
            'l.loan_officer_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id)        as loans'),
            \DB::raw('SUM(l.principal)            as total_principal'),
            \DB::raw('SUM(l.principal) * 0.40     as expected_interest'),
            \DB::raw('SUM(lt.tx_debit)            as expected_collections'),
            \DB::raw('SUM(lt.tx_credit)           as collections'),
            \DB::raw('SUM(lt.tx_uncollected)      as uncollected')
        )
        ->orderByDesc('loans')
        ->get();

        // dd($consultants);

    // ─── 3. PROVINCE → OFFICE → CONSULTANT DRILLDOWN ───────────────────────────

    $drilldown = \DB::table('loans as l')
        ->join('offices as o', 'o.id', '=', 'l.office_id')
        ->join('province as p', 'p.id', '=', 'o.province_id')
        ->join('users as u', 'u.id', '=', 'l.loan_officer_id')
        ->leftJoin(
            \DB::raw('(SELECT loan_id,
                              SUM(debit)             AS tx_debit,
                              SUM(credit)            AS tx_credit,
                              SUM(debit)-SUM(credit) AS tx_uncollected
                       FROM loan_transactions
                       GROUP BY loan_id) AS lt'),
            'lt.loan_id', '=', 'l.id'
        )
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->groupBy('p.id', 'p.name', 'o.id', 'o.name', 'l.loan_officer_id', 'u.first_name', 'u.last_name')
        ->select(
            'p.id as province_id',
            'p.name as province_name',
            'o.id as office_id',
            'o.name as office_name',
            'l.loan_officer_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id)        as loans'),
            \DB::raw('SUM(l.principal) * 0.40     as expected_interest'),
            \DB::raw('SUM(lt.tx_debit)            as expected_collections'),
            \DB::raw('SUM(lt.tx_credit)           as collections'),
            \DB::raw('SUM(lt.tx_uncollected)      as uncollected')
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


// ─── API: loans for a specific consultant ────────────────────────────────────
public function apiConsultantLoans(Request $request)
{
    $PAYROLL       = 1;
    $consultant_id = $request->query('consultant_id');

    if (!$consultant_id) {
        return response()->json(['data' => [], 'error' => 'consultant_id required'], 400);
    }

    $loans = \DB::table('loans as l')
        ->join('clients as c', 'c.id', '=', 'l.client_id')
        ->join('offices as o', 'o.id', '=', 'l.office_id')
        ->leftJoin('loan_transactions as lt', 'lt.loan_id', '=', 'l.id')
        ->where('l.loan_product_id', $PAYROLL)
        ->where('l.loan_officer_id', $consultant_id)
        ->whereNull('l.deleted_at')
        ->groupBy(
            'l.id', 'l.account_number', 'l.principal', 'l.status',
            'l.disbursement_date', 'o.name',
            'c.first_name', 'c.last_name', 'c.phone'
        )
        ->select(
            'l.id',
            'l.account_number',
            'l.principal',
            'l.status',
            'l.disbursement_date',
            'o.name as office_name',
            \DB::raw("CONCAT(c.first_name, ' ', c.last_name) as client_name"),
            'c.phone as client_phone',
            \DB::raw('SUM(lt.debit)  as expected_collections'),
            \DB::raw('SUM(lt.credit) as collections'),
            \DB::raw('SUM(lt.debit) - SUM(lt.credit) as uncollected'),
            \DB::raw('SUM(l.principal) * 0.40 as expected_interest')
        )
        ->orderBy('l.disbursement_date', 'desc')
        ->get()
        ->map(function ($row) {
            $expCol = $row->expected_collections ?? 0;
            $col    = $row->collections ?? 0;
            return [
                'id'                   => $row->id,
                'account_number'       => $row->account_number,
                'client_name'          => $row->client_name,
                'client_phone'         => $row->client_phone,
                'office_name'          => $row->office_name,
                'principal'            => round($row->principal ?? 0, 2),
                'expected_interest'    => round($row->principal * 0.40, 2),
                'expected_collections' => round($expCol, 2),
                'collections'          => round($col, 2),
                'uncollected'          => round($row->uncollected ?? 0, 2),
                'status'               => $row->status,
                'disbursement_date'    => $row->disbursement_date,
            ];
        });

    return response()->json(['data' => $loans]);
}

// ─── API: consultant performance ────────────────────────────────────────────
public function apiConsultants(Request $request)
{
    $PAYROLL = 1;

    $rows = \DB::table('loans as l')
        ->join('users as u', 'u.id', '=', 'l.loan_officer_id')
        ->leftJoin(
            \DB::raw('(SELECT loan_id,
                              SUM(debit)             AS tx_debit,
                              SUM(credit)            AS tx_credit,
                              SUM(debit)-SUM(credit) AS tx_uncollected
                       FROM loan_transactions
                       GROUP BY loan_id) AS lt'),
            'lt.loan_id', '=', 'l.id'
        )
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at')
        ->groupBy('l.loan_officer_id', 'u.first_name', 'u.last_name')
        ->select(
            'l.loan_officer_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id)        as loans'),
            \DB::raw('SUM(l.principal)            as total_principal'),
            \DB::raw('SUM(l.principal) * 0.40     as expected_interest'),
            \DB::raw('SUM(lt.tx_debit)            as expected_collections'),
            \DB::raw('SUM(lt.tx_credit)           as collections'),
            \DB::raw('SUM(lt.tx_uncollected)      as uncollected')
        )
        ->orderByDesc('loans')
        ->get()
        ->map(function ($r) {
            $rate = $r->expected_collections > 0
                ? round(($r->collections / $r->expected_collections) * 100, 1)
                : 0;
            $r->rate       = $rate;
            $r->rate_class = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
            return $r;
        });

    return response()->json(['data' => $rows]);
}

// ─── API: province → office → consultant drilldown ───────────────────────────
public function apiDrilldown(Request $request)
{
    $PAYROLL     = 1;
    $province_id = $request->query('province_id');
    $office_id   = $request->query('office_id');

    $query = \DB::table('loans as l')
        ->join('offices as o', 'o.id', '=', 'l.office_id')
        ->join('province as p', 'p.id', '=', 'o.province_id')
        ->join('users as u', 'u.id', '=', 'l.loan_officer_id')
        ->leftJoin(
            \DB::raw('(SELECT loan_id,
                              SUM(debit)             AS tx_debit,
                              SUM(credit)            AS tx_credit,
                              SUM(debit)-SUM(credit) AS tx_uncollected
                       FROM loan_transactions
                       GROUP BY loan_id) AS lt'),
            'lt.loan_id', '=', 'l.id'
        )
        ->where('l.loan_product_id', $PAYROLL)
        ->whereNull('l.deleted_at');

    if ($office_id) {
        $query->where('l.office_id', $office_id);
    } elseif ($province_id) {
        $query->where('o.province_id', $province_id);
    }

    $flat = $query
        ->groupBy('p.id', 'p.name', 'o.id', 'o.name', 'l.loan_officer_id', 'u.first_name', 'u.last_name')
        ->select(
            'p.id as province_id',
            'p.name as province_name',
            'o.id as office_id',
            'o.name as office_name',
            'l.loan_officer_id as consultant_id',
            \DB::raw("CONCAT(u.first_name, ' ', u.last_name) as consultant_name"),
            \DB::raw('COUNT(DISTINCT l.id)        as loans'),
            \DB::raw('SUM(l.principal) * 0.40     as expected_interest'),
            \DB::raw('SUM(lt.tx_debit)            as expected_collections'),
            \DB::raw('SUM(lt.tx_credit)           as collections'),
            \DB::raw('SUM(lt.tx_uncollected)      as uncollected')
        )
        ->orderBy('p.name')
        ->orderBy('o.name')
        ->orderBy('consultant_name')
        ->get();

    // If scoped to a single office, return flat consultant array
    if ($office_id) {
        $data = $flat->map(function ($row) {
            $expCol = $row->expected_collections ?? 0;
            $col    = $row->collections ?? 0;
            $rate   = $expCol > 0 ? round(($col / $expCol) * 100, 1) : 0;
            return [
                'consultant_id'        => $row->consultant_id,
                'consultant_name'      => $row->consultant_name,
                'loans'                => $row->loans,
                'expected_interest'    => round($row->expected_interest ?? 0, 2),
                'expected_collections' => round($expCol, 2),
                'collections'          => round($col, 2),
                'uncollected'          => round($row->uncollected ?? 0, 2),
                'rate'                 => $rate,
                'rate_class'           => $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger'),
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    // Province scope — return offices with nested consultants
    $provinces = [];
    foreach ($flat as $row) {
        $pKey = $row->province_id;
        $oKey = $row->office_id;

        if (!isset($provinces[$pKey])) {
            $provinces[$pKey] = [
                'province_id'   => $row->province_id,
                'province_name' => $row->province_name,
                'offices'       => [],
            ];
        }
        if (!isset($provinces[$pKey]['offices'][$oKey])) {
            $provinces[$pKey]['offices'][$oKey] = [
                'office_id'   => $row->office_id,
                'office_name' => $row->office_name,
                'consultants' => [],
            ];
        }
        $expCol = $row->expected_collections ?? 0;
        $col    = $row->collections ?? 0;
        $rate   = $expCol > 0 ? round(($col / $expCol) * 100, 1) : 0;

        $provinces[$pKey]['offices'][$oKey]['consultants'][] = [
            'consultant_id'        => $row->consultant_id,
            'consultant_name'      => $row->consultant_name,
            'loans'                => $row->loans,
            'expected_interest'    => round($row->expected_interest ?? 0, 2),
            'expected_collections' => round($expCol, 2),
            'collections'          => round($col, 2),
            'uncollected'          => round($row->uncollected ?? 0, 2),
            'rate'                 => $rate,
            'rate_class'           => $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger'),
        ];
    }

    $result = array_values(array_map(function ($p) {
        $p['offices'] = array_values($p['offices']);
        return $p;
    }, $provinces));

    return response()->json(['data' => $result]);
}

public function bulkRepayments(Request $request, $loanId)
{
    $request->validate([
        'num_payments' => 'required|integer|min:1|max:60',
    ]);

    $loan = Loan::findOrFail($loanId);

    // Resolve monthly amount from schedule (same logic as loan_schedule_summary.blade.php)
    $tenure = $loan->loan_term;
    if ($loan->schedule_type === 'old') {
        $oldRow = \App\Models\PayrollLoanOldSchedule::where('disbursement_amount', $loan->principal)->first();
        $colMap = [9 => 'repayment_9_months', 12 => 'repayment_12_months', 18 => 'repayment_18_months', 24 => 'repayment_24_months'];
        $col = $colMap[$tenure] ?? null;
        $monthlyAmount = ($oldRow && $col) ? $oldRow->$col : null;
    } else {
        $schedule = \DB::table('payroll_loan_schedules')
            ->where('loan_amount', $loan->principal)
            ->first();
        $monthlyAmount = $schedule ? ($schedule->{"months_$tenure"} ?? null) : null;
    }

    if (!$monthlyAmount) {
        return back()->with('error', 'No schedule found for this loan. Cannot create repayments.');
    }

    $numPayments  = (int) $request->num_payments;
    $user         = Sentinel::getUser();
    $disbursement = $loan->disbursement_date
        ? \Carbon\Carbon::parse($loan->disbursement_date)
        : \Carbon\Carbon::now();

    \DB::transaction(function () use ($loan, $monthlyAmount, $numPayments, $user, $disbursement) {
        for ($i = 1; $i <= $numPayments; $i++) {
            $date = $disbursement->copy()->addMonths($i);

            $tx = new \App\Models\LoanTransaction();
            $tx->loan_id          = $loan->id;
            $tx->office_id        = $loan->office_id;
            $tx->client_id        = $loan->client_id;
            $tx->created_by_id    = $user->id;
            $tx->transaction_type = 'repayment';
            $tx->payment_apply_to = 'part_payment';
            $tx->credit           = $monthlyAmount;
            $tx->debit            = 0;
            $tx->date             = $date->toDateString();
            $tx->month            = $date->month;
            $tx->year             = $date->year;
            $tx->reversible       = 1;
            $tx->notes            = 'Payroll entry (' . $i . ' of ' . $numPayments . ')';
            $tx->save();
        }
    });

    $total = number_format($monthlyAmount * $numPayments, 2);
    Flash::success("Created {$numPayments} repayment transaction(s) of K{$monthlyAmount} each (total K{$total}).");
    return redirect('loan/' . $loanId . '/show');
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
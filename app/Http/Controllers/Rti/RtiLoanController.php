<?php

namespace App\Http\Controllers\Rti;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\OfficeLoan;
use App\Models\OfficeLoanTransaction;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RtiLoanController extends Controller
{
    const RTI_RATE = 0.20; // 20% RTI arrangement

    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function dashboard(Request $request)
    {
        $user   = Sentinel::getUser();
        $offices = Office::where('active', 1)->orderBy('name')->get();

        $query = OfficeLoan::with(['office', 'staff']);

        // Filter: branch
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Filter: loan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->latest()->get();

        // --- Aggregates ---
        $totalLoans       = $loans->count();
        $totalDisbursed   = OfficeLoan::whereIn('status', [
            OfficeLoan::STATUS_DISBURSED,
            OfficeLoan::STATUS_PARTIALLY_PAID,
            OfficeLoan::STATUS_FULLY_PAID,
        ])->sum(DB::raw('principal + interest'));

        $totalPayable     = OfficeLoan::sum(DB::raw('principal + interest'));
        $totalRepaid      = OfficeLoanTransaction::approved()->repayments()->sum('credit');
        $totalOutstanding = max(0, $totalPayable - $totalRepaid);

        $fullyPaid        = OfficeLoan::where('status', OfficeLoan::STATUS_FULLY_PAID)->count();
        $partiallyPaid    = OfficeLoan::where('status', OfficeLoan::STATUS_PARTIALLY_PAID)->count();
        $pendingLoans     = OfficeLoan::where('status', OfficeLoan::STATUS_PENDING)->count();
        $declinedTx       = OfficeLoanTransaction::where('status', OfficeLoanTransaction::STATUS_DECLINED)->count();

        // Pending repayment approvals count
        $pendingRepayments = OfficeLoanTransaction::pending()->repayments()->count();

        return view('rti.dashboard', compact(
            'loans', 'offices',
            'totalLoans', 'totalDisbursed', 'totalPayable',
            'totalRepaid', 'totalOutstanding',
            'fullyPaid', 'partiallyPaid', 'pendingLoans',
            'declinedTx', 'pendingRepayments'
        ));
    }

    // -------------------------------------------------------------------------
    // Index — all loans
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user    = Sentinel::getUser();
        $offices = Office::where('active', 1)->orderBy('name')->get();

        $query = OfficeLoan::with(['office', 'staff']);

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans    = $query->latest()->paginate(25)->withQueryString();
        $statuses = OfficeLoan::statuses();

        return view('rti.index', compact('loans', 'offices', 'statuses'));
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {

        $offices = Office::where('active', 1)->orderBy('name')->get();
        $staff   = User::orderBy('first_name')->get();
        $rtiRate = self::RTI_RATE;

        return view('rti.create', compact('offices', 'staff', 'rtiRate'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'office_id' => 'required|exists:offices,id',
            'staff_id'  => 'required|exists:users,id',
            'principal' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $principal = (float) $request->principal;
        $interest  = round($principal * self::RTI_RATE, 2);

        OfficeLoan::create([
            'office_id' => $request->office_id,
            'staff_id'  => $request->staff_id,
            'principal' => $principal,
            'interest'  => $interest,
            'status'    => OfficeLoan::STATUS_PENDING,
        ]);

        return redirect()->route('rti.loans.index')
            ->with('success', 'RTI loan created successfully and is pending approval.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show($id)
    {
        $loan = OfficeLoan::with([
            'office',
            'staff',
            'transactions.approver',
            'transactions.office',
        ])->findOrFail($id);

        // Build running balance for the history table
        $runningBalance = $loan->total_payable;
        $history = $loan->transactions->map(function ($tx) use (&$runningBalance) {
            if ($tx->status === OfficeLoanTransaction::STATUS_APPROVED) {
                $runningBalance -= $tx->credit;
                $runningBalance += $tx->debit; // in case of adjustments
            }
            $tx->running_balance = max(0, $runningBalance);
            return $tx;
        });

        $statuses = OfficeLoan::statuses();

        return view('rti.show', compact('loan', 'history', 'statuses'));
    }

    // -------------------------------------------------------------------------
    // Approve loan
    // -------------------------------------------------------------------------

    public function approve(Request $request, $id)
    {


        $loan = OfficeLoan::findOrFail($id);

        if ($loan->status !== OfficeLoan::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending loans can be approved.');
        }

        $loan->status      = OfficeLoan::STATUS_APPROVED;
        $loan->approved_at = Carbon::now();
        $loan->save();

        return redirect()->route('rti.loans.show', $id)
            ->with('success', 'Loan approved successfully.');
    }

    // -------------------------------------------------------------------------
    // Decline loan
    // -------------------------------------------------------------------------

    public function decline(Request $request, $id)
    {


        $loan = OfficeLoan::findOrFail($id);

        if (!in_array($loan->status, [OfficeLoan::STATUS_PENDING, OfficeLoan::STATUS_APPROVED])) {
            return redirect()->back()->with('error', 'This loan cannot be declined in its current status.');
        }

        $loan->status = OfficeLoan::STATUS_DECLINED;
        $loan->save();

        return redirect()->route('rti.loans.show', $id)
            ->with('success', 'Loan declined.');
    }

    // -------------------------------------------------------------------------
    // Disburse loan — creates the debit transaction
    // -------------------------------------------------------------------------

    public function disburse(Request $request, $id)
    {


        $loan = OfficeLoan::findOrFail($id);

        if ($loan->status !== OfficeLoan::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Only approved loans can be disbursed.');
        }

        DB::transaction(function () use ($loan) {
            $loan->status       = OfficeLoan::STATUS_DISBURSED;
            $loan->disbursed_at = Carbon::now();
            $loan->save();

            // Create the disbursement transaction record
            OfficeLoanTransaction::create([
                'loan_id'     => $loan->id,
                'office_id'   => $loan->office_id,
                'transaction_type'   => OfficeLoanTransaction::TYPE_DISBURSEMENT,
                'debit'       => $loan->principal,
                'credit'      => 0,
                'approved_by' => Sentinel::getUser()->id,
                'status'      => OfficeLoanTransaction::STATUS_APPROVED,
                'notes'       => 'RTI loan disbursement',
                'approved_at' => Carbon::now(),
            ]);
            OfficeLoanTransaction::create([
                'loan_id'     => $loan->id,
                'office_id'   => $loan->office_id,
                'transaction_type'   => OfficeLoanTransaction::TYPE_INTEREST_INITIAL,
                'debit'       => $loan->interest,
                'credit'      => 0,
                'approved_by' => Sentinel::getUser()->id,
                'status'      => OfficeLoanTransaction::STATUS_APPROVED,
                'notes'       => 'RTI loan initial interest',
                'approved_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('rti.loans.show', $loan->id)
            ->with('success', 'Loan disbursed successfully. Disbursement transaction recorded.');
    }
}

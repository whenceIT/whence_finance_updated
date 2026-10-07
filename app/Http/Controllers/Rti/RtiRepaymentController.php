<?php

namespace App\Http\Controllers\Rti;

use App\Http\Controllers\Controller;
use App\Models\OfficeLoan;
use App\Models\OfficeLoanTransaction;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RtiRepaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // -------------------------------------------------------------------------
    // Show repayment form for a specific loan
    // -------------------------------------------------------------------------

    public function create($loanId)
    {
        if (!Sentinel::hasAccess('rti.repayment')) {
            return redirect()->back()->with('error', 'You are not authorised to record repayments.');
        }

        $loan = OfficeLoan::with('office')->findOrFail($loanId);

        if (!in_array($loan->status, [
            OfficeLoan::STATUS_DISBURSED,
            OfficeLoan::STATUS_PARTIALLY_PAID,
        ])) {
            return redirect()->route('rti.loans.show', $loanId)
                ->with('error', 'Repayments can only be recorded for disbursed or partially paid loans.');
        }

        return view('rti.repayment.create', compact('loan'));
    }

    // -------------------------------------------------------------------------
    // Store a repayment (creates a PENDING transaction)
    // -------------------------------------------------------------------------

    public function store(Request $request, $loanId)
    {
        if (!Sentinel::hasAccess('rti.repayment')) {
            return redirect()->back()->with('error', 'You are not authorised to record repayments.');
        }

        $loan = OfficeLoan::findOrFail($loanId);

        if (!in_array($loan->status, [
            OfficeLoan::STATUS_DISBURSED,
            OfficeLoan::STATUS_PARTIALLY_PAID,
        ])) {
            return redirect()->back()->with('error', 'Repayments can only be recorded for disbursed or partially paid loans.');
        }

        $outstanding = $loan->outstanding_balance;

        $validator = Validator::make($request->all(), [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($outstanding) {
                    if ((float) $value > $outstanding) {
                        $fail('Repayment amount (K' . number_format($value, 2) . ') exceeds the outstanding balance (K' . number_format($outstanding, 2) . ').');
                    }
                },
            ],
            'notes'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        OfficeLoanTransaction::create([
            'loan_id'   => $loan->id,
            'office_id' => $loan->office_id,
            'debit'     => 0,
            'credit'    => (float) $request->amount,
            'status'    => OfficeLoanTransaction::STATUS_PENDING,
            'notes'     => $request->notes,
        ]);

        return redirect()->route('rti.loans.show', $loanId)
            ->with('success', 'Repayment of K' . number_format($request->amount, 2) . ' recorded and is pending approval.');
    }

    // -------------------------------------------------------------------------
    // Pending repayments list (approval queue)
    // -------------------------------------------------------------------------

    public function pendingApprovals(Request $request)
    {
        if (!Sentinel::hasAccess('rti.approve_repayment')) {
            return redirect()->back()->with('error', 'You are not authorised to approve repayments.');
        }

        $transactions = OfficeLoanTransaction::with(['loan.office', 'loan.staff', 'office'])
            ->pending()
            ->repayments()
            ->latest()
            ->paginate(25);

        return view('rti.repayment.approvals', compact('transactions'));
    }

    // -------------------------------------------------------------------------
    // Approve a repayment transaction
    // -------------------------------------------------------------------------

    public function approve(Request $request, $transactionId)
    {
        if (!Sentinel::hasAccess('rti.approve_repayment')) {
            return redirect()->back()->with('error', 'You are not authorised to approve repayments.');
        }

        $transaction = OfficeLoanTransaction::with('loan')->findOrFail($transactionId);

        if ($transaction->status !== OfficeLoanTransaction::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending transactions can be approved.');
        }

        // Validate repayment will not exceed outstanding balance at time of approval
        $loan        = $transaction->loan;
        $outstanding = $loan->outstanding_balance;

        if ($transaction->credit > $outstanding) {
            return redirect()->back()->with(
                'error',
                'Cannot approve: repayment amount (K' . number_format($transaction->credit, 2) .
                ') exceeds current outstanding balance (K' . number_format($outstanding, 2) . ').'
            );
        }

        DB::transaction(function () use ($transaction, $loan) {
            $transaction->status      = OfficeLoanTransaction::STATUS_APPROVED;
            $transaction->approved_by = Sentinel::getUser()->id;
            $transaction->approved_at = Carbon::now();
            $transaction->save();

            // Recalculate the loan status based on new balance
            $loan->recalculateStatus();
        });

        return redirect()->back()
            ->with('success', 'Repayment approved successfully.');
    }

    // -------------------------------------------------------------------------
    // Decline a repayment transaction
    // -------------------------------------------------------------------------

    public function decline(Request $request, $transactionId)
    {
        if (!Sentinel::hasAccess('rti.approve_repayment')) {
            return redirect()->back()->with('error', 'You are not authorised to decline repayments.');
        }

        $transaction = OfficeLoanTransaction::findOrFail($transactionId);

        if ($transaction->status !== OfficeLoanTransaction::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending transactions can be declined.');
        }

        $transaction->status      = OfficeLoanTransaction::STATUS_DECLINED;
        $transaction->approved_by = Sentinel::getUser()->id;
        $transaction->approved_at = Carbon::now();
        $transaction->save();

        return redirect()->back()
            ->with('success', 'Repayment declined.');
    }
}

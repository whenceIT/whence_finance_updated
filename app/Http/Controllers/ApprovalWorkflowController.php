<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\DepositType;
use App\Models\BankDepositLog;
use App\Models\SetupDebtCost;
use App\Models\SetupDebtTransaction;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Expense;
use Laracasts\Flash\Flash;
use App\Services\LockService;
use Illuminate\Http\File;

class ApprovalWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function depositApprovals(Request $request)
    {
        $deposits = Deposit::withoutGlobalScope('approved')
            ->with(['depositTypeInfo', 'bankDepositLog.user', 'office'])
            ->whereNull('status')
            ->get();

            
        $deposits = new \Illuminate\Pagination\LengthAwarePaginator(
            $deposits,
            $deposits->count(),
            15,
            1,
            ['path' => $request->url()]
        );

        $depositTypes = DepositType::orderBy('sort_order')->get();

        $setupDebtDeposits = SetupDebtTransaction::with(['office', 'setupDebtCost'])
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('approvals.deposit_approvals', compact('deposits', 'depositTypes', 'setupDebtDeposits'));
    }

    public function approveDecline($id, $status)
    {
        if ($status == 1) {
            $deposit = Deposit::withoutGlobalScope('approved')->find($id);
            Deposit::withoutGlobalScope('approved')->where('id', $id)->update(['status' => 1]);
            
            if ($deposit) {
                // $lockService = new LockService();
                // $lockService->unblock_deposits($deposit->office);

                $blockage = \App\Models\Blockage::where('office_id', $deposit->office)->first();
                if ($blockage) {
                    $blockage->time_to_unlock = now()->addHours(5);
                    $blockage->save();
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Approved successfully.']);
        } else {
            BankDepositLog::where('deposit_id', $id)->delete();
            Deposit::withoutGlobalScope('approved')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Deposit deleted successfully.']);
        }
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids');
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No deposits selected.']);
        }

        $deposits = Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->get();
        $officeIds = $deposits->pluck('office')->unique()->filter()->values();
        
        Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->update(['status' => 1]);

        $lockService = new LockService();
        foreach ($officeIds as $officeId) {
            $lockService->unblock_deposits($officeId);

            $blockage = \App\Models\Blockage::where('office_id', $officeId)->first();
            if ($blockage) {
                $blockage->time_to_unlock = now()->addHours(5);
                $blockage->save();
            }
        }
        
        return response()->json(['success' => true, 'message' => count($ids) . ' deposit(s) approved successfully.']);
    }

    public function approveAll(Request $request)
    {
        $query = Deposit::withoutGlobalScope('approved')
            ->select(['d.*'])
            ->from('deposits AS d');

        if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
            $query->where('d.deposit_type', $request->deposit_type);
        }

        $officeIds = collect();
        if ($request->filled('office_id') && $request->office_id !== 'all') {
            $query->where('d.office', $request->office_id);
            $officeIds->push($request->office_id);
        } else {
            $officeIds = Deposit::withoutGlobalScope('approved')
                ->where(function ($q) use ($request) {
                    if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
                        $q->where('deposit_type', $request->deposit_type);
                    }
                })
                ->whereNull('status')
                ->pluck('office')
                ->unique()
                ->filter()
                ->values();
        }

        $count = $query->count();
        $query->update(['status' => 1]);

        $lockService = new LockService();
        foreach ($officeIds as $officeId) {
            $lockService->unblock_deposits($officeId);

            $blockage = \App\Models\Blockage::where('office_id', $officeId)->first();
            if ($blockage) {
                $blockage->time_to_unlock = now()->addHours(5);
                $blockage->save();
            }
        }

        return response()->json(['success' => true, 'message' => $count . ' deposit(s) approved successfully.']);
    }

    public function bulkDecline(Request $request)
     {
         $ids = $request->input('ids');
         
         if (empty($ids)) {
             return response()->json(['success' => false, 'message' => 'No deposits selected.']);
         }
         
         BankDepositLog::whereIn('deposit_id', $ids)->delete();
         Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->whereNull('status')->delete();
         return response()->json(['success' => true, 'message' => count($ids) . ' deposit(s) declined successfully.']);
     }

    public function declineAll(Request $request)
     {
         $query = Deposit::withoutGlobalScope('approved')
             ->select(['d.*'])
             ->from('deposits AS d')
             ->whereNull('status');

         if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
             $query->where('d.deposit_type', $request->deposit_type);
         }

         if ($request->filled('office_id') && $request->office_id !== 'all') {
             $query->where('d.office', $request->office_id);
         }

         $ids = (clone $query)->pluck('d.id');
         $count = $ids->count();
         BankDepositLog::whereIn('deposit_id', $ids)->delete();
         $query->delete();
         
         return response()->json(['success' => true, 'message' => $count . ' deposit(s) declined successfully.']);
     }

    /**
     * Administration queue for branch expenses awaiting first-stage approval.
     * Role ID 1 is the Administration role used by the existing LMS sidebar.
     */
    public function expenseApprovals()
    {
        $user = Sentinel::getUser();
        $roleId = optional($user->roles()->first())->id;

        if ($roleId != 1) {
            abort(403, 'Only Administration can approve submitted branch expenses.');
        }

        $expenses = Expense::with([
            'office',
            'type',
            'created_by'
        ])
        ->where('status', 'pending_admin')
        ->orderBy('created_at', 'asc')
        ->paginate(30);

        return view('approvals.expense_approvals', compact('expenses'));
    }

    /**
     * Administration approval moves an expense to the Risk Manager queue.
     * It deliberately does not initiate a payment.
     */
    public function approveExpense(Request $request, $id)
    {
        $user = Sentinel::getUser();
        $roleId = optional($user->roles()->first())->id;

        if ($roleId != 1) {
            abort(403, 'Only Administration can approve submitted branch expenses.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:5000',
        ]);

        $expense = Expense::where('status', 'pending_admin')->findOrFail($id);

        // Segregation of duties: a user cannot approve their own submission.
        if ((int) $expense->created_by_id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot approve an expense that you submitted.'
            ], 403);
        }

        $expense->status = 'pending_risk';
        $expense->admin_approved_by_id = $user->id;
        $expense->admin_approved_at = now();
        $expense->admin_approval_notes = $request->input('approval_notes');
        $expense->save();

        try {
            \App\Models\Notifix::notifyDailyReminderToRiskManager(
                'Administration approved expense with id: ' . $expense->id,
                '. Awaiting Risk Manager approval'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify Risk Manager about approved expense #' . $expense->id . ': ' . $e->getMessage());
        }

        \App\Helpers\GeneralHelper::audit_trail('Administration Approved', 'Expenses', $expense->id);

        return response()->json([
            'success' => true,
            'message' => 'Expense approved and forwarded to the Risk Manager. No payment has been initiated.'
        ]);
    }

    /**
     * Administration decline. A reason is mandatory and the expense is not deleted.
     */
    public function declineExpense(Request $request, $id)
    {
        $user = Sentinel::getUser();
        $roleId = optional($user->roles()->first())->id;

        if ($roleId != 1) {
            abort(403, 'Only Administration can decline submitted branch expenses.');
        }

        $request->validate([
            'approval_notes' => 'required|string|max:5000',
        ], [
            'approval_notes.required' => 'Please provide a reason for declining this expense.',
        ]);

        $expense = Expense::where('status', 'pending_admin')->findOrFail($id);

        if ((int) $expense->created_by_id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot decline an expense that you submitted.'
            ], 403);
        }

        $expense->status = 'declined';
        $expense->admin_approved_by_id = $user->id;
        $expense->admin_approved_at = now();
        $expense->admin_approval_notes = $request->input('approval_notes');
        $expense->save();

        \App\Helpers\GeneralHelper::audit_trail('Administration Declined', 'Expenses', $expense->id);

        return response()->json([
            'success' => true,
            'message' => 'Expense declined. The expense record has been retained.'
        ]);
    }

    public function approveDeclineSetupDebt($id, $status)
{
    $setupDebtTransaction = SetupDebtTransaction::findOrFail($id);
    
    if ($status == 1) {
        $setupDebtTransaction->status = 1;
        $setupDebtTransaction->save();

        //if there is a record in the Blockage for this office update the time_to_unlock to 5 hours from now
        $blockage = \App\Models\Blockage::where('office_id', $setupDebtTransaction->office_id)->first();
        if ($blockage) {
            $blockage->time_to_unlock = now()->addHours(5);
            $blockage->save();
        }
        
        return response()->json(['success' => true, 'message' => 'Setup debt transaction approved successfully.']);
        } else {
            $setupDebtTransaction->delete();
            return response()->json(['success' => true, 'message' => 'Setup debt transaction declined and deleted.']);
        }
    }

    public function bulkApproveSetupDebt(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No setup debt transactions selected.']);
        }

        $transactions = SetupDebtTransaction::whereIn('id', $ids)->get();
        SetupDebtTransaction::whereIn('id', $ids)->update(['status' => 1]);

        // Update time_to_unlock on blockages for approved transactions' offices
        $officeIds = $transactions->pluck('office_id')->unique()->filter();
        foreach ($officeIds as $officeId) {
            $blockage = \App\Models\Blockage::where('office_id', $officeId)->first();
            if ($blockage) {
                $blockage->time_to_unlock = now()->addHours(5);
                $blockage->save();
            }
        }

        return response()->json(['success' => true, 'message' => count($ids) . ' setup debt transaction(s) approved successfully.']);
    }

    public function bulkDeclineSetupDebt(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No setup debt transactions selected.']);
        }

        SetupDebtTransaction::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' setup debt transaction(s) declined successfully.']);
    }

    /*
ADD THESE IMPORTS TO THE TOP OF ApprovalWorkflowController.php IF THEY ARE NOT
ALREADY PRESENT:

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

ADD THE FOLLOWING METHODS INSIDE ApprovalWorkflowController CLASS.

IMPORTANT ROLE NOTE:
This uses the existing Sentinel role slug "role.risk", which is already referenced
in the LMS sidebar. If your actual Risk Manager role slug is different, change it
in riskUserIsAuthorized().
*/

private function riskUserIsAuthorized()
{
    $user = Sentinel::getUser();

    return $user && $user->roles()->where('slug', 'role.risk')->exists();
}

/**
 * Risk Manager queue: expenses already approved by Administration.
 */
public function riskExpenseApprovals()
{
    // if (!$this->riskUserIsAuthorized()) {
    //     abort(403, 'Only the Risk Manager can review these expenses.');
    // }

    $expenses = Expense::with(['office', 'type', 'created_by'])
        ->where('status', 'pending_risk')
        ->orderBy('created_at', 'asc')
        ->paginate(30);

    return view('approvals.risk_expense_approvals', compact('expenses'));
}

/**
 * Risk Manager approves the expense and starts the WithinHere transfer.
 *
 * The database is marked "processing" BEFORE calling WithinHere. The external
 * API call is made OUTSIDE the DB transaction. This prevents two simultaneous
 * clicks from starting two payments.
 *
 * A response with status "pending" means the transfer was accepted for
 * processing; it does NOT mean the expense has been paid.
 */
public function approveRiskExpense(Request $request, $id)
{
    // if (!$this->riskUserIsAuthorized()) {
    //     abort(403, 'Only the Risk Manager can approve these expenses.');
    // }

    $request->validate([
        'approval_notes' => 'nullable|string|max:5000',
    ]);

    $user = Sentinel::getUser();

    // Lock and reserve this expense so concurrent requests cannot initiate
    // more than one transfer.
    $expense = DB::transaction(function () use ($id, $user, $request) {
        $record = Expense::where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($record->status !== 'pending_risk') {
            abort(409, 'This expense is no longer awaiting Risk Manager approval.');
        }

        if (($record->payment_status ?? 'not_started') !== 'not_started') {
            abort(409, 'A payment attempt already exists for this expense. Please reconcile it before taking further action.');
        }

        // Segregation of duties.
        if ((int) $record->created_by_id === (int) $user->id) {
            abort(403, 'You cannot approve an expense that you submitted.');
        }

        $record->status = 'approved';
        $record->risk_approved_by_id = $user->id;
        $record->risk_approved_at = now();
        $record->risk_approval_notes = $request->input('approval_notes');
        $record->payment_status = 'processing';
        $record->save();

        return $record;
    });

    // Build the payout type from the payment method saved at submission.
    $paymentType = $expense->deposit_method;

    if (!in_array($paymentType, ['mobile_money', 'bank'], true)) {
        $this->markExpensePaymentFailure(
            $expense->id,
            'Unsupported or missing payment method: ' . (string) $paymentType
        );

        return response()->json([
            'success' => false,
            'message' => 'The expense was approved, but its payment method is invalid. Payment was not sent.'
        ], 422);
    }

    if (empty($expense->withinhere_user_id)) {
        $this->markExpensePaymentFailure($expense->id, 'Missing WithinHere user ID.');

        return response()->json([
            'success' => false,
            'message' => 'The expense was approved, but the WithinHere wallet user ID is missing. Payment was not sent.'
        ], 422);
    }

    if ($paymentType === 'mobile_money' && (
        empty($expense->payment_phone) || empty($expense->payment_operator)
    )) {
        $this->markExpensePaymentFailure($expense->id, 'Missing mobile money phone number or operator.');

        return response()->json([
            'success' => false,
            'message' => 'The expense was approved, but the mobile money phone number or operator is missing. Payment was not sent.'
        ], 422);
    }

    if ($paymentType === 'bank' && (
        empty($expense->payment_bank_id) || empty($expense->payment_account_number)
    )) {
        $this->markExpensePaymentFailure($expense->id, 'Missing bank or account number.');

        return response()->json([
            'success' => false,
            'message' => 'The expense was approved, but the bank details are incomplete. Payment was not sent.'
        ], 422);
    }

    $payoutType = $paymentType === 'mobile_money'
        ? 'withinhere_to_mno'
        : 'withinhere_to_bank';

    /*
     * Calculate the current WithinHere deduction on the server.
     * The submission form intentionally does not initiate a payment or rely
     * on the browser-provided totalDeducted value.
     */
    try {
        $chargesResponse = Http::timeout(45)->post(
            'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',
            [
                'amount' => $expense->amount,
                'payout_type' => $payoutType,
            ]
        );

        $chargesData = $chargesResponse->json();
        $totalDeducted = data_get($chargesData, 'data.totalDeducted');

        if (!$chargesResponse->successful() || !is_numeric($totalDeducted)) {
            $this->markExpensePaymentFailure(
                $expense->id,
                'Unable to calculate WithinHere charges. HTTP ' .
                $chargesResponse->status() . '. Response: ' . $chargesResponse->body()
            );

            return response()->json([
                'success' => false,
                'message' => 'The expense was approved, but WithinHere charges could not be calculated. No transfer was sent.'
            ], 502);
        }
    } catch (\Throwable $e) {
        $this->markExpensePaymentFailure(
            $expense->id,
            'WithinHere charges request failed: ' . $e->getMessage()
        );

        \Log::error('Expense #' . $expense->id . ' charges request failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'The expense was approved, but the payment charges service could not be reached. No transfer was sent.'
        ], 502);
    }

    // Use the exact payout endpoints and payload fields used by the original
    // ExpenseController integration.
    if ($paymentType === 'mobile_money') {
        $url = 'https://withinheremobileapi.com/api/v1/transfer/withdraw-to/mobile';

        $payload = [
            'amount' => $expense->amount,
            'phone' => $expense->payment_phone,
            'reason' => 'Branch expense #' . $expense->id . ': ' . $expense->name,
            'user_id' => $expense->withinhere_user_id,
            'operator' => $expense->payment_operator,
            'payout_type' => 'withinhere_to_mno',
            'totalDeducted' => $totalDeducted,
        ];
    } else {
        $url = 'https://withinheremobileapi.com/api/v1/transfer/transfer-to/bank';

        $payload = [
            'amount' => $expense->amount,
            'user_id' => $expense->withinhere_user_id,
            'bankId' => $expense->payment_bank_id,
            'accountNumber' => $expense->payment_account_number,
            'reason' => 'Branch expense #' . $expense->id . ': ' . $expense->name,
            'payout_type' => 'withinhere_to_bank',
            'totalDeducted' => $totalDeducted,
        ];
    }

    try {
        $paymentResponse = Http::timeout(60)->post($url, $payload);
        $paymentData = $paymentResponse->json();

        // Only "pending" is treated as an accepted transfer request.
        // Do not mark the expense as paid here.
        if (
            $paymentResponse->successful() &&
            data_get($paymentData, 'status') === 'pending'
        ) {
            $reference = data_get($paymentData, 'reference')
                ?? data_get($paymentData, 'transaction_id')
                ?? data_get($paymentData, 'id');

            Expense::where('id', $expense->id)->update([
                'payment_status' => 'pending',
                'payment_reference' => $reference,
                'payment_response' => json_encode($paymentData),
                'total_deducted' => $totalDeducted,
            ]);

            \App\Helpers\GeneralHelper::audit_trail(
                'Risk Approved - Payment Pending',
                'Expenses',
                $expense->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Risk approval recorded. WithinHere accepted the transfer request; payment is still pending confirmation.'
            ]);
        }

        /*
         * A non-pending response is recorded as unknown rather than blindly
         * retried. Reconcile the gateway response before attempting another
         * transfer, because the provider may have accepted a request despite
         * an unexpected response.
         */
        Expense::where('id', $expense->id)->update([
            'payment_status' => 'unknown',
            'payment_response' => json_encode([
                'http_status' => $paymentResponse->status(),
                'response' => $paymentData ?? $paymentResponse->body(),
            ]),
            'total_deducted' => $totalDeducted,
        ]);

        \Log::warning('Expense #' . $expense->id . ' WithinHere response requires reconciliation.', [
            'http_status' => $paymentResponse->status(),
            'response' => $paymentData ?? $paymentResponse->body(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Risk approval was recorded, but the payment response needs reconciliation. Do not retry until the WithinHere transaction has been checked.'
        ], 502);

    } catch (\Throwable $e) {
        /*
         * A timeout may happen after the provider has received the request.
         * Mark it unknown and do not send it again automatically.
         */
        Expense::where('id', $expense->id)->update([
            'payment_status' => 'unknown',
            'payment_response' => json_encode([
                'error' => $e->getMessage(),
                'message' => 'Transfer request outcome is unknown; reconcile with WithinHere before retrying.',
            ]),
            'total_deducted' => $totalDeducted,
        ]);

        \Log::error('Expense #' . $expense->id . ' payment outcome unknown: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Risk approval was recorded, but the payment outcome is unknown due to a connection error. Check WithinHere before attempting another transfer.'
        ], 502);
    }
}

/**
 * Risk Manager declines an expense. A reason is mandatory.
 */
public function declineRiskExpense(Request $request, $id)
{


    $request->validate([
        'approval_notes' => 'required|string|max:5000',
    ], [
        'approval_notes.required' => 'Please provide a reason for declining this expense.',
    ]);

    $user = Sentinel::getUser();

    $expense = DB::transaction(function () use ($id, $user, $request) {
        $record = Expense::where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($record->status !== 'pending_risk') {
            abort(409, 'This expense is no longer awaiting Risk Manager approval.');
        }

        if (($record->payment_status ?? 'not_started') !== 'not_started') {
            abort(409, 'A payment attempt already exists. This expense cannot be declined from this queue.');
        }

        if ((int) $record->created_by_id === (int) $user->id) {
            abort(403, 'You cannot decline an expense that you submitted.');
        }

        $record->status = 'declined';
        $record->risk_approved_by_id = $user->id;
        $record->risk_approved_at = now();
        $record->risk_approval_notes = $request->input('approval_notes');
        $record->save();

        return $record;
    });

    \App\Helpers\GeneralHelper::audit_trail(
        'Risk Declined',
        'Expenses',
        $expense->id
    );

    return response()->json([
        'success' => true,
        'message' => 'Expense declined. The expense record has been retained.'
    ]);
}

/**
 * Store a safe payment failure only when no transfer request was sent.
 */
private function markExpensePaymentFailure($expenseId, $message)
{
    Expense::where('id', $expenseId)->update([
        'payment_status' => 'failed',
        'payment_response' => json_encode(['message' => $message]),
    ]);

    \Log::error('Expense #' . $expenseId . ' payment setup failed: ' . $message);
}


}
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
                $lockService = new LockService();
                $lockService->unblock_deposits($deposit->office);
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

        $officeIds = Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->pluck('office')->unique()->filter()->values();
        
        Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->update(['status' => 1]);

        $lockService = new LockService();
        foreach ($officeIds as $officeId) {
            $lockService->unblock_deposits($officeId);
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

    public function expenseApprovals()
    {

    $expenses = Expense::with([
        'office',
        'type',
        'created_by'
    ])
    ->where('status', 'pending')
    ->orderBy('id', 'desc')
    ->paginate(50);

        return view('approvals.expense_approvals', compact('expenses'));
    }

    public function approveExpense($id, $status)
    {
    

        $expense = Expense::findOrFail($id);

        $expense->status = $status;
        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully'
        ]);
    }   

    public function declineExpense($id,$status)
    {

        $expense = Expense::findOrFail($id);

        $expense->status = $status;
        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully'
        ]);

    }


public function bulkApproveExpenses(Request $request)
{
   

    Expense::whereIn('id', $request->ids)
        ->update([
            'status' => 'approved'
        ]);

    return response()->json([
        'success' => true,
        'message' => count($request->ids).' expenses approved successfully.'
    ]);
}


public function approveAllExpenses(Request $request)
{
   

    $count = Expense::where(function ($query) {
            $query->whereNull('status')
                  ->orWhere('status', 'pending');
        })
        ->update([
            'status' => 'approved'
        ]);

    return response()->json([
        'success' => true,
        'message' => $count.' expenses approved successfully.'
    ]);
}

public function approveDeclineSetupDebt($id, $status)
{
    $setupDebtTransaction = SetupDebtTransaction::findOrFail($id);
    
    if ($status == 1) {
        $setupDebtTransaction->status = 1;
        $setupDebtTransaction->save();
        return response()->json(['success' => true, 'message' => 'Setup debt transaction approved successfully.']);
    } else {
        $setupDebtTransaction->delete();
        return response()->json(['success' => true, 'message' => 'Setup debt transaction declined and deleted.']);
    }
}

}
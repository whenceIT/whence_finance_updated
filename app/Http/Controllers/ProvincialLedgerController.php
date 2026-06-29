<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvincialTransaction;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class ProvincialLedgerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::query();
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            $provinces = Province::all();
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
            $provinces = $province_id ? Province::where('id', $province_id)->get() : Province::all();
        }
        
        $query->where('status', 'approved');
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpenses = (clone $query)->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;
        
        $recentTransactions = (clone $query)->orderBy('created_at', 'desc')
            ->limit(10)
            ->with('province')
            ->get();
            
        $incomeByProvince = (clone $query)->where('type', 'income')
            ->select(DB::raw('province_id, SUM(amount) as total'))
            ->groupBy('province_id')
            ->with('province')
            ->get();
            
        $expenseByProvince = (clone $query)->where('type', 'expense')
            ->select(DB::raw('province_id, SUM(amount) as total'))
            ->groupBy('province_id')
            ->with('province')
            ->get();

        return view('provincial-ledger.dashboard', compact(
            'provinces',
            'totalIncome',
            'totalExpenses',
            'netBalance',
            'recentTransactions',
            'incomeByProvince',
            'expenseByProvince',
            'isAdmin',
            'selectedProvinceId'
        ));
    }

    public function income(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::where('type', 'income');
        $query->where('status', 'approved');
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            $provinces = Province::all();
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
            $provinces = $province_id ? Province::where('id', $province_id)->get() : Province::all();
        }
        
        $income = $query->with('province')->orderBy('created_at', 'desc')->get();
        $total = $income->sum('amount');

        return view('provincial-ledger.income', compact('income', 'total', 'provinces', 'isAdmin', 'selectedProvinceId'));
    }

    public function expenses(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::where('type', 'expense');
        $query->where('status', 'approved');
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            $provinces = Province::all();
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
            $provinces = $province_id ? Province::where('id', $province_id)->get() : Province::all();
        }
        
        $expenses = $query->with('province')->orderBy('created_at', 'desc')->get();
        $total = $expenses->sum('amount');

        return view('provincial-ledger.expenses', compact('expenses', 'total', 'provinces', 'isAdmin', 'selectedProvinceId'));
    }

    public function balance()
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::query();
        if ($province_id) {
            $query->where('province_id', $province_id);
        }
        
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpenses = (clone $query)->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;

        $balanceByProvince = (clone $query)->select(
            'province_id',
            DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
            DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses")
        )
        ->groupBy('province_id')
        ->with('province')
        ->get();

        return view('provincial-ledger.balance', compact('netBalance', 'balanceByProvince'));
    }

    public function pendingTransactions(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::whereNull('status');
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            $provinces = Province::all();
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
            $provinces = $province_id ? Province::where('id', $province_id)->get() : Province::all();
        }

        $transactions = $query->with('province', 'creator')->orderBy('created_at', 'desc')->get();

        return view('provincial-ledger.pending', compact('transactions', 'provinces', 'isAdmin', 'selectedProvinceId'));
    }

    public function approvedTransactions(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::where('status', 'approved');
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            $provinces = Province::all();
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
            $provinces = $province_id ? Province::where('id', $province_id)->get() : Province::all();
        }

        $transactions = $query->with('province', 'approver')->orderBy('created_at', 'desc')->get();

        return view('provincial-ledger.approved', compact('transactions', 'provinces', 'isAdmin', 'selectedProvinceId'));
    }

public function approveTransaction(Request $request, $id)
    {
        $transaction = ProvincialTransaction::find($id);
        if (!$transaction) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaction not found']);
            }
            return redirect()->back()->with('error', 'Transaction not found');
        }

        $transaction->status = 'approved';
        $transaction->approved_by = Sentinel::getUser()->id;
        $transaction->approved_at = now();
        $transaction->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction approved']);
        }
        return redirect()->route('provincial-transactions.pending')->with('success', 'Transaction approved');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No transactions selected']);
            }
            return redirect()->back()->with('error', 'No transactions selected');
        }

        $user = Sentinel::getUser();
        ProvincialTransaction::whereIn('id', $ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Transactions approved']);
        }
        return redirect()->route('provincial-transactions.pending')->with('success', 'Transactions approved');
    }

    public function approveAll(Request $request)
    {
        $user = Sentinel::getUser();
        $count = ProvincialTransaction::where('status', 'pending')->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => "{$count} transactions approved"]);
    }

    public function bulkDecline(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No transactions selected']);
        }

        ProvincialTransaction::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Transactions declined and deleted']);
    }

    public function declineAll(Request $request)
    {
        $user = Sentinel::getUser();
        $province_id = $user && $user->office ? $user->office->province_id : null;
        $query = ProvincialTransaction::where('status', 'pending');
        $isAdmin = $user && $user->role && $user->role->role_id == 1;
        $selectedProvinceId = $isAdmin ? $request->query('province_id') : null;

        if ($isAdmin) {
            if ($selectedProvinceId) {
                $query->where('province_id', $selectedProvinceId);
            }
        } else {
            if ($province_id) {
                $query->where('province_id', $province_id);
            }
        }

        $count = $query->count();
        $query->delete();

        return response()->json(['success' => true, 'message' => $count . ' transaction(s) declined successfully.']);
    }

    public function declineTransaction($id)
    {
        $transaction = ProvincialTransaction::find($id);
        if (!$transaction) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaction not found']);
            }
            return redirect()->back()->with('error', 'Transaction not found');
        }

        $transaction->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction declined and deleted']);
        }
        return redirect()->route('provincial-transactions.pending')->with('success', 'Transaction declined and deleted');
    }
}
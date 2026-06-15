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
}
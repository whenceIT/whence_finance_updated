<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvincialTransaction;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

class ProvincialLedgerController extends Controller
{
    public function dashboard()
    {
        $userInfo = \App\Helpers\GeneralHelper::get_user_info();
        $user = $userInfo->user;
        $office = $userInfo->office;
        
        $provinceId = $office && isset($office->province_id) ? $office->province_id : null;
        
        $query = ProvincialTransaction::query();
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }
        
        $provinces = $provinceId ? Province::where('id', $provinceId)->get() : Province::all();
        
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
            'expenseByProvince'
        ));
    }

    public function income()
    {
        $userInfo = \App\Helpers\GeneralHelper::get_user_info();
        $office = $userInfo->office;
        
        $provinceId = $office && isset($office->province_id) ? $office->province_id : null;
        
        $query = ProvincialTransaction::where('type', 'income');
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }
        
        $income = $query->with('province')->orderBy('created_at', 'desc')->get();
        $total = $income->sum('amount');
        
        $provinces = $provinceId ? Province::where('id', $provinceId)->get() : Province::all();

        return view('provincial-ledger.income', compact('income', 'total', 'provinces'));
    }

    public function expenses()
    {
        $userInfo = \App\Helpers\GeneralHelper::get_user_info();
        $office = $userInfo->office;
        
        $provinceId = $office && isset($office->province_id) ? $office->province_id : null;
        
        $query = ProvincialTransaction::where('type', 'expense');
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }
        
        $expenses = $query->with('province')->orderBy('created_at', 'desc')->get();
        $total = $expenses->sum('amount');
        
        $provinces = $provinceId ? Province::where('id', $provinceId)->get() : Province::all();

        return view('provincial-ledger.expenses', compact('expenses', 'total', 'provinces'));
    }

    public function balance()
    {
        $userInfo = \App\Helpers\GeneralHelper::get_user_info();
        $office = $userInfo->office;
        
        $provinceId = $office && isset($office->province_id) ? $office->province_id : null;
        
        $query = ProvincialTransaction::query();
        if ($provinceId) {
            $query->where('province_id', $provinceId);
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
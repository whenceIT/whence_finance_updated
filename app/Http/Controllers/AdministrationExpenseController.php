<?php

namespace App\Http\Controllers;

use App\Models\AdministrationExpense;
use App\Models\BankAccountExpense;
use App\Models\ExpenseCategory;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;

class AdministrationExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = AdministrationExpense::with(['category', 'user'])->orderBy('id', 'desc');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('expense_date', [$start_date, $end_date]);
        }

        $expenses = $query->get();
        $categories = ExpenseCategory::where('type', 'administration')->get();

        $totalDeposits = DB::table('deposits')->where('deposit_type', 1)->sum('amount');
        $totalExpenses = AdministrationExpense::sum('amount');
        $availableBalance = $totalDeposits - $totalExpenses;

        $bankExpenses = BankAccountExpense::with(['category', 'user', 'bankAccount'])->orderBy('id', 'desc')->get();

        return view('administration_expenses.index', compact('expenses', 'categories', 'start_date', 'end_date', 'totalDeposits', 'totalExpenses', 'availableBalance', 'bankExpenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('type', 'administration')->get();
        return view('administration_expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $totalDeposits = DB::table('deposits')->where('deposit_type', 1)->sum('amount');
        $totalExpenses = AdministrationExpense::sum('amount');
        $availableBalance = $totalDeposits - $totalExpenses;

        if ($request->amount > $availableBalance) {
            Flash::warning("Insufficient funds. Available balance: K" . number_format($availableBalance, 2));
            return redirect()->back()->withInput();
        }

        $expense = new AdministrationExpense();
        $expense->category_id = $request->category_id;
        $expense->reference_number = $request->reference_number;
        $expense->comments = $request->comments;
        $expense->amount = $request->amount;
        $expense->gl_account_code = $request->gl_account_code;
        $expense->expense_date = $request->expense_date;
        $expense->entered_by = Sentinel::getUser()->id;
        $expense->bank_charge_type = $request->bank_charge_type;
        $expense->save();

        Flash::success("Expense recorded successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function edit(AdministrationExpense $expense)
    {
        $categories = ExpenseCategory::where('type', 'administration')->get();
        return view('administration_expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, AdministrationExpense $expense)
    {
        $expense->category_id = $request->category_id;
        $expense->reference_number = $request->reference_number;
        $expense->comments = $request->comments;
        $expense->amount = $request->amount;
        $expense->gl_account_code = $request->gl_account_code;
        $expense->expense_date = $request->expense_date;
        $expense->entered_by = Sentinel::getUser()->id;
        $expense->bank_charge_type = $request->bank_charge_type;
        $expense->save();

        Flash::success("Expense updated successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function destroy(AdministrationExpense $expense)
    {
        $expense->delete();
        Flash::success("Expense deleted successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function getDashboard()
    {
        $totalDeposits = DB::table('deposits')->where('deposit_type', 1)->sum('amount');
        $totalExpenses = AdministrationExpense::sum('amount');
        $availableBalance = $totalDeposits - $totalExpenses;

        $expensesByCategory = AdministrationExpense::with('category')
            ->select(DB::raw('category_id, SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $monthlyExpenses = AdministrationExpense::select(
            DB::raw('YEAR(expense_date) as year'),
            DB::raw('MONTH(expense_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy(DB::raw('YEAR(expense_date)'))
            ->groupBy(DB::raw('MONTH(expense_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('administration_expenses.dashboard', compact(
            'totalDeposits',
            'totalExpenses',
            'availableBalance',
            'expensesByCategory',
            'monthlyExpenses'
        ));
    }
}
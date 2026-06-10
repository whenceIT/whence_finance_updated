<?php

namespace App\Http\Controllers;

use App\Models\BankAccountExpense;
use App\Models\ExpenseCategory;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;

class BankAccountExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = BankAccountExpense::with(['category', 'user', 'bankAccount'])->orderBy('id', 'desc');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('transaction_date', [$start_date, $end_date]);
        }

        $expenses = $query->get();
        $categories = ExpenseCategory::where('type', 'bank_account')->get();
        $bankAccounts = Office::pluck('name', 'id');

        return view('bank_account_expenses.index', compact('expenses', 'categories', 'bankAccounts', 'start_date', 'end_date'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('type', 'bank_account')->get();
        $bankAccounts = Office::pluck('name', 'id');
        return view('administration_expenses.bank_expenses.create', compact('categories', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $expense = new BankAccountExpense();
        $expense->bank_account_id = $request->bank_account_id;
        $expense->category_id = $request->category_id;
        $expense->reference_number = $request->reference_number;
        $expense->comments = $request->comments;
        $expense->amount = $request->amount;
        $expense->gl_account_code = $request->gl_account_code;
        $expense->transaction_date = $request->transaction_date;
        $expense->entered_by = Sentinel::getUser()->id;
        $expense->save();

        Flash::success("Bank account expense recorded successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function edit(BankAccountExpense $expense)
    {
        $categories = ExpenseCategory::where('type', 'bank_account')->get();
        $bankAccounts = Office::pluck('name', 'id');
        return view('administration_expenses.bank_expenses.edit', compact('expense', 'categories', 'bankAccounts'));
    }

    public function update(Request $request, BankAccountExpense $expense)
    {
        $expense->bank_account_id = $request->bank_account_id;
        $expense->category_id = $request->category_id;
        $expense->reference_number = $request->reference_number;
        $expense->comments = $request->comments;
        $expense->amount = $request->amount;
        $expense->gl_account_code = $request->gl_account_code;
        $expense->transaction_date = $request->transaction_date;
        $expense->entered_by = Sentinel::getUser()->id;
        $expense->save();

        Flash::success("Bank account expense updated successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function destroy(BankAccountExpense $expense)
    {
        $expense->delete();
        Flash::success("Bank account expense deleted successfully");
        return redirect()->route('administration_expenses.index');
    }

    public function getDashboard()
    {
        $expensesByCategory = BankAccountExpense::with('category')
            ->select(DB::raw('category_id, SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $expensesByAccount = BankAccountExpense::with('bankAccount')
            ->select('bank_account_id', DB::raw('SUM(amount) as total'))
            ->groupBy('bank_account_id')
            ->get();

        $monthlyExpenses = BankAccountExpense::select(
            DB::raw('YEAR(transaction_date) as year'),
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy(DB::raw('YEAR(transaction_date)'))
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('bank_account_expenses.dashboard', compact(
            'expensesByCategory',
            'expensesByAccount',
            'monthlyExpenses'
        ));
    }
}
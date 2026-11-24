<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use App\Models\Artworks\DailyIncome;
use App\Models\Artworks\DailyExpense;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyIncome::with(['branch', 'recordedBy'])
            ->orderBy('income_date', 'desc')
            ->orderBy('created_at', 'desc');
        
        $expenseQuery = DailyExpense::with(['branch', 'recordedBy'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
            $expenseQuery->where('branch_id', $request->branch_id);
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('income_date', '>=', $request->date_from);
            $expenseQuery->where('expense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('income_date', '<=', $request->date_to);
            $expenseQuery->where('expense_date', '<=', $request->date_to);
        }
        if ($request->has('category') && $request->category != '') {
            $query->where('income_category', $request->category);
        }
        if ($request->has('status') && $request->status != '') {
            $expenseQuery->where('status', $request->status);
        }

        $incomes = $query->paginate(20)->appends($request->query());
        $expenses = $expenseQuery->paginate(20)->appends($request->query());

        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        $incomeCategories = DailyIncome::getCategories();
        $expenseCategories = DailyExpense::getCategories();
        $statuses = ['Pending', 'Approved', 'Rejected'];

        $totalIncome = DailyIncome::when($request->date_from, fn($q) => $q->where('income_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('income_date', '<=', $request->date_to))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->sum('amount');

        $totalExpense = DailyExpense::when($request->date_from, fn($q) => $q->where('expense_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('expense_date', '<=', $request->date_to))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->where('status', 'Approved')
            ->sum('amount');

        return view('artworks.daily-reports.index', compact(
            'incomes', 'expenses', 'branches', 
            'incomeCategories', 'expenseCategories', 'statuses',
            'totalIncome', 'totalExpense'
        ));
    }

    public function incomeCreate()
    {
        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        $categories = DailyIncome::getCategories();
        $sources = DailyIncome::getSources();
        return view('artworks.daily-reports.income-create', compact('branches', 'categories', 'sources'));
    }

    public function incomeStore(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'nullable|exists:offices,id',
            'income_date' => 'required|date',
            'income_category' => 'required|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Cheque,Other',
            'received_from' => 'nullable|string|max:255',
        ]);

        $income = DailyIncome::create([
            'branch_id' => $request->branch_id,
            'income_date' => $request->income_date,
            'income_category' => $request->income_category,
            'income_source' => $request->income_source,
            'reference_number' => $request->reference_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'recorded_by_user_id' => Auth::id(),
            'payment_method' => $request->payment_method,
            'received_from' => $request->received_from,
            'metadata' => $request->metadata ?? null,
        ]);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Income recorded successfully. Reference: ' . $income->reference_number);
    }

    public function incomeEdit($id)
    {
        $income = DailyIncome::findOrFail($id);
        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        $categories = DailyIncome::getCategories();
        $sources = DailyIncome::getSources();
        return view('artworks.daily-reports.income-edit', compact('income', 'branches', 'categories', 'sources'));
    }

    public function incomeUpdate(Request $request, $id)
    {
        $income = DailyIncome::findOrFail($id);

        $this->validate($request, [
            'branch_id' => 'nullable|exists:offices,id',
            'income_date' => 'required|date',
            'income_category' => 'required|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Cheque,Other',
            'received_from' => 'nullable|string|max:255',
        ]);

        $income->update([
            'branch_id' => $request->branch_id,
            'income_date' => $request->income_date,
            'income_category' => $request->income_category,
            'income_source' => $request->income_source,
            'reference_number' => $request->reference_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
            'received_from' => $request->received_from,
            'metadata' => $request->metadata ?? $income->metadata,
        ]);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Income updated successfully.');
    }

    public function incomeDelete($id)
    {
        $income = DailyIncome::findOrFail($id);
        $income->delete();
        return redirect()->route('daily-reports.index')
            ->with('success', 'Income deleted successfully.');
    }

    public function expenseCreate()
    {
        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        $categories = DailyExpense::getCategories();
        $types = DailyExpense::getTypes();
        return view('artworks.daily-reports.expense-create', compact('branches', 'categories', 'types'));
    }

    public function expenseStore(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'nullable|exists:offices,id',
            'expense_date' => 'required|date',
            'expense_category' => 'required|string|max:255',
            'expense_type' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Cheque,Other',
            'paid_to' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $expense = DailyExpense::create([
            'branch_id' => $request->branch_id,
            'expense_date' => $request->expense_date,
            'expense_category' => $request->expense_category,
            'expense_type' => $request->expense_type,
            'reference_number' => $request->reference_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'recorded_by_user_id' => Auth::id(),
            'payment_method' => $request->payment_method,
            'paid_to' => $request->paid_to,
            'status' => $request->status,
            'metadata' => $request->metadata ?? null,
        ]);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Expense recorded successfully. Reference: ' . $expense->reference_number);
    }

    public function expenseEdit($id)
    {
        $expense = DailyExpense::findOrFail($id);
        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        $categories = DailyExpense::getCategories();
        $types = DailyExpense::getTypes();
        return view('artworks.daily-reports.expense-edit', compact('expense', 'branches', 'categories', 'types'));
    }

    public function expenseUpdate(Request $request, $id)
    {
        $expense = DailyExpense::findOrFail($id);

        $this->validate($request, [
            'branch_id' => 'nullable|exists:offices,id',
            'expense_date' => 'required|date',
            'expense_category' => 'required|string|max:255',
            'expense_type' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'payment_method' => 'required|in:Cash,Bank Transfer,Mobile Money,Card,Cheque,Other',
            'paid_to' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $expense->update([
            'branch_id' => $request->branch_id,
            'expense_date' => $request->expense_date,
            'expense_category' => $request->expense_category,
            'expense_type' => $request->expense_type,
            'reference_number' => $request->reference_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
            'paid_to' => $request->paid_to,
            'status' => $request->status,
            'metadata' => $request->metadata ?? $expense->metadata,
        ]);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function expenseDelete($id)
    {
        $expense = DailyExpense::findOrFail($id);
        $expense->delete();
        return redirect()->route('daily-reports.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function branchReport(Request $request)
    {
        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();
        
        $selectedBranch = $request->branch_id ?? null;
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $branchReports = [];
        
        if ($selectedBranch) {
            $reportBranches = $branches->where('id', $selectedBranch);
        } else {
            $reportBranches = $branches;
        }

        foreach ($reportBranches as $branch) {
            $incomes = DailyIncome::where('branch_id', $branch->id)
                ->whereBetween('income_date', [$dateFrom, $dateTo])
                ->get();
            
            $expenses = DailyExpense::where('branch_id', $branch->id)
                ->whereBetween('expense_date', [$dateFrom, $dateTo])
                ->where('status', 'Approved')
                ->get();

            $incomeByCategory = $incomes->groupBy('income_category')
                ->map(fn($items) => $items->sum('amount'));
            
            $expenseByCategory = $expenses->groupBy('expense_category')
                ->map(fn($items) => $items->sum('amount'));

            $branchReports[] = [
                'branch' => $branch,
                'total_income' => $incomes->sum('amount'),
                'total_expense' => $expenses->sum('amount'),
                'net_position' => $incomes->sum('amount') - $expenses->sum('amount'),
                'income_by_category' => $incomeByCategory,
                'expense_by_category' => $expenseByCategory,
                'income_count' => $incomes->count(),
                'expense_count' => $expenses->count(),
            ];
        }

        return view('artworks.daily-reports.branch-report', compact(
            'branchReports', 'branches', 'selectedBranch', 'dateFrom', 'dateTo'
        ));
    }

    public function reports(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $branchId = $request->branch_id;

        $incomeQuery = DailyIncome::whereBetween('income_date', [$dateFrom, $dateTo]);
        $expenseQuery = DailyExpense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'Approved');

        if ($branchId) {
            $incomeQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpense = $expenseQuery->sum('amount');
        $netPosition = $totalIncome - $totalExpense;

        $incomeByCategory = DailyIncome::whereBetween('income_date', [$dateFrom, $dateTo])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('income_category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('income_category')
            ->orderBy('total', 'desc')
            ->get();

        $expenseByCategory = DailyExpense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('expense_category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_category')
            ->orderBy('total', 'desc')
            ->get();

        $incomeByBranch = DailyIncome::whereBetween('income_date', [$dateFrom, $dateTo])
            ->select('branch_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->orderBy('total', 'desc')
            ->get();

        $expenseByBranch = DailyExpense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'Approved')
            ->select('branch_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->orderBy('total', 'desc')
            ->get();

        $dailyTrend = DailyIncome::whereBetween('income_date', [$dateFrom, $dateTo])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('income_date', DB::raw('SUM(amount) as income'))
            ->groupBy('income_date')
            ->orderBy('income_date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->income_date => $item->income]);

        $dailyExpenseTrend = DailyExpense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'Approved')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select('expense_date', DB::raw('SUM(amount) as expense'))
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->expense_date => $item->expense]);

        $dates = [];
        $period = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        while ($period <= $end) {
            $dates[] = $period->format('Y-m-d');
            $period->addDay();
        }

        $chartLabels = $dates;
        $chartIncome = $dates->map(fn($d) => $dailyTrend[$d] ?? 0);
        $chartExpense = $dates->map(fn($d) => $dailyExpenseTrend[$d] ?? 0);

        $branches = Office::whereNull('deleted_at')->orderBy('name')->get();

        return view('artworks.daily-reports.reports', compact(
            'totalIncome', 'totalExpense', 'netPosition',
            'incomeByCategory', 'expenseByCategory',
            'incomeByBranch', 'expenseByBranch',
            'chartLabels', 'chartIncome', 'chartExpense',
            'branches', 'dateFrom', 'dateTo', 'branchId'
        ));
    }
}
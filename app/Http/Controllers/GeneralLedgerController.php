<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Models\AdvanceTransaction;
use Carbon\Carbon;
use App\Models\CycleDates;
use App\Models\Client;
use Cartalyst\Sentinel\Roles\EloquentRole;
use App\Models\Expense;
use Excel;
use App\Exports\ExportReport;
use App\Http\Controllers\Flash;
use Illuminate\Http\Request;
use App\Models\GeneralLedger;
use App\Helpers\GeneralHelper;
use App\Models\LoanTransaction;
use App\Models\Loan;

use App\Models\Office;
use App\Models\UserRole;
use App\Models\Permission;
use PDF;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\LedgerIncome;
use App\Models\User;


class GeneralLedgerController extends Controller
{
    /*public function index()
{
    $user = Sentinel::getUser(); 
    $role = UserRole::where('user_id', $user->id)->first();
    if ($user->hasAccess('groups.create')) {
        return $this->summary();
    }
    
    $cycleEndDate = 24;
    $ledgerData = GeneralLedger::where('user_id', $user->id)->first();
    if ($ledgerData) {
        return redirect()->route('ledger.summary');
    }

    Session::flash('success', 'Ledger entry saved successfully.');
    return view('ledger.general', compact('user', 'cycleEndDate'));
}


   /* public function store(Request $request)
    {
        $user = Sentinel::getUser();
        $validatedData = $request->validate([
            'opening_balance' => 'required|numeric',

            //'total_income' => 'required|numeric',
            //'cycle_opening_uncollected' => 'required|numeric',
        ]);
       

        $general_ledger = new GeneralLedger();
        $general_ledger->user_id = $user->id;
        $general_ledger->office_id = $user->office_id;
        $general_ledger->opening_balance = $validatedData['opening_balance'];
        //$general_ledger->total_income = $validatedData['total_income'];
        //$general_ledger->cycle_opening_uncollected = $validatedData['cycle_opening_uncollected'];
        $general_ledger->closing_balance = $request->closing_balance;
        $general_ledger->save();

        return redirect()->route('ledger.summary');
    } */

    //ADMIN VIEW
    public function summary(Request $request)
    {
        $todaysDate = date('Y-m-d');
        $startLimitDate = '2025-01-04';
        $user = Sentinel::getUser();
        $startDate = $request->input('start_date') ?: $startLimitDate;
        $endDate = $request->input('end_date') ?: $todaysDate;
    
        //start date is not earlier than the start limit
        if ($startDate < $startLimitDate) {
            $startDate = $startLimitDate;
        }
    
	$totalAdvances = 0;
	$totalAdvancesPaid = 0;
        $totalExpenses = 0;
        $totalFullPayments = 0;
        $totalReloanedAmount = 0;
        $totalPartPayment = 0;
        $totalNewLoans = 0;
        $totalCashBalance = 0;
        //ADMIN INCOME TABLE
        $totalIncome = LedgerIncome::sum('amount');
        $branches = Office::all(); 
    
        foreach ($branches as $branch) {
    
            //Closing balance calculation FOR EACH BRANCH (transactions from start limit date to today)
            $branchAdvances = Advance::where('office_id', $branch->id)
                ->where('status', 'approved')
                ->whereBetween('date_approved', [$startLimitDate, $todaysDate])
                ->sum('amount');
    
            $branchAdvancesPaid = Advance::where('office_id', $branch->id)
            ->where('status', 'approved')
                ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
                ->sum('amount_paid');

            $branchExpenses = Expense::where('office_id', $branch->id)
                ->whereBetween('date', [$startLimitDate, $todaysDate])
                ->sum('amount');
    
            $branchFullPayments = LoanTransaction::where('office_id', $branch->id)
                ->where('transaction_type', 'repayment')
                ->where('payment_apply_to', 'full_payment')
                ->whereBetween('date', [$startLimitDate, $todaysDate])
                ->sum('credit');
    
            $branchReloanedAmount = LoanTransaction::where('office_id', $branch->id)
                ->where('payment_apply_to', 'reloan_payment')
                ->whereBetween('date', [$startLimitDate, $todaysDate])
                ->sum('credit');
    
            $branchPartPayment = LoanTransaction::where('office_id', $branch->id)
                ->where('payment_apply_to', 'part_payment')
                ->whereBetween('date', [$startLimitDate, $todaysDate])
                ->sum('credit');
    
            $branchNewLoans = LoanTransaction::where('office_id', $branch->id)
                ->where('transaction_type', 'disbursement')
                ->whereBetween('date', [$startLimitDate, $todaysDate])
                ->sum('debit');
    
            // Calculate the net change for the closing balance
            $netChange = ($branchFullPayments + $branchReloanedAmount + $branchPartPayment + $branchAdvancesPaid) 
                - ($branchNewLoans + $branchAdvances + $branchExpenses);
    
            // Get the cash balance for EACH branch
            $branchOpeningBalance = GeneralLedger::where('office_id', $branch->id)
                ->orderBy('created_at', 'desc')
                ->first()
                ->cash_balance ?? 0;
            //fetch each branches income
            $branchIncome = GeneralLedger::where('office_id', $branch->id)
                ->pluck('total_income')
                ->first() ?? 0;
    
            $branchClosingBalance = $branchOpeningBalance + $netChange + $branchIncome;
    
            //update total cash balance
            $totalCashBalance += $branchClosingBalance;

            //$totalCashBalance += $totalIncome; add installemtn amount / repaym
    
            //transactions being displayed (filtering by user-specified date range)
            $advances = Advance::where('office_id', $branch->id)
            ->where('status', 'approved')
            ->whereBetween('date_approved', [$startDate, $endDate])
            ->sum('amount');

            $advancesPaid = Advance::where('office_id', $branch->id)
            ->where('status', 'approved')
                ->whereBetween('last_update_date', [$startDate, $endDate])
                ->sum('amount_paid');
    
            $expenses = Expense::where('office_id', $branch->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');
    
            $fullPayments = LoanTransaction::where('office_id', $branch->id)
                ->where('transaction_type', 'repayment')
                ->where('payment_apply_to', 'full_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('credit');
    
            $reloanedAmount = LoanTransaction::where('office_id', $branch->id)
                ->where('payment_apply_to', 'reloan_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('credit');
    
            $partPayment = LoanTransaction::where('office_id', $branch->id)
                ->where('payment_apply_to', 'part_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('credit');
    
            $newLoans = LoanTransaction::where('office_id', $branch->id)
                ->where('transaction_type', 'disbursement')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('debit');
    
            
            $totalAdvances += $advances;
            $totalAdvancesPaid += $advancesPaid;
            $totalExpenses += $expenses;
            $totalFullPayments += $fullPayments;
            $totalReloanedAmount += $reloanedAmount;
            $totalPartPayment += $partPayment;
            $totalNewLoans += $newLoans;
        }
        
        $totalCashBalance += $totalIncome;
    
        return view('ledger.summary', compact(
            'totalCashBalance', 
            'branches', 
            'totalIncome',
            'totalAdvances', 
            'totalAdvancesPaid',
            'totalExpenses', 
            'totalFullPayments', 
            'totalReloanedAmount', 
            'totalPartPayment', 
            'totalNewLoans',
            'startDate', 
            'endDate'
        ));
    }

   

    //store income from ADMIN
    public function income_store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|string|max:255',
        ]);
        LedgerIncome::create([
            'date' => $request->input('date'),
            'amount' => $request->input('amount'),
            'from' => $request->input('source'),
        ]);
        return redirect()->route('ledger.summary')->with('success', 'Income added successfully.');
    }

    

//view of the closing balance alongside the branch
public function transactions()
{
   
    $user = Sentinel::getUser();
    //$office_id = $user->office_id;
    
    if (Sentinel::hasAccess('groups.create')) {
        $offices = Office::all();

    } elseif (Sentinel::hasAccess('offices')) {
        $branchId = $user->office_id; 
        $offices = Office::where('id', $branchId)->get(); 
    } else {
        $userOffice = $user->office;
    $provinceId = $userOffice->province_id;
    $offices = Office::where('province_id', $provinceId)->get();
    }
    
    $ledgerEntriesByOffice = [];
    
    foreach ($offices as $office) {
        $recentLedgerEntry = GeneralLedger::where('office_id', $office->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $netChange = $this->calculateNetChange($office, $recentLedgerEntry); 
        $openingBalance = $recentLedgerEntry ? $recentLedgerEntry->cash_balance : 0;
        $closingBalance = $openingBalance + $netChange;
        $ledgerEntriesByOffice[$office->name] = [
            'recentEntry' => $recentLedgerEntry,
            'closingBalance' => $closingBalance
        ];
    }

    return view('ledger.transactions', compact('ledgerEntriesByOffice'));
}

//helper function to calculate the net change for the office
private function calculateNetChange($office, $recentLedgerEntry) {
    $startLimitDate = '2025-01-04';
    $todaysDate = date('Y-m-d');
    
    $advancesTotal = Advance::where('office_id', $office->id)
        ->whereIn('status', ['approved','closed'])
        ->whereBetween('date_approved', [$startLimitDate, $todaysDate])
        ->sum('amount');
    
    $advancesTotalPaid = AdvanceTransaction::whereHas('advance', function ($query) use ($office) {
        $query->where('office_id', $office->id);})
        ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
        ->sum('amount_paid');

    $expensesTotal = Expense::where('office_id', $office->id)
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('amount');
    
    $fullPaymentsTotal = LoanTransaction::where('office_id', $office->id)
        ->where('transaction_type', 'repayment')
        ->where('payment_apply_to', 'full_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    
    $reloanedAmountTotal = LoanTransaction::where('office_id', $office->id)
        ->where('payment_apply_to', 'reloan_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    
    $partPaymentTotal = LoanTransaction::where('office_id', $office->id)
        ->where('payment_apply_to', 'part_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    
    $newLoansTotal = LoanTransaction::where('office_id', $office->id)
        ->where('transaction_type', 'disbursement')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('debit');
    
    $totalIncome = $recentLedgerEntry ? $recentLedgerEntry->total_income : 0;
        

    return $fullPaymentsTotal + $reloanedAmountTotal + $partPaymentTotal+ $advancesTotalPaid  + $totalIncome - ($advancesTotal + $expensesTotal + $newLoansTotal);
}

        

    public function show(Request $request, $officeName) {

        $office = Office::where('name', $officeName)->first();
        if (!$office) {
            abort(404);
        }
    
        $todaysDate = date('Y-m-d');
        $startLimitDate = '2025-01-04'; // Start date limit
        $endDate = $request->end_date ?: $todaysDate;
        $startDate = $request->start_date ?: $startLimitDate;
        
        if ($startDate < $startLimitDate) {
            $startDate = $startLimitDate;
        }
    
        //fetch the current cash balance
        $generalLedger = GeneralLedger::where('office_id', $office->id)->first();
        $openingBalance = $generalLedger ? $generalLedger->cash_balance : 0;
    
        $netChange = 0;
    
        $advancesTotal = Advance::where('office_id', $office->id)
            ->whereIn('status', ['approved', 'closed'])
            ->whereBetween('date_approved', [$startDate, $endDate])
            ->sum('amount');
        $netChange -= $advancesTotal;

        $advancesPaid = AdvanceTransaction::whereHas('advance', function ($query) use ($office) {
            $query->where('office_id', $office->id); })
        ->whereBetween('last_update_date', [$startDate, $endDate])
        ->sum('amount_paid');
        $netChange += $advancesPaid;

        $expensesTotal = Expense::where('office_id', $office->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
        $netChange -= $expensesTotal;

        $fullPaymentsTotal = LoanTransaction::where('office_id', $office->id)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        $netChange += $fullPaymentsTotal;

        $reloanedAmountTotal = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        $netChange += $reloanedAmountTotal;

        $partPaymentTotal = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        $netChange += $partPaymentTotal;

        $newLoansTotal = LoanTransaction::where('office_id', $office->id)
            ->where('transaction_type', 'disbursement')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('debit');
        $netChange -= $newLoansTotal;

        $totalIncome = $generalLedger ? $generalLedger->total_income : 0;
        $netChange += $totalIncome;
    
        $closingBalance = $openingBalance + $netChange;
    
        
        $advances = Advance::where('office_id', $office->id)
            ->whereIn('status', ['approved', 'closed'])
            ->whereBetween('last_update_date', [$startDate, $endDate])
            ->sum('amount');

        $advancesPaid = AdvanceTransaction::whereHas('advance', function ($query) use ($office) {
            $query->where('office_id', $office->id);})
        ->whereBetween('last_update_date', [$startDate, $endDate])
        ->sum('amount_paid');
            
        
        
        $expenses = Expense::where('office_id', $office->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
        
        $fullPayments = LoanTransaction::where('office_id', $office->id)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        
        $reloanedAmount = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        
        $partPayment = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('credit');
        
        $newLoans = LoanTransaction::where('office_id', $office->id)
            ->where('transaction_type', 'disbursement')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('debit');

        
        return view('ledger.show', compact('endDate', 'startDate', 'advances', 'advancesPaid', 'expenses', 
            'fullPayments', 'reloanedAmount', 'partPayment', 
            'newLoans', 'office', 'officeName', 'closingBalance', 'generalLedger', 'totalIncome'
        ));
    }

    //branch income stored
    public function store(Request $request, $officeName) {
        try {
            $user = Sentinel::getUser();
            $office = Office::where('name', $officeName)->first();
            if (!$office) {
                abort(404);
            }
            if (!$user->hasAccess('groups.create') && !$user->hasAccess('offices') && $user->office->province_id != $office->province_id) {
                abort(403, 'Unauthorized action.');
            }

            $validatedData = $request->validate([
                'income_amount' => 'required|numeric',
                'date_received' => 'required|date',
            ]);

            $office_id = $office->id;
            
            if (!$user->hasAccess('groups.create') && !$user->hasAccess('offices')) {
                $userOffice = $user->office;
                $provinceId = $userOffice->province_id;
                $provinceOffices = Office::where('province_id', $provinceId)->pluck('id')->toArray();
                
                if (!in_array($office_id, $provinceOffices)) {
                    abort(403, 'You can only enter income for offices in your province.');
                }
            }

            $generalLedger = GeneralLedger::where('office_id', $office->id)->first();
            $totalIncome = $generalLedger ? $generalLedger->total_income : 0;
            
            if ($generalLedger) {
                $generalLedger->total_income += $validatedData['income_amount'];
                $generalLedger->save();
            } else {
                GeneralLedger::create([
                    'office_id' => $office->id,
                    'total_income' => $validatedData['income_amount'],
                ]);
            }
        
            return redirect()->route('ledger.show', ['officeName' => $officeName])
                ->with('success', 'Income added successfully.');
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    
    

    public function generateReport(Request $request)
{

    $todaysDate = date('Y-m-d');
    $officeId = $request->input('office_id');
    $officeName = Office::find($officeId)->name;
    $startLimitDate = '2025-01-04'; 
    $startDate = $request->input('start_date') ?: $startLimitDate;
    $endDate = $request->input('end_date') ?: $todaysDate;
    $format = $request->input('format', 'pdf');

    if (!$officeId) {
        return redirect()->back()->withErrors('Office ID is not provided in the request.');
        }

    if ($startDate < $startLimitDate) {
        $startDate = $startLimitDate;
    }
    
    $generalLedger = GeneralLedger::where('office_id', $officeId)->first();
    $openingBalance = $generalLedger ? $generalLedger->cash_balance : 0;

    $netChange = 0;
    //closing balance calculation
    
    $advancesTotal = Advance::where('office_id', $officeId)
    ->whereIn('status', ['approved', 'closed'])
    ->whereBetween('date_approved', [$startLimitDate, $todaysDate])
    ->sum('amount');
    $netChange -= $advancesTotal;


    $advancesPaid = AdvanceTransaction::whereHas('advance', function ($query) use ($officeId) {
        $query->where('office_id', $officeId);
    })
    ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
    ->sum('amount_paid');

    $netChange += $advancesPaid;

    $expensesTotal = Expense::where('office_id', $officeId)
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('amount');
    $netChange -= $expensesTotal;

    $fullPaymentsTotal = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'repayment')
        ->where('payment_apply_to', 'full_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $fullPaymentsTotal;

    $reloanedAmountTotal = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'reloan_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $reloanedAmountTotal;

    $partPaymentTotal = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'part_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $partPaymentTotal;

    $newLoansTotal = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'disbursement')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('debit');
    $netChange -= $newLoansTotal;
    $totalIncome = $generalLedger ? $generalLedger->total_income : 0;
        $netChange += $totalIncome;

    $closingBalance = $openingBalance + $netChange;
    //


    //detailed transactions within the specified date range
    $advances = Advance::where('office_id', $officeId)
        ->whereIn('status', ['approved', 'closed'])
        ->whereBetween('date_approved', [$startDate, $endDate])
        ->get(['id','first_name', 'last_name', 'amount', 'date_approved']);

    $advancesPaid = AdvanceTransaction::whereHas('advance', function ($query) use ($officeId) {
            $query->where('office_id', $officeId);
        })
        ->whereBetween('last_update_date', [$startDate, $endDate])
        ->get(['advance_id', 'amount_paid', 'last_update_date']);
    
    $expenses = Expense::where('office_id', $officeId)
        ->whereBetween('date', [$startDate, $endDate])
        ->get(['id','expense_type', 'name', 'amount', 'date']);

    $fullPayments = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'repayment')
        ->where('payment_apply_to', 'full_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get(['id', 'loan_id','client_id', 'credit', 'date']);

    $reloanedAmount = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'reloan_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get(['id', 'loan_id', 'client_id', 'credit', 'date']);

    $partPayment = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'part_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get(['id', 'loan_id', 'client_id', 'credit', 'date']);
        //update
        $newLoans = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'disbursement')
            ->whereBetween('date', [$startDate, $endDate])
            ->get(['id', 'loan_id','client_id', 'debit', 'date']);

    
    $data = compact(
        'startDate', 'endDate', 'advances', 'advancesPaid', 'expenses', 
        'fullPayments', 'reloanedAmount', 'partPayment', 
        'newLoans', 'closingBalance', 'officeId', 'officeName', 'totalIncome'
    );

    $pdf = PDF::loadView('ledger.ledger_report_pdf', compact(
        'startDate', 'endDate', 'advances', 'advancesPaid', 'expenses', 
        'fullPayments', 'reloanedAmount', 'partPayment', 
        'newLoans', 'closingBalance', 'officeId', 'officeName', 'totalIncome'
    ));
    
    return $pdf->download('ledger_report_' . $startDate . '_to_' . $endDate . '.pdf');

}


public function generateExcelReport(Request $request)
{
    $todaysDate = date('Y-m-d');
    $officeId = $request->input('office_id');
    $officeName = Office::find($officeId)->name;
    $startLimitDate = '2025-01-04'; 
    $startDate = $request->input('start_date') ?: $startLimitDate;
    $endDate = $request->input('end_date') ?: $todaysDate;

    if (!$officeId) {
        return redirect()->back()->withErrors('Office ID is not provided in the request.');
    }

    if ($startDate < $startLimitDate) {
        $startDate = $startLimitDate;
    }

    $generalLedger = GeneralLedger::where('office_id', $officeId)->first();
    $openingBalance = $generalLedger ? $generalLedger->cash_balance : 0;
    $netChange = 0;

    // totals
    $advancesTotal = Advance::where('office_id', $officeId)
        ->where('status', 'approved')
        ->whereBetween('date_approved', [$startLimitDate, $todaysDate])
        ->sum('amount');
    $netChange -= $advancesTotal;

    $advancesPaid = Advance::where('office_id', $officeId)
        ->where('status', 'approved')
        ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
        ->sum('amount_paid');
        $netChange += $advancesPaid;

    $expensesTotal = Expense::where('office_id', $officeId)
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('amount');
    $netChange -= $expensesTotal;

    $fullPaymentsTotal = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'repayment')
        ->where('payment_apply_to', 'full_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $fullPaymentsTotal;

    $reloanedAmountTotal = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'reloan_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $reloanedAmountTotal;

    $partPaymentTotal = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'part_payment')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('credit');
    $netChange += $partPaymentTotal;

    $newLoansTotal = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'disbursement')
        ->whereBetween('date', [$startLimitDate, $todaysDate])
        ->sum('debit');
    $netChange -= $newLoansTotal;

    $totalIncome = $generalLedger ? $generalLedger->total_income : 0;
        $netChange += $totalIncome;

    $closingBalance = $openingBalance + $netChange;

    // Fetch detailed transactions
    $advances = Advance::where('office_id', $officeId)
        ->where('status', 'approved')
        ->whereBetween('last_update_date', [$startDate, $endDate])
        ->get();

    $advancesPaid = Advance::where('office_id', $office->id)
        ->where('status', 'approved')
        ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
        ->get();

    $expenses = Expense::where('office_id', $officeId)
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    $fullPayments = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'repayment')
        ->where('payment_apply_to', 'full_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    $reloanedAmount = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'reloan_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    $partPayment = LoanTransaction::where('office_id', $officeId)
        ->where('payment_apply_to', 'part_payment')
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    $newLoans = LoanTransaction::where('office_id', $officeId)
        ->where('transaction_type', 'disbursement')
        ->whereBetween('date', [$startDate, $endDate])
        ->get();

    $data = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'advances' => $advances,
        'advancesPaid' => $advancesPaid,
        'expenses' => $expenses,
        'fullPayments' => $fullPayments,
        'reloanedAmount' => $reloanedAmount,
        'partPayment' => $partPayment,
        'newLoans' => $newLoans,
        'closingBalance' => $closingBalance,
        'officeId' => $officeId,
        'officeName' => $officeName,
        'totalIncome' => $totalIncome
    ];

    return Excel::download(new ExportReport('ledger.ledger_report_excel', $data), 'ledger_report_' . $startDate . '_to_' . $endDate . '.xlsx');
}

//ADMIN excel
public function allgenerateExcelReport(Request $request)
{
    $todaysDate = date('Y-m-d');
    $offices = Office::all(); 
    $startLimitDate = '2025-01-04'; 
    $startDate = $request->input('start_date') ?: $startLimitDate;
    $endDate = $request->input('end_date') ?: $todaysDate;

    if ($startDate < $startLimitDate) {
        $startDate = $startLimitDate;
    }

    $allBranchData = [];
    $totalClosingBalance = 0;
    $netChange = 0;

    // ADMIN INCOME
    $incomeTotal = LedgerIncome::sum('amount');

    foreach ($offices as $office) {
        $officeId = $office->id;
        $officeName = $office->name;  

        // FetchLedger for EACH office
        $generalLedger = GeneralLedger::where('office_id', $officeId)->first();
        $openingBalance = $generalLedger ? $generalLedger->cash_balance : 0;
        
        // Branch-specific netChange calculation
        $branchNetChange = 0;

        //totals for EACH branch
        $advancesTotal = Advance::where('office_id', $officeId)
            ->where('status', 'approved')
            ->whereBetween('date_approved', [$startLimitDate, $todaysDate])
            ->sum('amount');
        $branchNetChange -= $advancesTotal;

        $advancesPaid = Advance::where('office_id', $officeId)
        ->where('status', 'approved')
        ->whereBetween('last_update_date', [$startLimitDate, $todaysDate])
        ->sum('amount_paid');



        $expensesTotal = Expense::where('office_id', $officeId)
            ->whereBetween('date', [$startLimitDate, $todaysDate])
            ->sum('amount');
        $branchNetChange -= $expensesTotal;

        $fullPaymentsTotal = LoanTransaction::where('office_id', $officeId)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('date', [$startLimitDate, $todaysDate])
            ->sum('credit');
        $branchNetChange += $fullPaymentsTotal;

        $reloanedAmountTotal = LoanTransaction::where('office_id', $officeId)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('date', [$startLimitDate, $todaysDate])
            ->sum('credit');
        $branchNetChange += $reloanedAmountTotal;

        $partPaymentTotal = LoanTransaction::where('office_id', $officeId)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('date', [$startLimitDate, $todaysDate])
            ->sum('credit');
        $branchNetChange += $partPaymentTotal;

        $newLoansTotal = LoanTransaction::where('office_id', $officeId)
            ->where('transaction_type', 'disbursement')
            ->whereBetween('date', [$startLimitDate, $todaysDate])
            ->sum('debit');
        $branchNetChange -= $newLoansTotal;

        $branchIncomeTotal = GeneralLedger::where('office_id', $officeId)
            ->pluck('total_income')
            ->first();

        //closing balance PER Bbranch
        //$branchNetChange = ($fullPaymentsTotal + $reloanedAmountTotal + $partPaymentTotal) 
        //- ($newLoansTotal + $advancesTotal + $expensesTotal);

        // Closing balance per branch
        $closingBalance = $openingBalance + $branchNetChange + $branchIncomeTotal;
        $totalClosingBalance += $closingBalance;

        //collect all data for EACH branch
        $allBranchData[] = [
            'officeName' => $officeName,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'advances' => Advance::where('office_id', $officeId)
                ->where('status', 'approved')
                ->whereBetween('last_update_date', [$startDate, $endDate])
                ->get(),
            'advancesPaid' => Advance::where('office_id', $officeId)
                ->where('status', 'approved')
                ->whereBetween('last_update_date', [$startDate, $endDate])
                ->get(),
            'expenses' => Expense::where('office_id', $officeId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),
            'fullPayments' => LoanTransaction::where('office_id', $officeId)
                ->where('transaction_type', 'repayment')
                ->where('payment_apply_to', 'full_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),
            'reloanedAmount' => LoanTransaction::where('office_id', $officeId)
                ->where('payment_apply_to', 'reloan_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),
            'partPayment' => LoanTransaction::where('office_id', $officeId)
                ->where('payment_apply_to', 'part_payment')
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),
            'newLoans' => LoanTransaction::where('office_id', $officeId)
                ->where('transaction_type', 'disbursement')
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),

        
            //'closingBalance' => $closingBalance
        ];
    }

    // Fetch ADMIN INCOME
    $incomeTransactions = LedgerIncome::all();


    //closing balance with the sum of incomeTotal
    $totalClosingBalance += $incomeTotal;

    $data = [
        'allBranchData' => $allBranchData,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalClosingBalance' => $totalClosingBalance,
        //'incomeTransactions' => $incomeTransactions,
        'incomeTotal' => $incomeTotal
    ];

    return Excel::download(new ExportReport('ledger.all_report_excel', $data), 'ledger_report_' . $startDate . '_to_' . $endDate . '.xlsx');
}


}

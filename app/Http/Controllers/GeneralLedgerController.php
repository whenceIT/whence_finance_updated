<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use Carbon\Carbon;
use App\Models\CycleDates;
use Cartalyst\Sentinel\Roles\EloquentRole;
use App\Models\Expense;
use App\Http\Controllers\Flash;
use Illuminate\Http\Request;
use App\Models\GeneralLedger;
use App\Helpers\GeneralHelper;
use App\Models\LoanTransaction;
use App\Models\Loan;
use App\Models\Office;
use App\Models\UserRole;
use App\Models\Permission;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;


class GeneralLedgerController extends Controller
{

    public function index()
{
    $user = Sentinel::getUser(); 
    if ($user->hasAccess('groups.create')) {
        return $this->summary();
    }
    $cycle_dates = CycleDates::where('loan_officer_id', $user->id)->first();
    
    if (!$cycle_dates || !$cycle_dates->cycle_end_date) {

        $currentDayOfMonth = date('d');

        if ($currentDayOfMonth < 24) {
            $cycleEndDate = 24;
        } else {
            $cycleEndDate = date('d', strtotime('24th day of next month'));
        }

        if ($cycle_dates) {
            $cycle_dates->cycle_end_date = $cycleEndDate;
            $cycle_dates->save();
        } else {
            $cycle_dates = new CycleDates();
            $cycle_dates->loan_officer_id = $user->id;
            $cycle_dates->cycle_end_date = $cycleEndDate;
            $cycle_dates->save();
        }
        //update ob to cb after a month
        $previousCycleEndDate = date('Y-m-d', strtotime('-1 months', strtotime($cycleEndDate)));
        $previousCycleLedger = GeneralLedger::where('user_id', $user->id)
            ->whereDate('created_at', $previousCycleEndDate)
            ->first();

        if ($previousCycleLedger) {
            $openingBalance = $previousCycleLedger->closing_balance;
            $general_ledger = new GeneralLedger();
            $general_ledger->user_id = $user->id;
            $general_ledger->opening_balance = $openingBalance;
            $general_ledger->save();
        }
    }

    $ledgerData = GeneralLedger::where('user_id', $user->id)->first();
    if ($ledgerData) {
        return redirect()->route('ledger.summary');
    }

    Session::flash('success', 'Ledger entry saved successfully.');
    return view('ledger.general', compact('user', 'cycle_dates'));
}


    public function store(Request $request)
    {
        $user = Sentinel::getUser();
        $validatedData = $request->validate([
            'opening_balance' => 'required|numeric',
            'total_income' => 'required|numeric',
            'cycle_opening_uncollected' => 'required|numeric',
        ]);
       

        $general_ledger = new GeneralLedger();
        $general_ledger->user_id = $user->id;
        $general_ledger->office_id = $user->office_id;
        $general_ledger->opening_balance = $validatedData['opening_balance'];
        $general_ledger->total_income = $validatedData['total_income'];
        $general_ledger->cycle_opening_uncollected = $validatedData['cycle_opening_uncollected'];
        $general_ledger->closing_balance = $request->closing_balance;
        $general_ledger->save();

        return redirect()->route('ledger.summary');
    }

    public function summary()
    {
        $user = Sentinel::getUser();
        $userId = Sentinel::getUser()->id;
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->office->province_id;
        $role = UserRole::where('user_id',$userId)->first();
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $cycleDate = CycleDates::where('loan_officer_id', $user->id)->first();
        if ($cycleDate != null) {
            $targetDate = $use . $cycleDate->cycle_end_date;
        } else {
            $targetDate = $use . $num;
        }
        $targetDate = date('Y-m-d', strtotime($targetDate));
        if ($todaysDate > $targetDate) {
            $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
        }
        $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
        $currentDayOfMonth = date('d');

        //if ($currentDayOfMonth <= 24) {
        //$branchtargetDate = date('Y-m-24');
        //} else {
        //$branchtargetDate = date('Y-m-24', strtotime('+1 month'));
        //}

        $ledgerData = GeneralLedger::where('user_id', $user->id)->first();
        $startDate = $compareDate;
        $endDate = $targetDate; 

        //admins
        if ($user->hasAccess('groups.create')) {
        //$branchtargetDate = 24
            $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));

            // Initialize variables to store calculated values
            $total_cycle_opening_uncollected_amount = 0;
            $totalAdvances = 0;
            $totalExpenses = 0;
            $totalFullPayments = 0;
            $totalReloanedAmount = 0;
            $totalPartPayment = 0;
            $totalNewLoans = 0;
            $closingBalance = 0;

            $loans = Loan::with('transactions')->get();
            foreach ($loans as $loan) {
                $MoneyGivenOut = 0;
                $MoneyCollected = 0;
                $charges = 0;
                foreach ($loan->transactions as $transaction) {
                    if ($transaction->date <= $compareDate) {
                        $MoneyGivenOut += $transaction->debit;
                        if ($transaction->transaction_type != 'interest_waiver') {
                            $MoneyCollected += $transaction->credit;
                        }
                        if ($transaction->transaction_type == 'specified_due_date_fee') {
                            $charges += $transaction->debit;
                        }
                    }
                }
                $balance = ($MoneyGivenOut - $MoneyCollected - $charges);
                if ($balance < 0) {
                    $balance = 0;
                }
                $total_cycle_opening_uncollected_amount += $balance;
            }
        
            
            $totalAdvances = Advance::whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

            $totalExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
            $totalFullPayments = LoanTransaction::where('payment_apply_to', 'full_payment')
            ->where('transaction_type', 'repayment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalReloanedAmount = LoanTransaction::where('payment_apply_to', 'reloan_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            //$totalCashReloan = LoanTransaction::where('office_id', $userOfficeId)
            //->where('payment_apply_to', 'reloan_payment')
            //->whereBetween('created_at', [$startDate, $endDate])
            //->sum('credit');        

            $totalPartPayment = LoanTransaction::where('payment_apply_to', 'part_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalNewLoans = Loan::whereBetween('approved_date', [$startDate, $endDate])
            ->sum('principal'); 

            $branchOpeningBalances = GeneralLedger::pluck('opening_balance')->sum();
            $branchClosingBalances = GeneralLedger::pluck('closing_balance')->sum();

            return view('ledger.summary', compact('user', 'endDate', 'startDate', 'ledgerData', 'totalAdvances', 'totalExpenses', 'totalFullPayments', 'totalReloanedAmount', 'totalPartPayment', 'totalNewLoans', 'total_cycle_opening_uncollected_amount', 'branchOpeningBalances', 'branchClosingBalances'));

            //branch loans
        }elseif ($user-> hasAccess('offices')) {
            $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
            $userOfficeId = Sentinel::getUser()->office_id;
            $loans = Loan::with('transactions')->where('office_id', $userOfficeId)->get();
            $cycle_opening_uncollected_amount = 0;
            foreach ($loans as $loan) {
                $MoneyGivenOut = 0;
                $MoneyCollected = 0;
                $charges = 0;
                foreach ($loan->transactions as $transaction) {
                    if ($transaction->date <= $compareDate) {
                        $MoneyGivenOut += $transaction->debit;
                        if ($transaction->transaction_type != 'interest_waiver') {
                            $MoneyCollected += $transaction->credit;
                        }
                        if ($transaction->transaction_type == 'specified_due_date_fee') {
                            $charges += $transaction->debit;
                        }
                    }
                }
                $balance = ($MoneyGivenOut - $MoneyCollected - $charges);
                if ($balance < 0) {
                    $balance = 0;
                }
                $cycle_opening_uncollected_amount += $balance;
            }
        
            $advances = Advance::where('office_id', $userOfficeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
           
            $expenses = Expense::where('office_id', $userOfficeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
            $fullPayments = LoanTransaction::where('office_id', $userOfficeId)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $reloanedAmount = LoanTransaction::where('office_id', $userOfficeId)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            //$totalCashReloan = LoanTransaction::where('office_id', $userOfficeId)
            //->where('payment_apply_to', 'reloan_payment')
            //->whereBetween('created_at', [$startDate, $endDate])
            //->sum('credit');        

            $partPayment = LoanTransaction::where('office_id', $userOfficeId)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $newLoans = Loan::where('office_id', $userOfficeId)
            ->whereBetween('approved_date', [$startDate, $endDate])
            ->sum('principal'); 

            $closingBalance = (
                $ledgerData->opening_balance +
                $fullPayments +
                $reloanedAmount +
                $ledgerData->total_income +
                $partPayment
            ) - $expenses - $newLoans;
            $ledgerData->closing_balance = $closingBalance;
            $ledgerData->save();

            return view('ledger.summary', compact('user', 'endDate', 'startDate', 'ledgerData', 'advances', 'expenses', 'fullPayments', 'reloanedAmount', 'partPayment', 'newLoans', 'cycle_opening_uncollected_amount', 'closingBalance'));
            //provincial loans
        } else {
            $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
            $provinceOffices = Office::where('province_id', $userProvince)->pluck('id');

            $cycle_opening_uncollected_amount = 0;
            $totalAdvances = 0;
            $totalExpenses = 0;
            $totalFullPayments = 0;
            $totalReloanedAmount = 0;
            $totalPartPayment = 0;
            $totalNewLoans = 0;
            $closingBalance = 0;

            $loans = Loan::with('transactions')->whereIn('office_id', $provinceOffices)->get();
            foreach ($loans as $loan) {
                $MoneyGivenOut = 0;
                $MoneyCollected = 0;
                $charges = 0;
                foreach ($loan->transactions as $transaction) {
                    if ($transaction->date <= $compareDate) {
                        $MoneyGivenOut += $transaction->debit;
                        if ($transaction->transaction_type != 'interest_waiver') {
                            $MoneyCollected += $transaction->credit;
                        }
                        if ($transaction->transaction_type == 'specified_due_date_fee') {
                            $charges += $transaction->debit;
                        }
                    }
                }
                $balance = ($MoneyGivenOut - $MoneyCollected - $charges);
                if ($balance < 0) {
                    $balance = 0;
                }
                $cycle_opening_uncollected_amount += $balance;
            }
        
            $totalAdvances = Advance::whereIn('office_id', $provinceOffices)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

            $totalExpenses = Expense::whereIn('office_id', $provinceOffices)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
            $totalFullPayments = LoanTransaction::whereIn('office_id', $provinceOffices)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalReloanedAmount = LoanTransaction::whereIn('office_id', $provinceOffices)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            //$totalCashReloan = LoanTransaction::whereIn('office_id', $provinceOffices)
            //->where('payment_apply_to', 'reloan_payment')
            //->whereBetween('created_at', [$startDate, $endDate])
            //->sum('credit');        

            $totalPartPayment = LoanTransaction::whereIn('office_id', $provinceOffices)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalNewLoans = Loan::whereIn('office_id', $provinceOffices)
            ->whereBetween('approved_date', [$startDate, $endDate])
            ->sum('principal'); 

            $closingBalance = (
                $ledgerData->opening_balance +
                $totalFullPayments +
                $totalReloanedAmount +
                $ledgerData->total_income +
                $totalPartPayment
            ) - $totalExpenses - $totalNewLoans;
            
        }


        return view('ledger.summary', compact('user', 'endDate', 'startDate', 'ledgerData', 'totalAdvances', 'totalExpenses', 'totalFullPayments', 'totalReloanedAmount', 'totalPartPayment', 'totalNewLoans', 'cycle_opening_uncollected_amount', 'closingBalance'));
    }

    public function transactions()
    {
    if (!Sentinel::hasAccess('groups.create')) {
        Flash::warning("Permission Denied");
        return redirect()->back();
    }
    
    $offices = Office::all();

    $ledgerEntriesByOffice = [];
    foreach ($offices as $office) {
        $recentLedgerEntry = GeneralLedger::where('office_id', $office->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($recentLedgerEntry) {
            $ledgerEntriesByOffice[$office->name] = $recentLedgerEntry;
        }
    }

    return view('ledger.transactions', compact('ledgerEntriesByOffice'));
    }

    public function show($officeName)
    {
        $office = Office::where('name', $officeName)->first();
        if (!$office) {
            abort(404); 
        }
        $ledgerData = GeneralLedger::where('office_id', $office->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $targetDate = date('Y-m-24');
        $compareDate = date('Y-m-d', strtotime('-1 month', strtotime($targetDate)));
        $startDate = $compareDate;
        $endDate = $targetDate;

        $loans = Loan::with('transactions')->where('office_id', $office->id)->get();
            $cycle_opening_uncollected_amount = 0;
            foreach ($loans as $loan) {
                $MoneyGivenOut = 0;
                $MoneyCollected = 0;
                $charges = 0;
                foreach ($loan->transactions as $transaction) {
                    if ($transaction->date <= $compareDate) {
                        $MoneyGivenOut += $transaction->debit;
                        if ($transaction->transaction_type != 'interest_waiver') {
                            $MoneyCollected += $transaction->credit;
                        }
                        if ($transaction->transaction_type == 'specified_due_date_fee') {
                            $charges += $transaction->debit;
                        }
                    }
                }
                $balance = ($MoneyGivenOut - $MoneyCollected - $charges);
                if ($balance < 0) {
                    $balance = 0;
                }
                $cycle_opening_uncollected_amount += $balance;
            }
        
            $totalAdvances = Advance::where('office_id', $office->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
           
            $totalExpenses = Expense::where('office_id', $office->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
            $totalFullPayments = LoanTransaction::where('office_id', $office->id)
            ->where('transaction_type', 'repayment')
            ->where('payment_apply_to', 'full_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalReloanedAmount = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'reloan_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            //$totalCashReloan = LoanTransaction::where('office_id', $office->id)
            //->where('payment_apply_to', 'reloan_payment')
            //->whereBetween('created_at', [$startDate, $endDate])
            //->sum('credit');        

            $totalPartPayment = LoanTransaction::where('office_id', $office->id)
            ->where('payment_apply_to', 'part_payment')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('credit');

            $totalNewLoans = Loan::where('office_id', $office->id)
            ->whereBetween('approved_date', [$startDate, $endDate])
            ->sum('principal'); 

            /*$closingBalance = (
                $ledgerData->opening_balance +
                $totalFullPayments +
                $totalReloanedAmount +
                $ledgerData->total_income +
                $totalPartPayment
            ) - $totalExpenses - $totalNewLoans;
            $ledgerData->closing_balance = $closingBalance;
            $ledgerData->save();*/
             

            return view('ledger.show', compact( 'endDate', 'startDate', 'ledgerData', 'totalAdvances', 'totalExpenses', 'totalFullPayments', 'totalReloanedAmount', 'totalPartPayment', 'totalNewLoans', 'cycle_opening_uncollected_amount', 'office', 'officeName'));
    }
}
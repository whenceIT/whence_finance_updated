<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\GeneralHelper;
use App\Exports\ExportReport;
use App\Models\Loan;
use App\Models\Office; 
use App\Models\Client; 
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

use PDF;
use Excel;
use App\Models\User;

class PerformanceMetricsController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {
        $cycles = 2; // Last 12 months (1 year)
        $newClientsData = [];
        $labels = [];
        $user = Sentinel::getUser();
        $user_role = $user->role->role_id;
        $user_branch = $user->office_id;
        $user_province = $user->office->province_id;
        $branch = $request->office_id;
        $offices = Office::all(); 
        $officeId = $request->input('office_id');

        if (!empty($branch)) {
            $loanConsultants = User::where('office_id', $branch)
                ->whereHas('role', function($query) {
                    $query->whereIn('role_id', [3, 4, 6]);
                })
                ->with(['office', 'loan.transactions'])
                ->get();
        } else {
            $loanConsultants = User::whereHas('role', function($query) {
                $query->whereIn('role_id', [3, 4, 6]);
            })
            ->with(['office', 'loan.transactions'])
            ->get();
        }
        

        $dates = [];
        $target_dates = [];
        $compare_dates = [];
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $endDate = $use . $num;
        $endDate = date('Y-m-d', strtotime($endDate));
        if ($todaysDate > $endDate) {
            $endDate = date('Y-m-d', strtotime($endDate . ' + 1 months'));
        }
        $startDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
        for ($x = 0; $x < 2; $x++) {  //load 6monts
            if ($x != 0) {
                $endDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
                $startDate = date('Y-m-d', strtotime($startDate . ' - 1 months'));
            }
            array_push($dates, new \Carbon\Carbon($startDate));
            array_push($target_dates, $endDate);
            array_push($compare_dates, $startDate);
        }

        //newclients
        /*for ($x = 0; $x < $cycles; $x++) {
            if ($x != 0) {
                $endDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
                $startDate = date('Y-m-d', strtotime($startDate . ' - 1 months'));
            }
            array_push($dates, Carbon::parse($startDate));
            array_push($target_dates, Carbon::parse($endDate));
            array_push($compare_dates, Carbon::parse($startDate));
        }

        /* Fetch new clients for each cycle
        foreach ($dates as $index => $date) {
            $targetDate = $target_dates[$index];
            $compareDate = $compare_dates[$index];

            // Query to fetch new clients within the date range for the cycle
            $newClientsCount = Client::whereBetween('created_at', [$compareDate->startOfDay(), $targetDate->endOfDay()])
                ->count();
        
            // Add data to the arrays
            $newClientsData[] = $newClientsCount;
            $labels[] = $targetDate->format('M Y');
        }*/

    return view('performance_metrics.index', compact('offices', 'officeId', 'loanConsultants', 'branch', 'user_role', 'user_branch', 'user_province', 'dates', 'target_dates', 'compare_dates', 'newClientsData', 'labels'));
    }

    public function targets( Request $request)
    {
        $user = Sentinel::getUser();
        $user_role = $user->role->role_id;
        $user_branch = $user->office_id;
        $user_province = $user->office->province_id;
        $branch = $request->office_id;
        $offices = Office::all(); 
        $officeId = $request->input('office_id');

        if (!empty($branch)) {
            $loanConsultants = User::where('office_id', $branch)->whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        } else {
            $loanConsultants = User::whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        }

        $dates = [];
        $target_dates = [];
        $compare_dates = [];
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $endDate = $use . $num;
        $endDate = date('Y-m-d', strtotime($endDate));
        if ($todaysDate > $endDate) {
            $endDate = date('Y-m-d', strtotime($endDate . ' + 1 months'));
        }
        $startDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
        for ($x = 0; $x < 2; $x++) {  //load 6monts
            if ($x != 0) {
                $endDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
                $startDate = date('Y-m-d', strtotime($startDate . ' - 1 months'));
            }
            array_push($dates, new \Carbon\Carbon($startDate));
            array_push($target_dates, $endDate);
            array_push($compare_dates, $startDate);
        }

        return view('performance_metrics.targets', compact('offices', 'officeId', 'loanConsultants', 'branch', 'user_role', 'user_branch', 'user_province', 'dates', 'target_dates', 'compare_dates'));
    }

    public function uncollected(Request $request)
    {
        $user = Sentinel::getUser();
        $user_role = $user->role->role_id;
        $user_branch = $user->office_id;
        $user_province = $user->office->province_id;
        $branch = $request->office_id;
        $offices = Office::all(); 
        $officeId = $request->input('office_id');

        if (!empty($branch)) {
            $loanConsultants = User::where('office_id', $branch)->whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        } else {
            $loanConsultants = User::whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        }

        $dates = [];
        $target_dates = [];
        $compare_dates = [];
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $endDate = $use . $num;
        $endDate = date('Y-m-d', strtotime($endDate));
        if ($todaysDate > $endDate) {
            $endDate = date('Y-m-d', strtotime($endDate . ' + 1 months'));
        }
        $startDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
        for ($x = 0; $x < 2; $x++) { 
            if ($x != 0) {
                $endDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
                $startDate = date('Y-m-d', strtotime($startDate . ' - 1 months'));
            }
            array_push($dates, new \Carbon\Carbon($startDate));
            array_push($target_dates, $endDate);
            array_push($compare_dates, $startDate);
        }


        return view('performance_metrics.uncollected', compact('offices', 'officeId', 'loanConsultants', 'branch', 'user_role', 'user_branch', 'user_province', 'dates', 'target_dates', 'compare_dates'));
    }


    public function lowPerformance(Request $request)
    {
        $user = Sentinel::getUser();
        $user_role = $user->role->role_id;
        $user_branch = $user->office_id;
        $user_province = $user->office->province_id;
        $branch = $request->office_id;
        $offices = Office::all(); 
        $officeId = $request->input('office_id');
        $dates = [];
        $new_loans_cycle = 0;
        $cycle_reloan_payments = 0;
        $target_dates = [];
        $compare_dates = [];
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $targetDate = $use . $num;
        $targetDate = date('Y-m-d',strtotime($targetDate));
            if($todaysDate > $targetDate){
                $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
            }
            $compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));

        if (!empty($branch)) {
            $loanConsultants = User::where('office_id', $branch)->whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        } else {
            $loanConsultants = User::whereHas('role', function($query) {
                $query->where('role_id', 3);
            })->with(['office', 'loan.transactions'])->get();
        }
        /*$filteredLoans = [];

	foreach ($loanConsultants as $user) {
	$new_loans_cycle = 0;
	$cycle_reloan_payments = 0;

        foreach ($user->loan as $loan) {
            foreach ($loan->transactions as $transaction) {
            //new loans
            if ($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
                $new_loans_cycle += $transaction->debit;
            }

            //reloan payments
            if ($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
            $cycle_reloan_payments += $transaction->debit/0.4;
            }
        }
    }
    $total_loans_cycle = $new_loans_cycle + $cycle_reloan_payments;

    if ($total_loans_cycle < 20000) {
            $filteredLoans[] = [
                'user' => $user,
                'total_loans_cycle' => $total_loans_cycle,
            ];
        }
    }*/
    


        return view('performance_metrics.low_performance', compact('new_loans_cycle', 'cycle_reloan_payments', 'offices', 'officeId', 'loanConsultants', 'branch', 'user_role', 'user_branch', 'user_province', 'dates', 'target_dates', 'compare_dates'));
    }


    public function defaulted(Request $request)
    {
        $user = Sentinel::getUser();
        $user_role = $user->role->role_id;
        $user_branch = $user->office_id;
        $user_province = $user->office->province_id;
        $branch = $request->office_id;
        $offices = Office::all(); 
        $officeId = $request->input('office_id');
    
        
        if (!empty($branch)) {
            $loanConsultants = User::where('office_id', $branch)
                ->whereHas('role', function($query) {
                    $query->where('role_id', 3); 
                })
                ->with(['loan' => function($query) {
                    $query->where('defaulted', 'yes');
                }, 'loan.client'])
                ->get();
        } else {
            $loanConsultants = User::whereHas('role', function($query) {
                $query->where('role_id', 3); 
            })
            ->with(['loan' => function($query) {
                $query->where('defaulted', 'yes');
            }, 'loan.client'])
            ->get();
        }
    
        return view('performance_metrics.defaulted', compact('loanConsultants', 'offices', 'officeId', 'user_role', 'user_branch', 'user_province'));
    }
       
        
}

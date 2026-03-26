<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Repair;
use App\Models\Setting;
use App\Models\Leave;
use App\Models\Loan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use App\Models\Policy;
use App\Models\UserPolicyResponse;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Roles\RoleInterface;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\CycleDates;
use App\Models\LoanTransaction;
use App\Models\Office;
use App\Models\UserRole;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Psy\CodeCleaner\FunctionContextPass;
use App\Models\AppraisalForm;
use App\Models\AppraisalFormSection;
use App\Models\AppraisalQuestion;
use App\Models\AppraisalAnswer;
use App\Models\TargetTracker;
use App\Models\CarryOver;
use App\Models\ClientTransferLog;
use stdClass;
use Carbon\Carbon;
use App\Models\AuditLogs;


class HRController extends Controller{
     public function __construct()
    {

        $this->middleware('sentinel');
    }

    public function employees(Request $request)
    {
        $search = trim($request->get('search'));

         $employees = User::with(['office', 'role'])
        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                // Search by first name
                $q->where('first_name', 'like', "%{$search}%")

                // Search by last name
                ->orWhere('last_name', 'like', "%{$search}%")

                // Search full name (first + last)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])

                // Other fields
                ->orWhere('gender', 'like', "%{$search}%")
                // ->orWhere('employment_status', 'like', "%{$search}%")

                // Office search
                ->orWhereHas('office', function ($officeQuery) use ($search) {
                    $officeQuery->where('name', 'like', "%{$search}%");
                });

                // // Role search
                // ->orWhereHas('role', function ($roleQuery) use ($search) {
                //     $roleQuery->where('name', 'like', "%{$search}%");
                // });

            });

        })
        ->orderBy('first_name')
        ->paginate(12)
        ->appends(['search' => $search]); 
        

        
        
        // keeps search in pagination

        return view('hr.employees',compact('employees','search'));
    }


       public function employee(Request $request,$id)
    {
        $employee = User::with([
            'office',
            // 'role',
            // 'performances',
            // 'payrolls',
            // 'leaves',
            // 'advances'
        ])->findOrFail($id);


      $user = User::findOrFail($id);
    $userId = Sentinel::getUser()->id;
    $cycle_end = $user->cycle_dates
    ? (int) $user->cycle_dates->cycle_end_date
    : 24;
    $today = Carbon::today();

    $buildCycleDate = function (Carbon $month) use ($cycle_end) {
    $month = $month->copy()->startOfMonth();
    $cycleDay = min($cycle_end, $month->daysInMonth);
    return $month->day($cycleDay)->addDay();
};

    $cycleDate = $buildCycleDate(Carbon::now());
    if ($today->lt($cycleDate)) {
    $cycleDate = $buildCycleDate(Carbon::now()->subMonth());
}

$cycle_date = $cycleDate->format('Y-m-d');
$true_date = $cycle_date;


$cycle_close_date = Carbon::parse($cycle_date)
    ->addMonthNoOverflow()
    ->subDay()
    ->format('Y-m-d');

                $fixedDay = $cycle_end;
$userId = Sentinel::getUser()->id;

// Convert cycle_date/close_date to Carbon
$cycleStart = Carbon::parse($cycle_date);
$cycleEnd = Carbon::parse($cycle_close_date);

// ORIGINAL
$start = $cycleStart->copy()
    ->format('Y-m-d');

$end = $cycleEnd->copy()
    ->day(min($fixedDay, $cycleEnd->daysInMonth))
    ->format('Y-m-d');


$startMonth = $request->input('start_month');
$endMonth   = $request->input('end_month');

if ($startMonth) {
    $start = Carbon::parse($startMonth)
        ->day(min($fixedDay, Carbon::parse($startMonth)->daysInMonth))
        ->addDay()
        ->format('Y-m-d');
}

if ($endMonth) {
    $end = Carbon::parse($endMonth)
        ->day(min($fixedDay, Carbon::parse($endMonth)->daysInMonth))
        ->format('Y-m-d');
}


// Build query
$query = http_build_query([
    'user_id' => $id,
    'start_date' => $start,
    'end_date' => $end,
]);

$url = "https://lms2backend.whencefinancesystem.com/my-performance-new?$query";

$json = @file_get_contents($url);

$data = [];

if ($json !== false) {
    $decoded = json_decode($json, true);
    $data = is_array($decoded) ? $decoded : [];
}

        return view('hr.employee', compact('employee','data','start','end','data','userId'));
    }


}
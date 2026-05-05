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
use Illuminate\Support\Facades\Http;
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
use App\Models\PayrollTemplateMeta;
use App\Models\AdministrativeRecord;


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

        $role = Sentinel::getUser()->roles->first();
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


 $currentYear = Carbon::now()->year;
    $selectedLeaveYear = (int) $request->get('leave_year', $currentYear);

    $startOfYear = Carbon::create($selectedLeaveYear, 1, 1)->startOfDay();
    $endOfYear = Carbon::create($selectedLeaveYear, 12, 31)->endOfDay();

    $employeeLeaves = DB::table('leave_days')
        ->where('user_id', $employee->id)
        ->where(function ($query) use ($startOfYear, $endOfYear) {
            $query->whereBetween('commencement_date', [$startOfYear, $endOfYear])
                ->orWhereBetween('return_date', [$startOfYear, $endOfYear])
                ->orWhere(function ($q) use ($startOfYear, $endOfYear) {
                    $q->where('commencement_date', '<=', $startOfYear)
                      ->where('return_date', '>=', $endOfYear);
                });
        })
        ->orderBy('commencement_date', 'desc')
        ->get();

    $employeeLeaves = $employeeLeaves->map(function ($leave) use ($selectedLeaveYear) {
        $leave->days_taken = $this->countLeaveDaysInYear($leave, $selectedLeaveYear);
        return $leave;
    });

    $leaveYears = DB::table('leave_days')
        ->where('user_id', $employee->id)
        ->selectRaw('YEAR(commencement_date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    if (!$leaveYears->contains($currentYear)) {
        $leaveYears->prepend($currentYear);
    }

    $selectedAdvanceYear = (int) $request->get('advance_year', $currentYear);

    $employeeAdvances = Advance::where('user_id', $employee->id)
        ->whereYear('date_requested', $selectedAdvanceYear)
        ->orderBy('date_requested', 'desc')
        ->get()
        ->map(function ($advance) {
            if ((float) $advance->remaining_amount <= 0 && $advance->status === 'closed') {
                $advance->payment_status = 'Paid Back';
            } elseif ((float) $advance->amount_paid > 0 && (float) $advance->remaining_amount > 0) {
                $advance->payment_status = 'Partially Paid';
            } else {
                $advance->payment_status = 'Not Paid Back';
            }

            return $advance;
        });

    $advanceYears = Advance::where('user_id', $employee->id)
        ->selectRaw('YEAR(date_requested) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    if (!$advanceYears->contains($currentYear)) {
        $advanceYears->prepend($currentYear);
    }

     $role = Sentinel::getUser()->roles->first();


      $employeePayrolls = Payroll::where('user_id', $employee->id)->get();
      $payroll_fields = PayrollTemplateMeta::all();

      // Administrative Records
      $employeeDisciplinaryRecords = AdministrativeRecord::where('employee_id', $employee->id)
          ->where('record_type', 'disciplinary')
          ->with('creator')
          ->orderBy('created_at', 'desc')
          ->get();

      $employeeHealthRecords = AdministrativeRecord::where('employee_id', $employee->id)
          ->where('record_type', 'health')
          ->with('creator')
          ->orderBy('created_at', 'desc')
          ->get();

      $employeeCareerRecords = AdministrativeRecord::where('employee_id', $employee->id)
          ->where('record_type', 'career')
          ->with('creator')
          ->orderBy('recording_date', 'desc')
          ->get();

            return view('hr.employee', compact('employee','data','start','end','data','userId','employeeLeaves',
        'leaveYears',
        'selectedLeaveYear',
      'employeeAdvances',
        'advanceYears',
        'selectedAdvanceYear','employeePayrolls','payroll_fields',
        'employeeDisciplinaryRecords', 'employeeHealthRecords', 'employeeCareerRecords'));

    }

    public function storeAdministrativeRecord(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'record_type' => 'required|in:disciplinary,health,career',
            'disciplinary_type' => 'nullable|string',
            'warning_type' => 'nullable|string',
            'warning_level' => 'nullable|string',
            'health_type' => 'nullable|string',
            'incident_type' => 'nullable|string',
            'career_type' => 'nullable|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'recording_date' => 'nullable|date',
            'comments' => 'nullable|string',
            'number_of_days' => 'nullable|integer|min:1',
            'absence_dates' => 'nullable|array',
            'absence_dates.*' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['status'] = 'pending';
        $data['created_by'] = Sentinel::getUser()->id;

        AdministrativeRecord::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Administrative record created successfully.'
        ]);
    }

    public function administrativeRecords(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        $records = AdministrativeRecord::with(['employee', 'creator', 'approver'])
            ->when($tab === 'pending', function($query) {
                return $query->where('status', 'pending');
            })
            ->when($tab === 'active', function($query) {
                return $query->where('status', 'active');
            })
            ->when($tab === 'declined', function($query) {
                return $query->where('status', 'declined');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $recordTypeStats = AdministrativeRecord::selectRaw(
                'record_type, count(*) as total, '
                . 'sum(case when status = "pending" then 1 else 0 end) as pending, '
                . 'sum(case when status = "active" then 1 else 0 end) as active, '
                . 'sum(case when status = "declined" then 1 else 0 end) as declined'
            )
            ->groupBy('record_type')
            ->get()
            ->keyBy('record_type')
            ->map(function ($row) {
                return [
                    'total' => (int) $row->total,
                    'pending' => (int) $row->pending,
                    'active' => (int) $row->active,
                    'declined' => (int) $row->declined,
                ];
            })->toArray();

        $statusCounts = AdministrativeRecord::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('hr.administrative-records', compact('records', 'tab', 'recordTypeStats', 'statusCounts'));
    }

    public function administrativeRecordsData(Request $request)
    {
        $status = $request->get('status', 'pending');
        if (! in_array($status, ['pending', 'active', 'declined'])) {
            return response()->json(['message' => 'Invalid status'], 422);
        }

        $records = AdministrativeRecord::with(['employee', 'creator', 'approver'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $records->map(function ($record) {
            return [
                'id' => $record->id,
                'record_type' => $record->record_type,
                'disciplinary_type' => $record->disciplinary_type,
                'warning_type' => $record->warning_type,
                'warning_level' => $record->warning_level,
                'health_type' => $record->health_type,
                'incident_type' => $record->incident_type,
                'career_type' => $record->career_type,
                'name' => $record->name,
                'description' => $record->description,
                'recording_date' => optional($record->recording_date)->format('d M Y'),
                'comments' => $record->comments,
                'number_of_days' => $record->number_of_days,
                'absence_dates' => $record->absence_dates,
                'status' => $record->status,
                'created_at' => $record->created_at->format('d M Y'),
                'employee' => [
                    'full_name' => $record->employee->first_name . ' ' . $record->employee->last_name,
                    'employee_number' => $record->employee->employee_number ?? 'No ID',
                ],
                'creator_name' => $record->creator ? $record->creator->first_name . ' ' . $record->creator->last_name : 'Unknown',
                'approver_name' => $record->approver ? $record->approver->first_name . ' ' . $record->approver->last_name : 'Unknown',
                'approved_at' => optional($record->approved_at)->format('d M Y'),
                'decline_reason' => $record->decline_reason,
            ];
        });

        return response()->json([
            'records' => $response,
            'total' => $records->count(),
        ]);
    }

    public function approveRecord(Request $request, $id)
    {
        $record = AdministrativeRecord::findOrFail($id);

        $record->update([
            'status' => 'active',
            'approved_by' => Sentinel::getUser()->id,
            'approved_at' => now(),
        ]);

        Flash::success('Administrative record approved successfully.');
        return redirect()->back();
    }

    public function declineRecord(Request $request, $id)
    {
        $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);

        $record = AdministrativeRecord::findOrFail($id);

        $record->update([
            'status' => 'declined',
            'approved_by' => Sentinel::getUser()->id,
            'approved_at' => now(),
            'decline_reason' => $request->decline_reason,
        ]);

        Flash::success('Administrative record declined.');
        return redirect()->back();
    }


    private function countLeaveDaysInYear($leave, $selectedYear)
{
    $commencementDate = Carbon::parse($leave->commencement_date);
    $returnDate = Carbon::parse($leave->return_date);

    $startOfYear = Carbon::create($selectedYear, 1, 1)->startOfDay();
    $endOfYear = Carbon::create($selectedYear, 12, 31)->endOfDay();

    if ($commencementDate->lt($startOfYear)) {
        $commencementDate = $startOfYear->copy();
    }

    if ($returnDate->gt($endOfYear)) {
        $returnDate = $endOfYear->copy()->addDay();
    }

    $totalDays = 0;

    for ($date = $commencementDate->copy(); $date->lt($returnDate); $date->addDay()) {
        if (!$date->isWeekend() && !$this->isPublicHoliday($date->toDateString())) {
            $totalDays++;
        }
    }

    return $totalDays;
}


  //public holidys
    private function getZambianPublicHolidays($year)
    {
        $easterDate = $this->calculateEaster($year);
        $goodFriday = clone $easterDate;
        $goodFriday->modify('-2 days');
        $easterMonday = clone $easterDate;
        $easterMonday->modify('+1 day');

        return [
            "$year-01-01" => "New Year's Day",
            "$year-03-08" => "International Women's Day",
            "$year-03-12" => "Youth Day",
            $goodFriday->format('Y-m-d') => "Good Friday",
            $easterMonday->format('Y-m-d') => "Easter Monday",
            "$year-04-28" => "Kenneth Kaunda Day",
            "$year-05-01" => "Labour Day",
            "$year-05-25" => "Africa Freedom Day",
            $this->getFirstMondayOfJuly($year)->format('Y-m-d') => "Heroes Day",
            (clone $this->getFirstMondayOfJuly($year))->modify('+1 day')->format('Y-m-d') => "Unity Day",
            "$year-08-05" => "Farmer's Day",
            "$year-10-18" => "Prayer Day",
            "$year-10-24" => "Independence Day",
            "$year-12-25" => "Christmas Day",
        ];
    }

    private function isPublicHoliday($date)
    {
        $year = substr($date, 0, 4);
        $holidays = $this->getZambianPublicHolidays($year);
        return isset($holidays[$date]) ? $holidays[$date] : null;
    }

    private function calculateEaster($year)
    {
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);
        return $base->add(new \DateInterval("P{$days}D"));
    }

    private function getFirstMondayOfJuly($year)
    {
        return new \DateTime("first monday of july $year");
    }


    public function workforce_analytics()
    {
        $baseUrl = 'https://lms2backend.whencefinancesystem.com'; // e.g. http://localhost:3000

        // Fetch data from external API
        $diversity = Http::get($baseUrl . '/diversity-and-inclusion')->json();
        $tenure = Http::get($baseUrl . '/tenure-and-stability')->json();
        $offices = Http::get($baseUrl . '/office-workforce-insights')->json();

            return view('hr.workforce_analytics', [
            'diversity' => $diversity,
            'tenure' => $tenure,
            'offices' => $offices
        ]);
    }

}
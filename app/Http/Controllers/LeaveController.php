<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use DateTime;
use DateInterval;
use Laracasts\Flash\Flash;
use App\Models\Office;
use App\Helpers\GeneralHelper;
use App\Models\Leave;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;


class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

public function myLeavedays(Request $request)
{
    $user = Sentinel::getUser();

    $currentYear = Carbon::now()->year;
    $selectedYear = $request->get('year', $currentYear);

    $startOfYear = Carbon::create($selectedYear, 1, 1)->startOfDay();
    $endOfYear = Carbon::create($selectedYear, 12, 31)->endOfDay();

    $approvedLeaves = \DB::table('leave_days')
        ->where('user_id', $user->id)
        ->where('status', 'approved')
        ->where(function ($query) use ($startOfYear, $endOfYear) {
            $query->whereBetween('commencement_date', [$startOfYear, $endOfYear])
                  ->orWhereBetween('return_date', [$startOfYear, $endOfYear])
                  ->orWhere(function ($q) use ($startOfYear, $endOfYear) {
                      $q->where('commencement_date', '<=', $startOfYear)
                        ->where('return_date', '>=', $endOfYear);
                  });
        })
        ->select('id', 'commencement_date', 'return_date', 'reason')
        ->get();

    $leaveSummary = $approvedLeaves->groupBy('reason')->map(function ($leaves) use ($selectedYear) {
        return $leaves->sum(function ($leave) use ($selectedYear) {
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
        });
    });

    $years = \DB::table('leave_days')
        ->where('user_id', $user->id)
        ->where('status', 'approved')
        ->selectRaw('YEAR(commencement_date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    if (!$years->contains($currentYear)) {
        $years->prepend($currentYear);
    }

    $calendarHtml = $this->generateCalendar(
        $request->get('y', $selectedYear),
        $request->get('m'),
        $approvedLeaves
    );

    return view('leave.my_leave_days', compact(
        'calendarHtml',
        'leaveSummary',
        'years',
        'selectedYear',
        'currentYear'
    ));
}


    private function generateCalendar($year, $month, $approvedLeaves)
    {
        $year = isset($_GET['y']) ? $_GET['y'] : date('Y');
        $month = isset($_GET['m']) ? $_GET['m'] : date('m');

        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];

        $prevMonth = sprintf('%02d', ($month - 1 <= 0 ? 12 : $month - 1));
        $prevYear = ($month - 1 <= 0 ? $year - 1 : $year);

        $nextMonth = sprintf('%02d', ($month + 1 > 12 ? 1 : $month + 1));
        $nextYear = ($month + 1 > 12 ? $year + 1 : $year);

        $calendarHtml = '<div style="margin-bottom: 20px; text-align: center;">';
        $calendarHtml .= '<div class="btn-group" aria-label="Month Navigation">';

        foreach ($months as $key => $val) {
            $calendarHtml .= '<a href="?y=' . $year . '&m=' . $key . '" class="btn btn-primary" style="margin: 0px; font-size: 1.2em; color: #FFFFFF;">' . $val . '</a>';
        }

        $calendarHtml .= '</div>';
        $calendarHtml .= '</div>';
        $calendarHtml .= '<div style="position: relative; text-align: center; margin-bottom: 20px;">';
        //$calendarHtml .= '<a href="?y=' . $prevYear . '&m=' . $prevMonth . '" style="position: absolute; left: 250px; top: 50%; transform: translateY(-50%); font-size: 1.0em; text-decoration: none;">&lt;</a>';
        $calendarHtml .= '<span style="font-size: 1.5em; font-weight: bold;">' . $months[$month] . ' ' . $year . '</span>';
        //$calendarHtml .= '<a href="?y=' . $nextYear . '&m=' . $nextMonth . '" style="position: absolute; right: 250px; top: 50%; transform: translateY(-50%); font-size: 1.0em; text-decoration: none;">&gt;</a>';
        $calendarHtml .= '</div>';
        $calendarHtml .= '<table style="width: 100%; border-collapse: collapse;">';
        $calendarHtml .= '<thead><tr>';

        foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day) {
            $calendarHtml .= '<th style="padding: 10px; text-align: center; border: 1px solid #ccc; background-color: #FFB346;">' . $day . '</th>';
        }

        $calendarHtml .= '</tr></thead><tbody>';
        $firstDayOfMonth = date('N', strtotime($year . '-' . $month . '-01'));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dayOfMonth = 1;
        //row
        $calendarHtml .= '<tr>';

        //empty cells for days before the 1st of the month
        for ($i = 1; $i < $firstDayOfMonth; $i++) {
            $calendarHtml .= '<td style="padding: 10px; text-align: center; border: 1px solid #ccc;"></td>';
        }

        //days
        while ($dayOfMonth <= $daysInMonth) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $dayOfMonth);
            $isLeaveDay = $this->isApprovedLeaveDay($currentDate, $approvedLeaves);
            $isHoliday = $this->isPublicHoliday($currentDate);
            $isWeekend = date('N', strtotime($currentDate)) >= 6; // Check if it's Saturday (6) or Sunday (7)
            $cellStyle = '';
            $textStyle = '';

            if ($isLeaveDay && !$isHoliday && !$isWeekend) {
                $cellStyle = 'background-color: #c0e8d2;';
            } elseif ($isHoliday) {
                $textStyle = 'color: red;';
            }

            $link = $isLeaveDay ? route('leave.show', ['id' => $isLeaveDay->id]) : '#';

            $calendarHtml .= '<td style="padding: 10px; text-align: center; border: 1px solid #ccc; ' . $cellStyle . '">';
            if ($isLeaveDay && !$isHoliday && !$isWeekend) {
                $calendarHtml .= '<a href="' . $link . '" style="display: block; width: 100%; height: 100%; ' . $textStyle . '">' . $dayOfMonth . '</a>';
            } else {
                $calendarHtml .= '<span style="' . $textStyle . '">' . $dayOfMonth . '</span>';
            }

            $calendarHtml .= '</td>';
            $dayOfMonth++;

            //new month 
            if (($firstDayOfMonth + $dayOfMonth - 1) % 7 == 1) {
                $calendarHtml .= '</tr><tr>';
            }
        }

        //empty cells at the end of the month
        while (($firstDayOfMonth + $daysInMonth) % 7 != 0) {
            $calendarHtml .= '<td style="padding: 10px; text-align: center; border: 1px solid #ccc;"></td>';
            $daysInMonth++;
        }

        $calendarHtml .= '</tbody></table>';

        return $calendarHtml;
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

    private function isApprovedLeaveDay($date, $approvedLeaves)
    {
        foreach ($approvedLeaves as $leave) {
            if ($date >= $leave->commencement_date && $date < $leave->return_date) {
                return $leave;
            }
        }
        return false;
    }

    public function applyForLeave()
    {
        $user = Sentinel::getUser();
        $firstName = $user->first_name;
        $lastName = $user->last_name;

        if ($user->inRole(1)) {
            $offices = Office::all();
        } elseif ($user->inRole(6)) {
            $offices = Office::where('province_id', $user->province_id)->get();
        } elseif ($user->inRole(4)) {
            $offices = Office::where('id', $user->office_id)->get();
        } else {
            $offices = Office::all();
        }

        return view('leave.apply', compact('firstName', 'lastName', 'offices'));
    }

    public function submitLeave(Request $request)
    {
        $validatedData = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'reason' => 'required|string',
            'commencement_date' => 'required|date',
            'return_date' => 'required|date|after:commencement_date',
        ]);

        $user = Sentinel::getUser();

        $leave = new Leave();
        $leave->user_id = Sentinel::getUser()->id;
        $leave->office_id = $request->office_id;
        $leave->first_name = $request->first_name;
        $leave->last_name = $request->last_name;
        $leave->department = $request->department;
        $leave->position = $request->position;
        $leave->reason = $request->reason;
        $leave->notes = $request->notes;
        $leave->commencement_date = $request->commencement_date;
        $leave->return_date = $request->return_date;
        $leave->date_requested = now();
        $leave->save();

        GeneralHelper::audit_trail("Create", "Leeave", $leave->id);
        Flash::success("Leave Application submitted successfully");
        return Redirect::route('leave.my_leave_days');
    }

    public function showPendingApprovals()
    {
        $user = Sentinel::getUser();
        $query = Leave::where('status', 'pending');

        if ($user->inRole(1)) {
            // Admin sees all
        } elseif ($user->inRole(6)) {
            $query->whereIn('office_id', function ($q) use ($user) {
                $q->select('id')->from('offices')->where('province_id', $user->province_id);
            });
        } elseif ($user->inRole(4)) {
            $query->where('office_id', $user->office_id);
        } else {
            // Default behavior
        }

        $leave = $query->get();
        return view('leave.pending_leave_approvals', compact('leave'));
    }

    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'approved';
        $leave->date_approved = now();
        $leave->approved_by_id = Sentinel::getUser()->id;
        $leave->save();

        return Redirect::route('leave.pending_leave_approvals')->with('success', trans('general.successfully_saved'));
    }


    public function decline(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'declined';
        $leave->declined_by_id = Sentinel::getUser()->id;
        $leave->save();
        $request->session()->flash('success', 'Leave Application declined successfully.');
        return redirect()->back();
    }

    public function showActiveLeave()
    {
        $user = Sentinel::getUser();
        $currentDate = date('Y-m-d');

        $query = Leave::where('status', 'approved')
            ->where('commencement_date', '<=', $currentDate)
            ->where('return_date', '>=', $currentDate);

        // if ($user->inRole(1)) {
        //     // Admin sees all
        // } elseif ($user->inRole(6)) {
            
        //     $query->where('office_id', $user->office_id);
        //     // $query->whereIn('office_id', function ($q) use ($user) {
        //     //     $q->select('id')->from('offices')->where('province_id', $user->province_id);
        //     // });
        // } elseif ($user->inRole(4)) {
        //     $query->where('office_id', $user->office_id);
        // } else {
            
        //     $query->where('office_id', $user->office_id);
        //     // Default to showing nothing or all? preserving "all" for now as per other controllers being default-permissive if not caught
        //     // But technically safer to restricting. I will show ALL to avoid regression for other roles not mentioned.
        // }

        $leave = $query->get();
        return view('leave.active_leave', compact('leave'));
    }

    public function showDetails(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $user = $leave->user;
        $startDate = Carbon::create(2025, 1, 1);

        $approvedLeaves = \DB::table('leave_days')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate) {
                $query->whereDate('commencement_date', '>=', $startDate)
                    ->orWhereDate('return_date', '>=', $startDate);
            })
            ->select('id', 'commencement_date', 'return_date', 'reason')
            ->get();
        $userLeaveRecords = Leave::where('user_id', $leave->user_id)
            ->where(function ($query) use ($startDate) {
                $query->whereDate('commencement_date', '>=', $startDate)
                    ->orWhereDate('return_date', '>=', $startDate);
            })
            ->get();

        $leaveReasons = $approvedLeaves->groupBy('reason')->map(function ($leaves) {
            return $leaves->sum(function ($leave) {
                $commencementDate = Carbon::parse($leave->commencement_date);
                $returnDate = Carbon::parse($leave->return_date);

                $totalDays = 0;
                for ($date = $commencementDate; $date->lt($returnDate); $date->addDay()) {
                    if (!$date->isWeekend() && !$this->isPublicHoliday($date->toDateString())) {
                        $totalDays++;
                    }
                }
                return $totalDays;
            });
        });

        $calendarHtml = $this->generateCalendar($request->get('y'), $request->get('m'), $approvedLeaves);
        return view('leave.show', compact('leave', 'userLeaveRecords', 'leaveReasons', 'calendarHtml'));
    }

    public function showDeclinedLeave()
    {
        $user = Sentinel::getUser();
        $query = Leave::where('status', 'declined');

        if ($user->inRole(1)) {
            // Admin sees all
        } elseif ($user->inRole(6)) {
            $query->whereIn('office_id', function ($q) use ($user) {
                $q->select('id')->from('offices')->where('province_id', $user->province_id);
            });
        } elseif ($user->inRole(4)) {
            $query->where('office_id', $user->office_id);
        } else {
            // Default behavior
        }

        $leave = $query->get();
        return view('leave.declined_leave', compact('leave'));
    }

}


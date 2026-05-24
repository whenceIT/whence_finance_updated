<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\FleetMaintenanceSchedule;
use App\Models\Office;
use App\Models\Position;
use App\Models\User;
use App\Models\Department;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GOAController extends Controller
{
    /**
     * Display the GOA Dashboard overview page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fleet statistics
        $totalVehicles = Fleet::count();
        $activeVehicles = Fleet::where('vehicle_status', 'Active')->count();
        $maintenanceVehicles = Fleet::where('vehicle_status', 'Maintenance')->count();
        $outOfServiceVehicles = Fleet::where('vehicle_status', 'Out of Service')->count();
        $utilization = $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0;

        // Insurance statistics
        $insuranceExpired = Fleet::where('insurance_expire_date', '<', Carbon::now())->count();
        $insuranceUpToDate = Fleet::where('insurance_expire_date', '>=', Carbon::now())->count();

        // Alerts
        $insuranceExpiredRecent = Fleet::with('user', 'office')->where('insurance_expire_date', '<', Carbon::now())->where('insurance_expire_date', '>=', Carbon::now()->subWeek())->orderBy('insurance_expire_date')->get();
        $insuranceExpiringSoon = Fleet::with('user', 'office')->whereBetween('insurance_expire_date', [Carbon::now(), Carbon::now()->addWeek()])->orderBy('insurance_expire_date')->get();
        $maintenanceSoon = FleetMaintenanceSchedule::with('fleet.user', 'fleet.office')->whereBetween('due_date', [Carbon::now(), Carbon::now()->addDays(5)])->where('status', 'pending')->orderBy('due_date')->get();
        $insurancePastDue = Fleet::with('user', 'office')->where('insurance_expire_date', '<', Carbon::now()->subWeek())->orderBy('insurance_expire_date')->get();
        $maintenancePastDue = FleetMaintenanceSchedule::with('fleet.user', 'fleet.office')->where('due_date', '<', Carbon::now())->where('status', 'pending')->orderBy('due_date')->get();

        // Average vehicle age
        $avgVehicleAge = Fleet::whereNotNull('date_purchased')
            ->selectRaw('AVG(DATEDIFF(CURDATE(), date_purchased)) / 365 as avg_age')
            ->first()->avg_age ?? 0;
        $avgVehicleAge = round($avgVehicleAge, 1);

        // Positions statistics
        $totalPositions = Position::count();
        $filledPositions = Position::where('is_vacant', 0)->count();
        $vacantPositions = Position::where('is_vacant', 1)->count();
        $inProcessPositions = Position::where('status', 'In Review')->count();
        $fillRate = $totalPositions > 0 ? round(($filledPositions / $totalPositions) * 100) : 0;

        // Maintenance statistics
        $scheduledMaintenance = FleetMaintenanceSchedule::where('status', 'pending')->count();
        $overdueMaintenance = FleetMaintenanceSchedule::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())->count();
        $thisMonthMaintenance = FleetMaintenanceSchedule::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $monthlyMaintenanceCost = FleetMaintenanceSchedule::where('status', 'completed')->sum('amount');

        return view('goa.index', compact(
            'totalVehicles', 'activeVehicles', 'maintenanceVehicles', 'outOfServiceVehicles', 'utilization',
            'avgVehicleAge', 'totalPositions', 'filledPositions', 'vacantPositions', 'inProcessPositions', 'fillRate',
            'scheduledMaintenance', 'overdueMaintenance', 'thisMonthMaintenance', 'insuranceExpired', 'insuranceUpToDate',
            'insuranceExpiredRecent', 'insuranceExpiringSoon', 'maintenanceSoon', 'insurancePastDue', 'maintenancePastDue', 'monthlyMaintenanceCost'
        ));
    }

    /**
     * Display the fleet management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function fleetManagement()
    {
        $fleets = Fleet::with('office')->latest()->paginate(15);
        $totalValue = Fleet::sum('current_value');
        $offices = Office::where('active', 1)->orderBy('name')->get();
        $users = User::whereNull('deleted_at')->orderBy('first_name')->get();
        $departments = Department::where('active', 1)->orderBy('name')->get();

        // Fleet statistics
        $totalFleets = Fleet::with('office', 'user')->get();
        $activeFleets = Fleet::with('office', 'user')->where('vehicle_status', 'Active')->get();
        $maintenanceFleets = Fleet::with('office', 'user')->where('vehicle_status', 'Maintenance')->get();
        $outOfServiceFleets = Fleet::with('office', 'user')->where('vehicle_status', 'Out of Service')->get();
        $totalVehicles = $totalFleets->count();
        $activeVehicles = $activeFleets->count();
        $maintenanceVehicles = $maintenanceFleets->count();
        $outOfServiceVehicles = $outOfServiceFleets->count();

        $maintenanceSchedules = FleetMaintenanceSchedule::with('fleet')->where('status', 'pending')->orderBy('due_date')->get();

        return view('goa.fleet-management', compact('fleets', 'offices', 'users', 'totalVehicles', 'activeVehicles', 'maintenanceVehicles', 'outOfServiceVehicles', 'maintenanceSchedules', 'totalFleets', 'activeFleets', 'maintenanceFleets', 'outOfServiceFleets', 'totalValue'));
    }

    /**
     * Display the vacancies and staffing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function vacanciesAndStaffing()
    {
        $positions = Position::where('is_vacant', 1)->get();
        $departments = Department::orderBy('name')->get();
        $vacancies = Vacancy::with(['position.department', 'office'])->get();
        $offices = Office::where('active', 1)->orderBy('name')->get();

        // Staffing statistics
        $totalPositions = Position::count();
        $filledPositions = Position::where('is_vacant', 0)->count();
        $vacantPositions = $vacancies->count();
        $inProcessPositions = Position::where('status', 'In Review')->count();

        // Department stats
        foreach($departments as $dept) {
            $dept->total_positions = Position::where('department_id', $dept->id)->count();
            $dept->filled_positions = Position::where('department_id', $dept->id)->where('is_vacant', 0)->count();
        }

        // Recent hires (users with positions updated_at)
        $recentHires = User::with('position.department')->whereNotNull('position_id')->orderBy('updated_at', 'desc')->limit(10)->get();

        return view('goa.vacancies-and-staffing', compact('positions', 'departments', 'vacancies', 'offices', 'totalPositions', 'filledPositions', 'vacantPositions', 'inProcessPositions', 'recentHires'));
    }

    public function removePosition($id)
    {
        $position = Position::findOrFail($id);
        $position->update(['is_vacant' => 0, 'num_of_vacancies' => 0]);
        return redirect()->back()->with('success', 'Position removed successfully.');
    }

    public function fillPosition($id)
    {
        $position = Position::findOrFail($id);
        $position->update(['is_vacant' => 0, 'num_of_vacancies' => 0]);
        return redirect()->back()->with('success', 'Position filled successfully.');
    }

    public function showPosition($id)
    {
        $position = Position::findOrFail($id);
        return response()->json($position);
    }
}
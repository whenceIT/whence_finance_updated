<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\FleetMaintenanceSchedule;
use App\Models\Office;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class GOAController extends Controller
{
    /**
     * Display the GOA Dashboard overview page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('goa.index');
    }

    /**
     * Display the fleet management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function fleetManagement()
    {
        $fleets = Fleet::with('office')->latest()->paginate(15);
        $offices = Office::where('active', 1)->orderBy('name')->get();
        $users = User::whereNull('deleted_at')->orderBy('first_name')->get();

        // Fleet statistics
        $totalVehicles = Fleet::count();
        $activeVehicles = Fleet::where('vehicle_status', 'Active')->count();
        $maintenanceVehicles = Fleet::where('vehicle_status', 'Maintenance')->count();
        $outOfServiceVehicles = Fleet::where('vehicle_status', 'Out of Service')->count();

        $maintenanceSchedules = FleetMaintenanceSchedule::with('fleet')->where('status', 'pending')->orderBy('due_date')->get();

        return view('goa.fleet-management', compact('fleets', 'offices', 'users', 'totalVehicles', 'activeVehicles', 'maintenanceVehicles', 'outOfServiceVehicles', 'maintenanceSchedules'));
    }

    /**
     * Display the vacancies and staffing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function vacanciesAndStaffing()
    {
        $positions = Position::where('is_vacant', 1)->get();
        return view('goa.vacancies-and-staffing', compact('positions'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\FleetMaintenanceSchedule;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
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

    public function create()
    {
        return view('goa.fleet-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'nullable|string|max:100|unique:fleets,vehicle_id',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'assigned_to' => 'nullable|string|max:150',
            'office_id' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'date_purchased' => 'nullable|date',
            'insurance_expire_date' => 'nullable|date',
            'current_value' => 'nullable|numeric|min:0',
            'white_book' => 'required|in:available,none',
            'vehicle_status' => 'nullable|string|max:50',
            'last_maintenance' => 'nullable|date',
        ]);

        if (empty($data['vehicle_id'])) {
            do {
                $num = mt_rand(10000, 999999);
                $data['vehicle_id'] = $num;
            } while (Fleet::where('vehicle_id', $data['vehicle_id'])->exists());
        }

        Fleet::create($data);

        return redirect()->back()->with('success', 'Fleet record created successfully.');
    }

    public function storeMaintenance(Request $request)
    {
        try {

        $data = $request->validate([
            'maintenanceVehicleId' => 'required|string',
            'maintenanceType' => 'required|string',
            'maintenanceTechnician' => 'nullable|string',
            'maintenanceDueDate' => 'nullable|date',
            'maintenanceNotes' => 'nullable|string',
        ]);

        $fleet = Fleet::where('vehicle_id', $data['maintenanceVehicleId'])->first();

        if ($fleet) {
            FleetMaintenanceSchedule::create([
                'fleet_id' => $fleet->id,
                'maintenance_type' => $data['maintenanceType'],
                'technician' => $data['maintenanceTechnician'],
                'due_date' => $data['maintenanceDueDate'],
                'notes' => $data['maintenanceNotes'],
            ]);

            return redirect()->back()->with('success', 'Maintenance scheduled successfully.');
        } else {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }
        } catch (\Throwable $th) {
             return redirect()->back()->with('error', 'An error occurred while scheduling maintenance.');
        }
    }

    public function completeMaintenance(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $schedule = FleetMaintenanceSchedule::findOrFail($id);
        $schedule->update([
            'status' => 'completed',
            'amount' => $data['amount'],
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Maintenance marked as completed.');
    }

    public function show(Fleet $fleet)
    {
        return view('goa.fleet-show', compact('fleet'));
    }

    public function edit(Fleet $fleet)
    {
        $offices = Office::where('active', 1)->orderBy('name')->get();
        $users = User::whereNull('deleted_at')->orderBy('first_name')->get();
        return view('goa.fleet-edit', compact('fleet', 'offices', 'users'));
    }

    public function update(Request $request, Fleet $fleet)
    {
        $data = $request->validate([
            'vehicle_id' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'assigned_to' => 'nullable|string|max:150',
            'office_id' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'current_value' => 'nullable|numeric|min:0',
            'white_book' => 'required|in:available,none',
            'vehicle_status' => 'nullable|string|max:50',
        ]);

        $fleet->update($data);

        return redirect()->route('goa.fleet-management')->with('success', 'Fleet record updated successfully.');
    }

    public function destroy(Fleet $fleet)
    {
        $fleet->delete();

        return redirect()->back()->with('success', 'Fleet record deleted successfully.');
    }
}

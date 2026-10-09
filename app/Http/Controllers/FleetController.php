<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\FleetAccident;
use App\Models\FleetServiceRecord;
use App\Models\FleetExpense;
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
        $fleet->load([
            'office',
            'user',
            'accidents',
            'serviceRecords',
            'expenses',
            'maintenanceSchedules',
        ]);

        // Cost summary grouped by expense category
        $expenseSummary = $fleet->expenses
            ->groupBy('category')
            ->map(fn($group) => $group->sum('cost'));

        $accidentTotal      = $fleet->accidents->sum('cost');
        $serviceTotal       = $fleet->serviceRecords->sum('cost');
        $totalExpenditure   = $fleet->expenses->sum('amount')
                            + $accidentTotal
                            + $serviceTotal;

        return view('goa.fleet-show', compact(
            'fleet',
            'expenseSummary',
            'accidentTotal',
            'serviceTotal',
            'totalExpenditure'
        ));
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

    /**
     * Quick update for insurance expiry date only (used from fleet-show page modal).
     */
    public function updateInsurance(Request $request, Fleet $fleet)
    {
        $data = $request->validate([
            'insurance_expire_date' => 'required|date|after_or_equal:today',
        ]);

        $fleet->update([
            'insurance_expire_date' => $data['insurance_expire_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insurance expiry date updated successfully.',
            'new_date'     => $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A',
            'new_date_iso' => $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('Y-m-d') : null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  ACCIDENT HISTORY
    // ──────────────────────────────────────────────────────────────

    public function storeAccident(Request $request, Fleet $fleet)
    {
        $data = $request->validate([
            'accident_date'          => 'required|date',
            'driver'                 => 'nullable|string|max:150',
            'location'               => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'police_report_number'   => 'nullable|string|max:100',
            'insurance_claim_number' => 'nullable|string|max:100',
            'repair_details'         => 'nullable|string',
            'cost'                   => 'nullable|numeric|min:0',
            'document'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store("fleet/{$fleet->id}/accidents", 'public');
        }

        $fleet->accidents()->create([
            'accident_date'          => $data['accident_date'],
            'driver'                 => $data['driver'] ?? null,
            'location'               => $data['location'] ?? null,
            'description'            => $data['description'] ?? null,
            'police_report_number'   => $data['police_report_number'] ?? null,
            'insurance_claim_number' => $data['insurance_claim_number'] ?? null,
            'repair_details'         => $data['repair_details'] ?? null,
            'cost'                   => $data['cost'] ?? 0,
            'document_path'          => $path,
            'recorded_by'            => auth()->user()->first_name . ' ' . auth()->user()->last_name ?? 'System',
        ]);

        return redirect()->back()->with('success', 'Accident record added successfully.')->withFragment('tab-accidents');
    }

    public function destroyAccident(Fleet $fleet, FleetAccident $accident)
    {
        $accident->delete();
        return redirect()->back()->with('success', 'Accident record deleted.')->withFragment('tab-accidents');
    }

    // ──────────────────────────────────────────────────────────────
    //  MAINTENANCE & REPAIR HISTORY
    // ──────────────────────────────────────────────────────────────

    public function storeServiceRecord(Request $request, Fleet $fleet)
    {
        $data = $request->validate([
            'service_date'    => 'required|date',
            'service_type'    => 'required|string|max:150',
            'description'     => 'nullable|string',
            'parts_replaced'  => 'nullable|string',
            'workshop'        => 'nullable|string|max:200',
            'odometer_reading' => 'nullable|integer|min:0',
            'cost'            => 'nullable|numeric|min:0',
            'document'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store("fleet/{$fleet->id}/service", 'public');
        }

        $fleet->serviceRecords()->create([
            'service_date'     => $data['service_date'],
            'service_type'     => $data['service_type'],
            'description'      => $data['description'] ?? null,
            'parts_replaced'   => $data['parts_replaced'] ?? null,
            'workshop'         => $data['workshop'] ?? null,
            'odometer_reading' => $data['odometer_reading'] ?? null,
            'cost'             => $data['cost'] ?? 0,
            'document_path'    => $path,
            'recorded_by'      => auth()->user()->first_name . ' ' . auth()->user()->last_name ?? 'System',
        ]);

        return redirect()->back()->with('success', 'Service record added successfully.')->withFragment('tab-service');
    }

    public function destroyServiceRecord(Fleet $fleet, FleetServiceRecord $record)
    {
        $record->delete();
        return redirect()->back()->with('success', 'Service record deleted.')->withFragment('tab-service');
    }

    // ──────────────────────────────────────────────────────────────
    //  EXPENSE HISTORY
    // ──────────────────────────────────────────────────────────────

    public function storeExpense(Request $request, Fleet $fleet)
    {
        $data = $request->validate([
            'expense_date'     => 'required|date',
            'category'         => 'required|string|in:Maintenance,Repairs,Accidents,Tyres,Insurance,Spare Parts,Other',
            'description'      => 'nullable|string|max:255',
            'amount'           => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store("fleet/{$fleet->id}/expenses", 'public');
        }

        $fleet->expenses()->create([
            'expense_date'     => $data['expense_date'],
            'category'         => $data['category'],
            'description'      => $data['description'] ?? null,
            'amount'           => $data['amount'],
            'reference_number' => $data['reference_number'] ?? null,
            'document_path'    => $path,
            'recorded_by'      => auth()->user()->first_name . ' ' . auth()->user()->last_name ?? 'System',
        ]);

        return redirect()->back()->with('success', 'Expense recorded successfully.')->withFragment('tab-expenses');
    }

    public function destroyExpense(Fleet $fleet, FleetExpense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', 'Expense deleted.')->withFragment('tab-expenses');
    }
}

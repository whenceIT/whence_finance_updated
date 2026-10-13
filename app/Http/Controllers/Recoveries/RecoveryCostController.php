<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\RecoveryCost;
use App\Models\RecoveryCase;
use App\Models\Office;
use App\Models\UserRole;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class RecoveryCostController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Return a base query scoped to the current user's role.
     *
     * role 1 / 10  → Admin: all records
     * role 3       → Loan Officer: own office only
     * role 4       → Branch Manager: own office only
     * role 12      → DM Manager: all offices sharing the same district_id as user's office
     * role 6       → Provincial Manager: all offices sharing the same province_id as user's office
     */
    private function scopedQuery()
    {
        $user   = Sentinel::getUser();
        $role   = UserRole::where('user_id', $user->id)->first();
        $roleId = $role ? $role->role_id : null;

        $query = RecoveryCost::with(['recoveryCase.client', 'office', 'recordedBy'])
            ->latest();

        if (in_array($roleId, [1, 10]) || $roleId === null) {
            // Admin — no filter
            return $query;
        }

        if (in_array($roleId, [3, 4])) {
            // Branch-level: own office only
            return $query->where('office_id', $user->office_id);
        }

        if ($roleId == 12) {
            // DM Manager: all offices in the same district as user's office
            $districtId = optional($user->office)->district_id;
            if ($districtId) {
                $officeIds = Office::where('district_id', $districtId)->pluck('id');
                return $query->whereIn('office_id', $officeIds);
            }
            return $query->where('office_id', $user->office_id);
        }

        if ($roleId == 6) {
            // Provincial Manager: all offices in same province as user's office
            $provinceId = optional($user->office)->province_id;
            if ($provinceId) {
                $officeIds = Office::where('province_id', $provinceId)->pluck('id');
                return $query->whereIn('office_id', $officeIds);
            }
            return $query->where('office_id', $user->office_id);
        }

        // Fallback: own office
        return $query->where('office_id', $user->office_id);
    }

    /**
     * Restrict the office dropdown to what the current user is allowed to see.
     */
    private function allowedOffices()
    {
        $user   = Sentinel::getUser();
        $role   = UserRole::where('user_id', $user->id)->first();
        $roleId = $role ? $role->role_id : null;

        if (in_array($roleId, [1, 10])) {
            return Office::orderBy('name')->get();
        }

        if (in_array($roleId, [3, 4])) {
            return Office::where('id', $user->office_id)->get();
        }

        if ($roleId == 12) {
            $districtId = optional($user->office)->district_id;
            return $districtId
                ? Office::where('district_id', $districtId)->orderBy('name')->get()
                : Office::where('id', $user->office_id)->get();
        }

        if ($roleId == 6) {
            $provinceId = optional($user->office)->province_id;
            return $provinceId
                ? Office::where('province_id', $provinceId)->orderBy('name')->get()
                : Office::where('id', $user->office_id)->get();
        }

        return Office::where('id', $user->office_id)->get();
    }

    /**
     * Restrict the case dropdown to what the current user is allowed to see.
     */
    private function allowedCases()
    {
        $user   = Sentinel::getUser();
        $role   = UserRole::where('user_id', $user->id)->first();
        $roleId = $role ? $role->role_id : null;

        $query = RecoveryCase::with('client')
            ->whereNotNull('approved_date')
            ->orderByDesc('created_at');

        if (in_array($roleId, [1, 10]) || $roleId === null) {
            return $query->get();
        }

        if (in_array($roleId, [3, 4])) {
            return $query->where('origin_branch_id', $user->office_id)->get();
        }

        if ($roleId == 12) {
            $districtId = optional($user->office)->district_id;
            if ($districtId) {
                $officeIds = Office::where('district_id', $districtId)->pluck('id');
                return $query->whereIn('origin_branch_id', $officeIds)->get();
            }
            return $query->where('origin_branch_id', $user->office_id)->get();
        }

        if ($roleId == 6) {
            $provinceId = optional($user->office)->province_id;
            if ($provinceId) {
                $officeIds = Office::where('province_id', $provinceId)->pluck('id');
                return $query->whereIn('origin_branch_id', $officeIds)->get();
            }
            return $query->where('origin_branch_id', $user->office_id)->get();
        }

        return $query->where('origin_branch_id', $user->office_id)->get();
    }

    // ── CRUD ─────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = $this->scopedQuery();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by office
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Filter by case
        if ($request->filled('case_id')) {
            $query->where('case_id', $request->case_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('cost_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('cost_date', '<=', $request->date_to);
        }

        $costs   = $query->paginate(25)->withQueryString();
        $offices = $this->allowedOffices();
        $cases   = $this->allowedCases();

        // Summary totals (scoped)
        $totalAmount = $this->scopedQuery()->sum('amount');

        return view('recoveries.costs.index', compact('costs', 'offices', 'cases', 'totalAmount'));
    }

    public function create()
    {
        $offices = $this->allowedOffices();
        $cases   = $this->allowedCases();
        $user    = Sentinel::getUser();

        return view('recoveries.costs.create', compact('offices', 'cases', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount'      => 'required|numeric|min:0.01',
            'case_id'     => 'nullable|exists:recovery_cases,id',
            'office_id'   => 'required|exists:offices,id',
            'cost_date'   => 'required|date|before_or_equal:today',
        ]);

        $user = Sentinel::getUser();

        RecoveryCost::create([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount,
            'case_id'     => $request->case_id ?: null,
            'office_id'   => $request->office_id,
            'recorded_by' => $user->id,
            'cost_date'   => $request->cost_date,
        ]);

        Flash::success('Recovery cost recorded successfully.');

        return redirect('recovery/costs');
    }

    public function show($id)
    {
        $cost = RecoveryCost::with(['recoveryCase.client', 'office', 'recordedBy'])->findOrFail($id);

        return view('recoveries.costs.show', compact('cost'));
    }

    public function edit($id)
    {
        $cost    = RecoveryCost::findOrFail($id);
        $offices = $this->allowedOffices();
        $cases   = $this->allowedCases();

        return view('recoveries.costs.edit', compact('cost', 'offices', 'cases'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount'      => 'required|numeric|min:0.01',
            'case_id'     => 'nullable|exists:recovery_cases,id',
            'office_id'   => 'required|exists:offices,id',
            'cost_date'   => 'required|date|before_or_equal:today',
        ]);

        $cost = RecoveryCost::findOrFail($id);

        $cost->update([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount,
            'case_id'     => $request->case_id ?: null,
            'office_id'   => $request->office_id,
            'cost_date'   => $request->cost_date,
        ]);

        Flash::success('Recovery cost updated successfully.');

        return redirect('recovery/costs');
    }

    public function destroy($id)
    {
        RecoveryCost::findOrFail($id)->delete();

        Flash::success('Recovery cost deleted.');

        return redirect('recovery/costs');
    }
}

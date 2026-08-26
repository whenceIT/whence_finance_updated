<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Exports\ExportReport;
use App\Models\AuditTrail;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\Loan;
use App\Models\Office;
use App\Models\Province;
use App\Models\District;
use App\Models\UserRole;
use App\Models\PlatformSetting;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;

class CollateralController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('collateral_type_id')) {
            $query->where('collateral_type_id', $request->collateral_type_id);
        }
        if ($request->filled('loan_status')) {
            $query->whereHas('loan', function ($q) use ($request) {
                $q->where('status', $request->loan_status);
            });
        }
        if ($request->filled('date_purchased_from')) {
            $query->where('date_purchased', '>=', $request->date_purchased_from);
        }
        if ($request->filled('date_purchased_to')) {
            $query->where('date_purchased', '<=', $request->date_purchased_to);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        return $query;
    }

    /**
     * Display a paginated, filtered, sorted list of collateral items
     * scoped to the authenticated user's role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $user   = Sentinel::getUser();
        $userId = $user->id;
        $role   = UserRole::where('user_id', $userId)->first();
        $roleId = $role ? $role->role_id : null;

        // Base query with required eager-loads
        $query = Collateral::with(['loan.client', 'loan.office', 'type', 'created_by']);

        // --- Role-based scope ---
        if ($roleId == 1) {
            // Admin — sees ALL collateral; no additional constraint
        } elseif ($roleId == 4) {
            // Loan Officer / Branch Manager — own office only
            $officeId = $user->office_id;
            $query->with('loan')->where('office_id', $officeId);
            
        } elseif ($roleId == 12) {
            // DM Manager — own district (loan->office->district_id == user->office->district_id)
            $userOffice  = $user->office;
            $districtId  = $userOffice ? $userOffice->district_id : null;
            $query->with('loan')->where('district_id', $districtId);
        } elseif ($roleId == 6) {
            // Provincial Manager — own province
            $provinceId = $user->office->province_id;
            $query->with('loan')->where('province_id', $provinceId);
        } else {
            // Default: scope to collateral created by the user (Loan Consultants)
            $query->where('created_by_id', $userId);
        }

        // --- Filters ---
        if ($request->filled('key')) {
            if ($request->key === 'admin') {
                $query->whereIn('status', ['pledged', 'seizure_pending', 'seized_inventory', 'valuation_completed', 'listed_for_sale', 'written_off']);
            } elseif ($request->key === 'sales') {
                $query->whereIn('status', ['valuation_completed', 'listed_for_sale', 'sold']);
            } elseif ($request->key === 'valuation') {
                $query->where('status', 'seized_inventory');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('loan_id')) {
            $query->where('loan_id', $request->loan_id);
        }

        if ($request->filled('collateral_type_id')) {
            $query->where('collateral_type_id', $request->collateral_type_id);
        }

        if ($request->filled('office_id')) {
            $filterOfficeId = $request->office_id;
            $query->with('loan', function ($q) use ($filterOfficeId) {
                $q->where('office_id', $filterOfficeId);
            });
        }

        if ($request->filled('province_id')) {
            $filterProvinceId = $request->province_id;
            $query->with('loan.office', function ($q) use ($filterProvinceId) {
                $q->where('province_id', $filterProvinceId);
            });
        }

        if ($request->filled('date_purchased_from')) {
            $query->whereDate('date_purchased', '>=', $request->date_purchased_from);
        }

        if ($request->filled('date_purchased_to')) {
            $query->whereDate('date_purchased', '<=', $request->date_purchased_to);
        }

        if ($request->filled('date_resold_from')) {
            $query->whereDate('date_resold', '>=', $request->date_resold_from);
        }

        if ($request->filled('date_resold_to')) {
            $query->whereDate('date_resold', '<=', $request->date_resold_to);
        }

        // --- Search (partial match on name, description, or loan_id) ---
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%')
                  ->orWhere('loan_id', 'like', '%' . $term . '%');
            });
        }

        // --- Sorting ---
        $allowedSortColumns = ['name', 'initial_price', 'current_worth', 'status', 'condition', 'date_purchased'];
        $sortBy  = in_array($request->sortBy, $allowedSortColumns) ? $request->sortBy : 'created_at';
        $sortDir = $request->sort === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);


        // --- Paginate ---
        $collateral = $query->paginate(15)->appends($request->except('page'));

        $collateralTypes = CollateralType::all();
        $offices = Office::all();
        $provinces = Province::all();

        // Role-based scoping for loans
        $loansQuery = Loan::whereIn('status', ['disbursed', 'defaulted']);
        if ($roleId == 1) {
            // Admin — sees ALL loans
        } elseif ($roleId == 4) {
            // Loan Officer / Branch Manager — own office only
            $officeId = $user->office_id;
            $loansQuery->where('office_id', $officeId);
        } elseif ($roleId == 12) {
            // DM Manager — own district
            $userOffice  = $user->office;
            $districtId  = $userOffice ? $userOffice->district_id : null;
            $loansQuery->whereHas('office', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        } elseif ($roleId == 6) {
            // Provincial Manager — own province
            $provinceId = $user->province_id;
            $loansQuery->whereHas('office', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        } else {
            // Default: scope to own office
            $loansQuery->where('loan_officer_id', $user->id);
        }
        $loans = $loansQuery->get();

        // --- Stats for admin disposal view ---
        $adminStatuses = ['pledged', 'seizure_pending', 'seized_inventory', 'valuation_completed', 'listed_for_sale', 'sold', 'written_off'];
        $disposalStats = [];
        $totalCount = 0;
        $totalWorth = 0;

        if ($request->filled('key') && $request->key === 'admin') {
            foreach ($adminStatuses as $st) {
                $count = Collateral::where('status', $st);
                if ($roleId == 4) {
                    $count->where('office_id', $user->office_id);
                } elseif ($roleId == 12) {
                    $count->where('district_id', $user->office ? $user->office->district_id : null);
                } elseif ($roleId == 6) {
                    $count->where('province_id', $user->office ? $user->office->province_id : null);
                }
                $count = $count->count();
                $sum = Collateral::where('status', $st);
                if ($roleId == 4) {
                    $sum->where('office_id', $user->office_id);
                } elseif ($roleId == 12) {
                    $sum->where('district_id', $user->office ? $user->office->district_id : null);
                } elseif ($roleId == 6) {
                    $sum->where('province_id', $user->office ? $user->office->province_id : null);
                }
                $sum = $sum->sum('current_worth');
                $disposalStats[$st]['count'] = $count;
                $disposalStats[$st]['sum'] = $sum;
                $totalCount += $count;
                $totalWorth += $sum;
            }
        }

        return view('collateral.index', compact('collateral', 'collateralTypes', 'offices', 'provinces', 'loans', 'disposalStats', 'totalCount', 'totalWorth'));
    }

    /**
     * Show the form for creating a new collateral item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $user   = Sentinel::getUser();
        $userId = $user->id;
        $role   = UserRole::where('user_id', $userId)->first();
        $roleId = $role ? $role->role_id : null;

        // Role-based scoping for loans
        $loansQuery = Loan::query();
        if ($roleId == 1) {
            // Admin — sees ALL loans
        } elseif ($roleId == 4) {
            // Loan Officer / Branch Manager — own office only
            $officeId = $user->office_id;
            $loansQuery->where('office_id', $officeId);
        } elseif ($roleId == 12) {
            // DM Manager — own district
            $userOffice  = $user->office;
            $districtId  = $userOffice ? $userOffice->district_id : null;
            $loansQuery->whereHas('office', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        } elseif ($roleId == 6) {
            // Provincial Manager — own province
            $provinceId = $user->province_id;
            $loansQuery->whereHas('office', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        } else {
            // Default: scope to own office
            $loansQuery->where('loan_officer_id', $user->id);
        }
        $loans = $loansQuery->with('vetted_by_field')->get();
        $collateralTypes = CollateralType::all();

        $stageOptions = [
            'pledged' => 'Pledged collateral',
            'brought_in' => 'Brought in collateral',
            'seized' => 'Seized collateral',
        ];

        $loanBalances = [];
        foreach ($loans as $loan) {
            $loanBalances[$loan->id] = \App\Helpers\GeneralHelper::new_new_loan_total_balance($loan->id);
        }

        // Get loan_id from query parameter if provided
        $loanId = $request->query('loan_id');

        // If loan_id is provided and not in the loans collection, add it
        if ($loanId && !$loans->contains('id', $loanId)) {
            $loan = Loan::with('vetted_by_field')->find($loanId);
            if ($loan) {
                $loans->prepend($loan);
                $loanBalances[$loan->id] = \App\Helpers\GeneralHelper::new_new_loan_total_balance($loan->id);
            }
        }

        return view('collateral.create', compact('loans', 'collateralTypes', 'loanId', 'stageOptions', 'loanBalances'));
    }

    /**
     * Store a newly created collateral item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $request->validate([
            'name'           => 'required',
            'serial_num'     => 'nullable|string|max:255|unique:collaterals,serial_num',
            'initial_price'  => 'required',
            'current_worth'  => 'required',
            'loan_id'        => 'required|integer|unique:collaterals,loan_id',
            'date_purchased' => 'required',
            'pledged_at'     => 'nullable|date',
            'condition'      => 'required',
            'stage_icon'     => 'nullable|string',
            'vetted_valuation'      => 'nullable|numeric|min:0',
            'vetted_valuation_cost' => 'nullable|numeric|min:0',
            'vetted_valuation_by'    => 'nullable|integer|exists:users,id',
            'vetted_valuation_status' => 'nullable|integer|in:0,1',
            'vvc_items'             => 'nullable|array',
            'vvc_items.*.name'   => 'nullable|string|max:255',
            'vvc_items.*.amount' => 'nullable|numeric|min:0',
        ]);

        // Verify the selected loan has an eligible status
        $loan = Loan::find($request->loan_id);
        // if (!$loan || !in_array($loan->status, ['disbursed', 'defaulted'])) {
        //     return redirect()->back()
        //         ->withInput()
        //         ->withErrors(['loan_id' => 'The selected loan must be disbursed or defaulted.']);
        // }

        $collateral = new Collateral();
        $collateral->name              = $request->name;
        $collateral->serial_num        = $request->serial_num;
        $collateral->initial_price     = $request->initial_price;
        $collateral->current_worth     = $request->current_worth;
        $collateral->approved_value    = $request->approved_value ?? $request->current_worth;
        $collateral->loan_id           = $request->loan_id;
        $collateral->date_purchased    = $request->date_purchased;
        $collateral->pledged_at        = $request->pledged_at;
        $collateral->status            = 'pledged';
        $collateral->condition         = $request->condition;
        $collateral->description       = $request->description;
        $collateral->collateral_type_id = $request->collateral_type_id;
        $collateral->created_by_id     = Sentinel::getUser()->id;
        $collateral->new_approval_status = 0;

        $collateral->province_id = $loan->office->province_id; //for Province analytics level
        $collateral->district_id = $loan->office->district_id; //for District analytics level
        $collateral->office_id = $loan->office->id; //for Office analytics level

        $collateral->stage_icon = $request->stage_icon;
        $collateral->vetted_valuation = $request->vetted_valuation;
        $collateral->vetted_valuation_by = $loan->vetted_by ?? null;
        $vvcItems = $request->vvc_items ?: [];
        $collateral->vvc_items = $vvcItems;
        $collateral->vetted_valuation_cost = collect($vvcItems)->sum('amount') ?: ($request->vetted_valuation_cost ?? 0);

        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => $collateral->name.' collateral created',
            'table_name' => 'collaterals',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Successfully saved');
        return redirect()->route('collateral.index');
    }

    public function approve(Request $request, $collateralId)
    {
        $collateral = Collateral::findOrFail($collateralId);
        $collateral->vetted_valuation_status = 1;
        $collateral->save();

        return response()->json(['success' => true, 'message' => 'Collateral approved']);
    }

    public function decline(Request $request, $collateralId)
    {
        $collateral = Collateral::findOrFail($collateralId);
        $collateral->vetted_valuation_by = null;
        $collateral->save();

        return response()->json(['success' => true, 'message' => 'Collateral declined']);
    }

    /**
     * Display the specified collateral item.
     *
     * @param  \App\Models\Collateral  $collateral
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function getEligibleLoans()
    {
        $user = Sentinel::getUser();
        $userId = $user->id;
        $role = UserRole::where('user_id', $userId)->first();
        $roleId = $role ? $role->role_id : null;

        $loansQuery = Loan::query();
        if ($roleId == 1) {
            // Admin — sees ALL loans
        } elseif ($roleId == 4) {
            $loansQuery->where('office_id', $user->office_id);
        } elseif ($roleId == 12) {
            $userOffice = $user->office;
            $districtId = $userOffice ? $userOffice->district_id : null;
            $loansQuery->whereHas('office', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        } elseif ($roleId == 6) {
            $provinceId = $user->province_id;
            $loansQuery->whereHas('office', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        } else {
            $loansQuery->where('loan_officer_id', $user->id);
        }

        return $loansQuery->get();
    }

    public function show(Collateral $collateral, Request $request)
    {
        $collateral->load([
            'type',
            'created_by',
            'auditTrail',
            'statusChanges.requested_by',
            'statusChanges.approved_by',
        ]);

        if ($collateral->loan_id) {
            $collateral->load([
                'loan.client',
                'loan.office',
            ]);
            $loanPrincipal = DB::table('loans')->where('id', $collateral->loan_id)->value('principal');
            $loanInterest = $loanPrincipal * 0.40;
            $loanBalance = \App\Helpers\GeneralHelper::new_new_loan_total_balance($collateral->loan_id);
            $loanPenalty = \App\Models\LoanTransaction::where('loan_id', $collateral->loan_id)
                ->where('transaction_type', 'specified_due_date_fee')
                ->sum('amount');
        } else {
            $loanPrincipal = 0;
            $loanInterest = 0;
            $loanBalance = 0;
            $loanPenalty = 0;
        }

        $hasPendingStatusChange = $collateral->statusChanges()
            ->where('approval_status', 'pending')
            ->exists();

        $loans = $collateral->loan_id ? null : $this->getEligibleLoans();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_viewed',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        return view('collateral.show', compact('collateral', 'loanPrincipal', 'loanInterest', 'loanBalance', 'loanPenalty', 'hasPendingStatusChange', 'loans'));
    }

    /**
     * Assign a loan to the specified collateral item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function assignLoan(Request $request, Collateral $collateral)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
        ]);

        $loan = Loan::find($request->loan_id);
        $collateral->loan_id = $loan->id;
        $collateral->province_id = $loan->office->province_id;
        $collateral->district_id = $loan->office->district_id;
        $collateral->office_id = $loan->office->id;
        $collateral->save();

        Flash::success('Loan assigned successfully');
        return redirect()->route('collateral.show', $collateral);
    }

    /**
     * Show the form for editing the specified collateral item.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function edit(Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $collateralTypes = CollateralType::all();

        $stageOptions = [
            'pledged' => 'Pledged collateral',
            'brought_in' => 'Brought in collateral',
            'seized' => 'Seized collateral',
        ];

        $collateral->load(['loan.vetted_by_field']);

        return view('collateral.edit', compact('collateral', 'collateralTypes', 'stageOptions'));
    }

    /**
     * Update the specified collateral item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $request->validate([
            'current_worth' => 'required',
            'condition'     => 'required',
            'description'   => 'nullable',
            'date_resold'   => 'nullable|date',
            'pledged_at'    => 'nullable|date',
            'seized_at'     => 'nullable|date',
            'valuated_at'   => 'nullable|date',
            'listed_at'     => 'nullable|date',
            'sold_at'       => 'nullable|date',
            'written_off_at'=> 'nullable|date',
            'released_at'   => 'nullable|date',
            'serial_num'    => 'required|string|max:255|unique:collaterals,serial_num,' . $collateral->id,
            'loan_id'       => 'required|integer|unique:collaterals,loan_id,' . $collateral->id,
            'stage_icon'    => 'nullable|string',
            'vetted_valuation'      => 'nullable|numeric|min:0',
            'vetted_valuation_cost' => 'nullable|numeric|min:0',
            'vetted_valuation_by'    => 'nullable|integer|exists:users,id',
            'vetted_valuation_status' => 'nullable|integer|in:0,1',
            'vvc_items'             => 'nullable|array',
            'vvc_items.*.name'   => 'nullable|string|max:255',
            'vvc_items.*.amount' => 'nullable|numeric|min:0',
        ]);

        $loan = Loan::find($request->loan_id ?? $collateral->loan_id);

        $collateral->serial_num      = $request->serial_num;
        $collateral->current_worth   = $request->current_worth;
        $collateral->approved_value  = $request->approved_value ?? $collateral->approved_value ?? $request->current_worth;
        $collateral->condition       = $request->condition;
        $collateral->description     = $request->description;
        $collateral->date_resold     = $request->date_resold;
        $collateral->pledged_at      = $request->pledged_at;
        $collateral->seized_at       = $request->seized_at;
        $collateral->valuated_at     = $request->valuated_at;
        $collateral->listed_at       = $request->listed_at;
        $collateral->sold_at         = $request->sold_at;
        $collateral->written_off_at  = $request->written_off_at;
        $collateral->released_at     = $request->released_at;
        $collateral->stage_icon      = $request->stage_icon;
        $collateral->vetted_valuation = $request->vetted_valuation;
        $collateral->vetted_valuation_by = $request->vetted_valuation_by ?: ($loan->vetted_by ?? $collateral->vetted_valuation_by);
        $vvcItems = $request->vvc_items ?: [];
        $collateral->vvc_items = $vvcItems;
        $collateral->vetted_valuation_cost = collect($vvcItems)->sum('amount') ?: ($request->vetted_valuation_cost ?? 0);

        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_updated',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Successfully saved');
        return redirect()->route('collateral.show', $collateral);
    }

    public function destroy(Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.delete')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_deleted',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => request()->ip(),
        ]);

        $collateral->delete();

        Flash::success('Collateral deleted successfully.');
        return redirect()->route('collateral.index');
    }

    public function setup(Request $request)
    {
        $users = User::where('status', 'Active')
            ->with('position')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $supervisor = PlatformSetting::where('key', 'collateral_supervisor')->first();
        $valuator = PlatformSetting::where('key', 'collateral_valuator')->first();

        $supervisorId = $supervisor ? $supervisor->value : null;
        $valuatorId = $valuator ? $valuator->value : null;

        return view('collateral.setup', compact('users', 'supervisorId', 'valuatorId'));
    }

    public function setupUpdate(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'nullable|exists:users,id',
            'valuator_id'   => 'nullable|exists:users,id',
        ]);

        PlatformSetting::updateOrCreate(
            ['key' => 'collateral_supervisor'],
            ['value' => $request->supervisor_id]
        );

        PlatformSetting::updateOrCreate(
            ['key' => 'collateral_valuator'],
            ['value' => $request->valuator_id]
        );

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_workflow_setup_updated',
            'table_name' => 'platform_settings',
            'record_id'  => 0,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Collateral workflow setup updated successfully.');
        return redirect()->route('collateral.setup');
    }

    public function requestRelease(Request $request, Collateral $collateral)
    {
        if (in_array($collateral->status, ['sold', 'written_off', 'released', 'release_pending'])) {
            Flash::warning('This collateral cannot be released at its current status.');
            return redirect()->route('collateral.show', $collateral);
        }

        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $collateral->status = 'release_pending';
        $collateral->release_requested_at = Carbon::now();
        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_release_requested',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Release request submitted. Status changed to Release Pending.');
        return redirect()->route('collateral.show', $collateral);
    }

    public function analyticsExecutive(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.analytics.executive')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $query = Collateral::with(['loan.office', 'type']);
        $query = $this->applyFilters($query, $request);

        $statuses = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $typeExposure = $query->selectRaw('collateral_type_id, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('collateral_type_id')
            ->with('type')
            ->get();

        $startDate = $request->filled('date_purchased_from') ? Carbon::parse($request->date_purchased_from)->startOfDay() : Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $request->filled('date_purchased_to') ? Carbon::parse($request->date_purchased_to)->endOfDay() : Carbon::now()->endOfDay();

        $timeSeries = Collateral::whereBetween('date_purchased', [$startDate, $endDate])
            ->when($request->filled('collateral_type_id'), function ($q) use ($request) {
                $q->where('collateral_type_id', $request->collateral_type_id);
            })
            ->when($request->filled('loan_status'), function ($q) use ($request) {
                $q->with('loan', function ($q2) use ($request) {
                    $q2->where('status', $request->loan_status);
                });
            })
            ->selectRaw("DATE_FORMAT(date_purchased, '%Y-%m') as period, SUM(current_worth) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $chartLabels = $timeSeries->pluck('period');
        $chartValues = $timeSeries->pluck('total');
        $emptyState = $statuses->isEmpty() && $typeExposure->isEmpty();
        $collateralTypes = CollateralType::all();
        $loanStatuses = ['disbursed', 'defaulted', 'pending', 'approved', 'declined', 'written_off'];

        $perPage = 10;
        $statusKeys = ['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released','release_pending'];

        $statusData = [];
        foreach ($statusKeys as $statusKey) {
            $statusQuery = Collateral::with(['loan.office', 'type'])
                ->where('status', $statusKey);
            if ($request->filled('collateral_type_id')) {
                $statusQuery->where('collateral_type_id', $request->collateral_type_id);
            }
            if ($request->filled('loan_status')) {
                $statusQuery->whereHas('loan', function ($q) use ($request) {
                    $q->where('status', $request->loan_status);
                });
            }
            if ($request->filled('date_purchased_from')) {
                $statusQuery->whereDate('date_purchased', '>=', $request->date_purchased_from);
            }
            if ($request->filled('date_purchased_to')) {
                $statusQuery->whereDate('date_purchased', '<=', $request->date_purchased_to);
            }
            $statusData[$statusKey] = $statusQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        }

        return view('collateral.analytics_executive', compact(
            'statuses',
            'typeExposure',
            'chartLabels',
            'chartValues',
            'emptyState',
            'collateralTypes',
            'loanStatuses',
            'statusData'
        ));
    }

    public function analyticsProvincial(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.analytics.provincial')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $user = Sentinel::getUser();
        $provinceId = $user->province_id;

        $query = Collateral::with(['loan.office', 'type'])->where('province_id', $provinceId);

        $query = $this->applyFilters($query, $request);

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('condition')
            ->get();

        $defaulted = $query->where('status', 'defaulted')->count();
        $sold = $query->where('status', 'sold')->count();

        $provinceOptions = Province::where('id', $provinceId)->get();
        $offices = Office::where('province_id', $provinceId)->get();
        $collateralTypes = CollateralType::all();
        $statusOptions = ['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released','release_pending'];

        $perPage = 10;
        $statusData = [];
        foreach ($statusOptions as $statusKey) {
            $statusQuery = Collateral::with(['loan.office', 'type'])
                ->where('province_id', $provinceId)
                ->where('status', $statusKey);
            if ($request->filled('collateral_type_id')) {
                $statusQuery->where('collateral_type_id', $request->collateral_type_id);
            }
            if ($request->filled('date_purchased_from')) {
                $statusQuery->whereDate('date_purchased', '>=', $request->date_purchased_from);
            }
            if ($request->filled('date_purchased_to')) {
                $statusQuery->whereDate('date_purchased', '<=', $request->date_purchased_to);
            }
            $statusData[$statusKey] = $statusQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        }

        return view('collateral.analytics_provincial', compact(
            'statusTotals',
            'conditionTotals',
            'defaulted',
            'sold',
            'provinceOptions',
            'offices',
            'collateralTypes',
            'provinceId',
            'statusOptions',
            'statusData',
        ));
    }

    public function analyticsDistrict(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.analytics.district')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $user = Sentinel::getUser();
        $districtId = $user->office->district_id;

        $query = Collateral::with(['loan.office', 'type'])
            ->with('loan.office', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        $query = $this->applyFilters($query, $request);

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('condition')
            ->get();

        $defaulted = $query->where('status', 'defaulted')->count();
        $sold = $query->where('status', 'sold')->count();

        $districtOptions = ($isExecutive || $isProvincial) ? District::all() : District::where('id', $districtId)->get();
        $offices = Office::where('district_id', $districtId)->get();
        $collateralTypes = CollateralType::all();
        $statusOptions = ['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released','release_pending'];

        $perPage = 10;
        $statusData = [];
        foreach ($statusOptions as $statusKey) {
            $statusQuery = Collateral::with(['loan.office', 'type'])
                ->with('loan.office', function ($q) use ($districtId) {
                    $q->where('district_id', $districtId);
                })
                ->where('status', $statusKey);
            if ($request->filled('collateral_type_id')) {
                $statusQuery->where('collateral_type_id', $request->collateral_type_id);
            }
            if ($request->filled('date_purchased_from')) {
                $statusQuery->whereDate('date_purchased', '>=', $request->date_purchased_from);
            }
            if ($request->filled('date_purchased_to')) {
                $statusQuery->whereDate('date_purchased', '<=', $request->date_purchased_to);
            }
            $statusData[$statusKey] = $statusQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        }

        return view('collateral.analytics_district', compact(
            'statusTotals',
            'conditionTotals',
            'defaulted',
            'sold',
            'districtOptions',
            'offices',
            'collateralTypes',
            'districtId',
            'statusOptions',
            'statusData',
            'isExecutive',
            'isProvincial'
        ));
    }

    public function analyticsBranch(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.analytics.branch')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $user = Sentinel::getUser();
        $officeId = $user->office_id;

        $query = Collateral::with(['loan.office', 'type'])->where('office_id', $officeId);
        $query = $this->applyFilters($query, $request);

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('condition')
            ->get();

        $reassessmentList = $query->whereRaw('current_worth < initial_price * 0.85')
            ->orderBy('date_purchased', 'desc')
            ->limit(20)
            ->get();

        $collateralTypes = CollateralType::all();
        $conditionOptions = ['new', 'good', 'fair', 'poor'];
        $statusOptions = ['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released','release_pending'];
        $office = \App\Models\Office::find($officeId);

        $perPage = 10;
        $statusData = [];
        foreach ($statusOptions as $statusKey) {
            $statusQuery = Collateral::with(['loan.office', 'type'])
                ->where('office_id', $officeId)
                ->where('status', $statusKey);
            if ($request->filled('collateral_type_id')) {
                $statusQuery->where('collateral_type_id', $request->collateral_type_id);
            }
            if ($request->filled('date_purchased_from')) {
                $statusQuery->whereDate('date_purchased', '>=', $request->date_purchased_from);
            }
            if ($request->filled('date_purchased_to')) {
                $statusQuery->whereDate('date_purchased', '<=', $request->date_purchased_to);
            }
            $statusData[$statusKey] = $statusQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        }

        return view('collateral.analytics_branch', compact(
            'statusTotals',
            'conditionTotals',
            'reassessmentList',
            'collateralTypes',
            'conditionOptions',
            'statusOptions',
            'officeId',
            'office',
            'statusData'
        ));
    }

    public function exportCsv(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.reports')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $query = Collateral::with(['loan.client', 'loan.office', 'type']);
        $data = $this->applyFilters($query, $request)->get();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_report_generated',
            'table_name' => 'collateral',
            'record_id'  => 0,
            'ip_address' => $request->ip(),
        ]);

        $filename = 'collateral_report_' . Carbon::now()->format('Ymd') . '.csv';

        return Excel::download(new ExportReport('collateral.exports.collateral_csv', compact('data')), $filename);
    }

    public function myCollateral(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $user = Sentinel::getUser();
        $userId = $user->id;

        $query = Collateral::with(['loan.office', 'type'])
            ->where('created_by_id', $userId);
        $query = $this->applyFilters($query, $request);

        $statuses = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $typeExposure = $query->selectRaw('collateral_type_id, COUNT(*) as count, SUM(current_worth) as total, SUM(sold_price) as total_sold, SUM(penalty) as total_penalty')
            ->groupBy('collateral_type_id')
            ->with('type')
            ->get();

        $startDate = $request->filled('date_purchased_from') ? Carbon::parse($request->date_purchased_from)->startOfDay() : Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $request->filled('date_purchased_to') ? Carbon::parse($request->date_purchased_to)->endOfDay() : Carbon::now()->endOfDay();

        $timeSeries = Collateral::where('created_by_id', $userId)
            ->whereBetween('date_purchased', [$startDate, $endDate])
            ->when($request->filled('collateral_type_id'), function ($q) use ($request) {
                $q->where('collateral_type_id', $request->collateral_type_id);
            })
            ->when($request->filled('loan_status'), function ($q) use ($request) {
                $q->with('loan', function ($q2) use ($request) {
                    $q2->where('status', $request->loan_status);
                });
            })
            ->selectRaw("DATE_FORMAT(date_purchased, '%Y-%m') as period, SUM(current_worth) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $chartLabels = $timeSeries->pluck('period');
        $chartValues = $timeSeries->pluck('total');
        $emptyState = $statuses->isEmpty() && $typeExposure->isEmpty();
        $collateralTypes = CollateralType::all();
        $loanStatuses = ['disbursed', 'defaulted', 'pending', 'approved', 'declined', 'written_off'];

        $perPage = 10;
        $statusKeys = ['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released','release_pending'];
        $statusData = [];
        foreach ($statusKeys as $statusKey) {
            $statusQuery = Collateral::with(['loan.office', 'type'])
                ->where('created_by_id', $userId)
                ->where('status', $statusKey);
            if ($request->filled('collateral_type_id')) {
                $statusQuery->where('collateral_type_id', $request->collateral_type_id);
            }
            if ($request->filled('loan_status')) {
                $statusQuery->whereHas('loan', function ($q) use ($request) {
                    $q->where('status', $request->loan_status);
                });
            }
            if ($request->filled('date_purchased_from')) {
                $statusQuery->whereDate('date_purchased', '>=', $request->date_purchased_from);
            }
            if ($request->filled('date_purchased_to')) {
                $statusQuery->whereDate('date_purchased', '<=', $request->date_purchased_to);
            }
            $statusData[$statusKey] = $statusQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        }

        return view('collateral.my_analytics', compact(
            'statuses',
            'typeExposure',
            'chartLabels',
            'chartValues',
            'emptyState',
            'collateralTypes',
            'loanStatuses',
            'statusData'
        ));
    }
}

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
        $query = Collateral::with(['loan.client', 'loan.office', 'type']);

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

        // --- Search (partial match on name and description) ---
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        // --- Sorting ---
        $allowedSortColumns = ['name', 'initial_price', 'current_worth', 'status', 'condition', 'date_purchased'];
        $sortBy  = in_array($request->sortBy, $allowedSortColumns) ? $request->sortBy : 'created_at';
        $sortDir = $request->sortDir === 'desc' ? 'desc' : 'asc';
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

        return view('collateral.index', compact('collateral', 'collateralTypes', 'offices', 'provinces', 'loans'));
    }

    /**
     * Show the form for creating a new collateral item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $collateralTypes = CollateralType::all();

        return view('collateral.create', compact('loans', 'collateralTypes'));
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
            'initial_price'  => 'required',
            'current_worth'  => 'required',
            'loan_id'        => 'required',
            'date_purchased' => 'required',
            'status'         => 'required',
            'condition'      => 'required',
        ]);

        // Verify the selected loan has an eligible status
        $loan = Loan::find($request->loan_id);
        if (!$loan || !in_array($loan->status, ['disbursed', 'defaulted'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['loan_id' => 'The selected loan must be disbursed or defaulted.']);
        }

        $collateral = new Collateral();
        $collateral->name              = $request->name;
        $collateral->initial_price     = $request->initial_price;
        $collateral->current_worth     = $request->current_worth;
        $collateral->loan_id           = $request->loan_id;
        $collateral->date_purchased    = $request->date_purchased;
        $collateral->status            = $request->status;
        $collateral->condition         = $request->condition;
        $collateral->description       = $request->description;
        $collateral->collateral_type_id = $request->collateral_type_id;
        $collateral->created_by_id     = Sentinel::getUser()->id;

        $collateral->province_id = $loan->office->province_id; //for Province analytics level
        $collateral->district_id = $loan->office->district_id; //for District analytics level
        $collateral->office_id = $loan->office->id; //for Office analytics level

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

    /**
     * Display the specified collateral item.
     *
     * @param  \App\Models\Collateral  $collateral
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Collateral $collateral, Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.view')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        $collateral->load([
            'loan.client',
            'loan.office',
            'type',
            'created_by',
            'auditTrail',
            'statusChanges.requested_by',
            'statusChanges.approved_by',
        ]);

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_viewed',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        return view('collateral.show', compact('collateral'));
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

        return view('collateral.edit', compact('collateral', 'collateralTypes'));
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
        ]);

        $collateral->current_worth = $request->current_worth;
        $collateral->condition     = $request->condition;
        $collateral->description   = $request->description;
        $collateral->date_resold   = $request->date_resold;
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

    public function analyticsExecutive(Request $request)
    {
        // if (!Sentinel::hasAccess('collateral.analytics.executive')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $query = Collateral::with(['loan.office', 'type']);
        $query = $this->applyFilters($query, $request);

        $statuses = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total')
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

        return view('collateral.analytics_executive', compact(
            'statuses',
            'typeExposure',
            'chartLabels',
            'chartValues',
            'emptyState',
            'collateralTypes',
            'loanStatuses'
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

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('condition')
            ->get();

        $defaulted = $query->where('status', 'defaulted')->count();
        $sold = $query->where('status', 'sold')->count();

        $provinceOptions = Province::where('id', $provinceId)->get();
        $offices = Office::where('province_id', $provinceId)->get();
        $collateralTypes = CollateralType::all();
        $statusOptions = ['active', 'sold', 'defaulted', 'repossessed'];

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

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('condition')
            ->get();

        $defaulted = $query->where('status', 'defaulted')->count();
        $sold = $query->where('status', 'sold')->count();

        $districtOptions = ($isExecutive || $isProvincial) ? District::all() : District::where('id', $districtId)->get();
        $offices = Office::where('district_id', $districtId)->get();
        $collateralTypes = CollateralType::all();
        $statusOptions = ['active', 'sold', 'defaulted', 'repossessed'];

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

        $statusTotals = $query->selectRaw('status, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('status')
            ->get();

        $conditionTotals = $query->selectRaw('`condition`, COUNT(*) as count, SUM(current_worth) as total')
            ->groupBy('condition')
            ->get();

        $reassessmentList = $query->whereRaw('current_worth < initial_price * 0.85')
            ->orderBy('date_purchased', 'desc')
            ->limit(20)
            ->get();

        $collateralTypes = CollateralType::all();
        $conditionOptions = ['new', 'good', 'fair', 'poor'];
        $statusOptions = ['active', 'sold', 'defaulted', 'repossessed'];
        $office = \App\Models\Office::find($officeId);

        return view('collateral.analytics_branch', compact(
            'statusTotals',
            'conditionTotals',
            'reassessmentList',
            'collateralTypes',
            'conditionOptions',
            'statusOptions',
            'officeId',
            'office'
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
}

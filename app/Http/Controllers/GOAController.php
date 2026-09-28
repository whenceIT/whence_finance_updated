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

        // Positions statistics — approved capacity vs current personnel (all branches)
        $totalPositions = Position::count();
        $approvedTotal = Office::where('active', 1)->sum('branch_capacity');
        $personnelTotal = User::whereIn('status', ['Active', 'active'])
            ->whereNotNull('office_id')
            ->count();
        $filledPositions = $personnelTotal;
        $vacantPositions = max($approvedTotal - $personnelTotal, 0);
        $inProcessPositions = Vacancy::whereIn('recruitment_status', ['Advertising', 'Shortlisting', 'Interviewing', 'Offer Made'])
            ->count();
        $fillRate = $approvedTotal > 0 ? min(100, round(($personnelTotal / $approvedTotal) * 100)) : 0;

        // Recruitment pipeline statistics (all branches)
        $pipelineTotalVacancies = Vacancy::where('recruitment_status', '!=', 'Filled')
            ->where('recruitment_status', '!=', 'Cancelled')
            ->sum('num_of_vacancies');
        $pipelineTotalApplicants = Vacancy::sum('num_of_applicants');
        $pipelineTotalShortlisted = Vacancy::sum('num_of_shortlisted');
        $pipelineTotalOffersIssued = Vacancy::whereIn('offer_status', ['Pending', 'Accepted'])
            ->count();
        $pipelineTotalReported = Vacancy::whereNotNull('actual_reporting_date')->count();

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
            'approvedTotal', 'personnelTotal',
            'pipelineTotalVacancies', 'pipelineTotalApplicants', 'pipelineTotalShortlisted',
            'pipelineTotalOffersIssued', 'pipelineTotalReported',
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
        $positions = Position::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $vacancies = Vacancy::with(['position.department', 'office'])
            ->whereNotIn('recruitment_status', ['Filled', 'Cancelled'])
            ->whereNull('actual_reporting_date')
            ->get();
        $offices = Office::where('active', 1)->orderBy('name')->get();

        // Staffing statistics — approved capacity vs current personnel (all branches)
        $approvedTotal = Office::where('active', 1)->sum('branch_capacity');
        $personnelTotal = User::whereIn('status', ['Active', 'active'])
            ->whereNotNull('office_id')
            ->count();
        $totalPositions = $approvedTotal;
        $filledPositions = $personnelTotal;
        $vacantPositions = max($approvedTotal - $personnelTotal, 0);
        $inProcessPositions = Vacancy::whereIn('recruitment_status', ['Advertising', 'Shortlisting', 'Interviewing', 'Offer Made'])
            ->count();

        // Department stats — based on users per department vs department capacity
        foreach($departments as $dept) {
            $dept->total_positions = Position::where('department_id', $dept->id)->count();
            $dept->filled_positions = User::whereIn('status', ['Active', 'active'])
                ->whereNotNull('position_id')
                ->whereHas('position', function($q) use ($dept) {
                    $q->where('department_id', $dept->id);
                })->count();
            $dept->vacant_positions = max(($dept->capacity > 0 ? $dept->capacity : $dept->total_positions) - $dept->filled_positions, 0);
        }

        // Recent hires (users with positions updated_at)
        $recentHires = User::with('position.department')->whereNotNull('position_id')->orderBy('updated_at', 'desc')->limit(10)->get();

        // All job_positions for the management tab
        $allPositions = Position::with('department')->orderBy('name')->get();

        return view('goa.vacancies-and-staffing', compact('positions', 'departments', 'vacancies', 'offices', 'totalPositions', 'filledPositions', 'vacantPositions', 'inProcessPositions', 'recentHires', 'allPositions'));
    }

    /**
     * Approved staffing structure vs actual personnel for every branch.
     *
     * Vacancy    = Approved Capacity - Current Personnel
     * Staffing % = Current Personnel / Approved Capacity x 100
     * Vacancy %  = Vacancy / Approved Capacity x 100
     *
     * @return \Illuminate\Http\Response
     */
    public function branchStaffingCapacity(Request $request)
    {
        $offices = Office::with(['district', 'province'])->where('active', 1)->orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        // Personnel currently attached to a branch
        $personnelByOffice = User::whereIn('status', ['Active', 'active'])
            ->whereNotNull('office_id')
            ->selectRaw('office_id, COUNT(*) as personnel_total')
            ->groupBy('office_id')
            ->pluck('personnel_total', 'office_id');

        // Branch level comparison used by the "All Branches" tab.
        // The approved capacity of a branch is offices.branch_capacity.
        $branchSummary = $offices->map(function ($office) use ($personnelByOffice) {
            $approvedCapacity = (int) ($office->branch_capacity ?? 0);

            return array_merge(
                $this->buildBranchCapacityMetrics($approvedCapacity, (int) ($personnelByOffice[$office->id] ?? 0)),
                 [
                    'office' => $office,
                    'capacity_recorded' => $approvedCapacity > 0,
                    'structure_defined' => $approvedCapacity > 0,
                ]
            );
        });

        $overallMetrics = $this->buildBranchCapacityMetrics(
            (int) $branchSummary->sum('approved'),
            (int) $branchSummary->sum('current')
        );

        // Branch currently being inspected
        $selectedOfficeId = $request->get('office_id');
        $selectedOffice = $selectedOfficeId
            ? $offices->firstWhere('id', (int) $selectedOfficeId)
            : $offices->first();

        if (!$selectedOffice && $selectedOfficeId) {
            $selectedOffice = Office::with(['district', 'province'])->find($selectedOfficeId);
        }

        $personnel = collect();
        $unassignedPersonnel = collect();
        $positionRows = collect();
        $branchVacancies = collect();
        $vacancyByPosition = collect();
        $approvedTotal = 0;
        $personnelTotal = 0;
        $vacancyTotal = 0;
        $staffingPercentage = null;
        $vacancyPercentage = 0;
        $structureDefined = false;

        if ($selectedOffice) {
            $personnel = User::with('position')
                ->where('office_id', $selectedOffice->id)
                ->whereIn('status', ['Active', 'active'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            $unassignedPersonnel = $personnel->whereNull('position_id')->values();

            $personnelByPosition = $personnel
                ->filter(function ($user) {
                    return $user->position_id !== null;
                })
                ->groupBy('position_id');

            $structureDefined = (int) ($selectedOffice->branch_capacity ?? 0) > 0;

            $positionIdsWithPersonnel = $personnelByPosition->keys()->all();
            $positionIdsWithVacancies = Vacancy::where('office_id', $selectedOffice->id)
                ->whereNotNull('position_id')
                ->pluck('position_id')
                ->all();

            $relevantPositionIds = array_unique(array_merge($positionIdsWithPersonnel, $positionIdsWithVacancies));
            $allPositionsMap = $positions->keyBy('id');

            $positionRows = collect($relevantPositionIds)->map(function ($positionId) use ($allPositionsMap, $personnelByPosition) {
                $group = $personnelByPosition->get($positionId, collect());
                $position = $allPositionsMap->get($positionId);
                $approvedForPosition = (int) ($position->approved ?? 0);

                return array_merge($this->buildBranchCapacityMetrics($approvedForPosition, $group->count()), [
                    'position' => $position,
                    'position_id' => $positionId,
                    'in_structure' => $approvedForPosition > 0,
                    'personnel' => $group->values(),
                ]);
            })->sortBy(function ($row) {
                return strtolower($row['position']->name ?? 'zz');
            })->values();

            $approvedTotal = (int) ($selectedOffice->branch_capacity ?? 0);
            $personnelTotal = $personnel->count();

            $branchMetrics = $this->buildBranchCapacityMetrics($approvedTotal, $personnelTotal);
            $vacancyTotal = $branchMetrics['vacancy'];
            $staffingPercentage = $branchMetrics['staffing_percentage'];
            $vacancyPercentage = $branchMetrics['vacancy_percentage'];

            $branchVacancies = Vacancy::with('position')
                ->where('office_id', $selectedOffice->id)
                ->orderByDesc('date_arose')
                ->orderByDesc('created_at')
                ->get();

            $vacancyByPosition = $branchVacancies->keyBy('position_id');
        }

        $activeTab = $request->get('tab', 'dashboard');

        return view('goa.branch-staffing-capacity', compact(
            'offices', 'positions', 'selectedOffice', 'branchSummary', 'overallMetrics',
            'positionRows', 'personnel', 'unassignedPersonnel', 'approvedTotal',
            'personnelTotal', 'vacancyTotal', 'staffingPercentage', 'vacancyPercentage',
            'structureDefined', 'branchVacancies', 'vacancyByPosition', 'activeTab'
        ));
    }

    /**
     * Display the recruitment pipeline dashboard.
     *
     * Shows a funnel/timeline view of the recruitment process for each vacancy
     * across all branches or filtered by branch.
     *
     * @return \Illuminate\Http\Response
     */
    public function recruitmentPipeline(Request $request)
    {
        $offices = Office::with(['district', 'province'])->where('active', 1)->orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        // Get selected office for filtering
        $selectedOfficeId = $request->get('office_id');
        $activeTab = 'recruitment-pipeline';
        $selectedOffice = $selectedOfficeId
            ? $offices->firstWhere('id', (int) $selectedOfficeId)
            : null;

        // Base query for vacancies
        $vacancyQuery = Vacancy::with(['office', 'position.department']);

        if ($selectedOffice) {
            $vacancyQuery->where('office_id', $selectedOffice->id);
        }

        $vacancies = $vacancyQuery->orderBy('office_id')
            ->orderBy('position_id')
            ->orderByDesc('date_arose')
            ->get();

        // Group vacancies by office for summary
        $vacanciesByOffice = $vacancies->groupBy('office_id');

        // Calculate pipeline summary statistics
        $pipelineStats = [
            'vacancies' => $vacancies->sum('num_of_vacancies'),
            'applicants' => $vacancies->sum('num_of_applicants'),
            'total_applicants' => $vacancies->sum('num_of_applicants'),
            'shortlisted' => $vacancies->sum('num_of_shortlisted'),
            'interviewed' => $vacancies->whereIn('interview_status', ['Completed', 'In Progress'])->sum('num_of_shortlisted'),
            'selected' => $vacancies->whereNotNull('selected_candidate')->where('selected_candidate', '!=', '')->count(),
            'offers_issued' => $vacancies->whereIn('offer_status', ['Accepted', 'Pending'])->count(),
            'reported' => $vacancies->whereNotNull('actual_reporting_date')->count(),
        ];

        // For each vacancy, calculate pipeline stages
        $vacanciesWithPipeline = $vacancies->map(function ($vacancy) {
            $numVacancies = $vacancy->num_of_vacancies ?? 0;
            $applicants = $vacancy->num_of_applicants ?? 0;
            $shortlisted = $vacancy->num_of_shortlisted ?? 0;
            $interviewStatus = $vacancy->interview_status ?? 'Not Started';
            $selectedCandidate = $vacancy->selected_candidate;
            $offerStatus = $vacancy->offer_status ?? 'Not Made';
            $actualReporting = $vacancy->actual_reporting_date;

            // Determine interview count based on interview status
            $interviewed = 0;
            if (in_array($interviewStatus, ['Completed', 'In Progress'])) {
                $interviewed = min($shortlisted, $numVacancies);
            }

            // Determine selected count
            $selected = $selectedCandidate && $selectedCandidate !== '' ? 1 : 0;

            // Determine offers issued
            $offersIssued = in_array($offerStatus, ['Accepted', 'Pending']) ? 1 : 0;

            // Determine reported
            $reported = $actualReporting ? 1 : 0;

            return [
                'vacancy' => $vacancy,
                'pipeline' => [
                    'vacancies' => $numVacancies,
                    'applicants' => $applicants,
                    'shortlisted' => $shortlisted,
                    'interviewed' => $interviewed,
                    'selected' => $selected,
                    'offers_issued' => $offersIssued,
                    'reported' => $reported,
                ],
                'conversion_rates' => [
                    'application_to_shortlist' => $applicants > 0 ? round(($shortlisted / $applicants) * 100, 1) : 0,
                    'shortlist_to_interview' => $shortlisted > 0 ? round(($interviewed / $shortlisted) * 100, 1) : 0,
                    'interview_to_select' => $interviewed > 0 ? round(($selected / $interviewed) * 100, 1) : 0,
                    'select_to_offer' => $selected > 0 ? round(($offersIssued / $selected) * 100, 1) : 0,
                    'offer_to_report' => $offersIssued > 0 ? round(($reported / $offersIssued) * 100, 1) : 0,
                ],
            ];
        });

        return view('goa.recruitment-pipeline', compact(
            'offices', 'positions', 'selectedOffice', 'selectedOfficeId', 'vacanciesWithPipeline', 'pipelineStats', 'activeTab'
        ));
    }

    /**
     * Compare approved capacity against current personnel for one scope
     * (a position within a branch, or the whole branch).
     *
     * @param  int|float|null  $approvedCapacity
     * @param  int|float|null  $currentPersonnel
     * @return array
     */
    protected function buildBranchCapacityMetrics($approvedCapacity, $currentPersonnel)
    {
        $approvedCapacity = max((int) $approvedCapacity, 0);
        $currentPersonnel = max((int) $currentPersonnel, 0);
        $vacancy = max($approvedCapacity - $currentPersonnel, 0);

        return [
            'approved' => $approvedCapacity,
            'current' => $currentPersonnel,
            'vacancy' => $vacancy,
            'surplus' => max($currentPersonnel - $approvedCapacity, 0),
            'staffing_percentage' => $approvedCapacity > 0
                ? round(($currentPersonnel / $approvedCapacity) * 100, 1)
                : null,
            'vacancy_percentage' => $approvedCapacity > 0
                ? round(($vacancy / $approvedCapacity) * 100, 1)
                : 0,
        ];
    }

    /**
     * Save the approved branch capacity (total headcount) for a branch.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBranchCapacity(Request $request)
    {
        $data = $request->validate([
            'office_id' => 'required|integer|exists:offices,id',
            'branch_capacity' => 'required|integer|min:0|max:10000',
        ]);

        Office::whereKey($data['office_id'])->update([
            'branch_capacity' => $data['branch_capacity'],
        ]);

        return redirect()
            ->route('goa.branch-staffing-capacity', ['office_id' => $data['office_id'], 'tab' => 'setup'])
            ->with('success', 'Approved staffing capacity updated successfully.');
    }

    /**
     * Create a vacancy record (with its recruitment tracking details).
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVacancy(Request $request)
    {
        $payload = $this->vacancyPayload($request);

        Vacancy::create($payload);

        return redirect()
            ->route('goa.branch-staffing-capacity', ['office_id' => $payload['office_id'], 'tab' => 'vacancies'])
            ->with('success', 'Vacancy record created successfully.');
    }

    /**
     * Update the recruitment progress of an existing vacancy record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVacancy(Request $request, $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $payload = $this->vacancyPayload($request);

        $vacancy->update($payload);

        return redirect()
            ->route('goa.branch-staffing-capacity', ['office_id' => $payload['office_id'], 'tab' => 'vacancies'])
            ->with('success', 'Vacancy record updated successfully.');
    }

    /**
     * Remove a vacancy record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyVacancy($id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $officeId = $vacancy->office_id;

        $vacancy->delete();

        return redirect()
            ->route('goa.branch-staffing-capacity', ['office_id' => $officeId, 'tab' => 'vacancies'])
            ->with('success', 'Vacancy record removed successfully.');
    }

    /**
     * Validate and normalise the vacancy form input.
     *
     * @return array
     */
    protected function vacancyPayload(Request $request)
    {
        $data = $request->validate([
            'office_id' => 'required|integer|exists:offices,id',
            'position_id' => 'required|integer|exists:job_positions,id',
            'num_of_vacancies' => 'nullable|integer|min:0|max:1000',
            'date_arose' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
            'recruitment_status' => 'nullable|string|max:50',
            'num_of_applicants' => 'nullable|integer|min:0|max:100000',
            'num_of_shortlisted' => 'nullable|integer|min:0|max:100000',
            'interview_status' => 'nullable|string|max:50',
            'selected_candidate' => 'nullable|string|max:255',
            'offer_status' => 'nullable|string|max:50',
            'expected_reporting_date' => 'nullable|date',
            'actual_reporting_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        return [
            'office_id' => $data['office_id'],
            'position_id' => $data['position_id'],
            'num_of_vacancies' => $data['num_of_vacancies'] ?? 0,
            'status' => $data['recruitment_status'] ?? 'Open',
            'date_arose' => $data['date_arose'] ?? null,
            'reason' => $data['reason'] ?? null,
            'recruitment_status' => $data['recruitment_status'] ?? 'Open',
            'num_of_applicants' => $data['num_of_applicants'] ?? 0,
            'num_of_shortlisted' => $data['num_of_shortlisted'] ?? 0,
            'interview_status' => $data['interview_status'] ?? null,
            'selected_candidate' => $data['selected_candidate'] ?? null,
            'offer_status' => $data['offer_status'] ?? null,
            'expected_reporting_date' => $data['expected_reporting_date'] ?? null,
            'actual_reporting_date' => $data['actual_reporting_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    public function removePosition($id)
    {
        // $id is the Vacancy record ID — delete the vacancy listing
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->delete();
        return redirect()->back()->with('success', 'Vacancy removed successfully.');
    }

    public function fillPosition($id)
    {
        // $id is the Vacancy record ID — mark position filled and remove the vacancy
        $vacancy = Vacancy::with('position')->findOrFail($id);
        if ($vacancy->position) {
            $vacancy->position->update(['is_vacant' => 0, 'num_of_vacancies' => 0]);
        }
        $vacancy->delete();
        return redirect()->back()->with('success', 'Position filled successfully.');
    }

    public function showPosition($id)
    {
        $position = Position::with('department')->findOrFail($id);
        return response()->json([
            'id'               => $position->id,
            'name'             => $position->name,
            'department'       => $position->department ? $position->department->name : 'N/A',
            'status'           => $position->status,
            'job_description'  => $position->job_description,
            'posted_date'      => $position->posted_date ? $position->posted_date->format('Y-m-d') : null,
            'date_added'       => $position->date_added ? $position->date_added->format('Y-m-d') : null,
            'is_vacant'        => $position->is_vacant,
            'num_of_vacancies' => $position->num_of_vacancies,
            'num_of_active'    => $position->num_of_active,
        ]);
    }

    public function assignPosition(Request $request, $user)
    {
        
        $member = \App\Models\User::where('id',$user->id)->first();
        $member->position_id = $request->position_id;
        $member->save();

        return redirect()->back()->with('success', 'Position assigned to ' . $member->first_name . ' ' . $member->last_name . ' successfully.');
    }
}
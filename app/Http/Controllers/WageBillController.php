<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\WageBill;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use App\Models\WageBillAuditLog;
use Illuminate\Support\Facades\DB;
use App\Models\WageBillApproval;
use App\Models\WageBillSubmission;
use App\Exports\ApprovedWageBillExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllBranchesApprovedWageBillExport;
use App\Exports\ApprovedWageBillPayeExport;
use App\Exports\ApprovedWageBillNapsaExport;
use App\Exports\ApprovedWageBillAccessBankExport;
use App\Models\DepartmentalWageBill;
use Illuminate\Pagination\LengthAwarePaginator;


class WageBillController extends Controller
{
    /**
     * Display the wage bill overview.
     */
  
public function index(Request $request)
{
    $user = Sentinel::getUser();

    if (!$user) {
        abort(403, 'You are not authorized to access payroll.');
    }

    // Determine the user's role.
    $isAdminOrRisk = $user->roles()
        ->where('roles.id', 1)
        ->exists();

    $isBranchManager = $user->roles()
        ->where('roles.id', 4)
        ->exists();

    if (!$isAdminOrRisk && !$isBranchManager) {
        abort(403, 'You are not authorized to access wage bills.');
    }

    /*
     * Branch access:
     * Role 1: all branches.
     * Role 4: own branch only.
     */
    if ($isAdminOrRisk) {
        $offices = Office::where('active', 1)
            ->orderBy('name')
            ->get();

        $allowedBranchIds = $offices->pluck('id')->toArray();
    } else {
        if (!$user->office_id) {
            abort(403, 'Your account is not assigned to a branch.');
        }

        $office = Office::where('id', $user->office_id)
            ->where('active', 1)
            ->first();

        if (!$office) {
            abort(403, 'Your assigned branch is unavailable.');
        }

        $offices = collect([$office]);
        $allowedBranchIds = [$office->id];
    }

    // Validate filters.
    $request->validate([
        'branch_id' => 'nullable|integer',
        'cycle_start' => [
            'nullable',
            'date_format:Y-m-d',
        ],
    ]);

    // Branch managers cannot select another branch.
    $selectedBranchId = $isBranchManager
        ? $user->office_id
        : $request->input('branch_id');

    if (
        $selectedBranchId &&
        !in_array((int) $selectedBranchId, $allowedBranchIds)
    ) {
        abort(403, 'You cannot view wage bills for this branch.');
    }

    /*
     * Office 67 is reserved for departmental payroll.
     * It must not be loaded from the branch wage_bills table.
     */
    $branchIdsForPayroll = array_values(array_filter(
        $allowedBranchIds,
        function ($id) {
            return (int) $id !== 67;
        }
    ));

    /*
     * Determine which payroll sources should be included.
     */
    $includeDepartmental = $isAdminOrRisk &&
        (
            !$selectedBranchId ||
            (int) $selectedBranchId === 67
        );

    $includeBranchPayroll = !$selectedBranchId ||
        (int) $selectedBranchId !== 67;

    /*
     * Get available payroll cycles from both sources.
     */
    $branchCycles = collect();

    if ($includeBranchPayroll) {
        $branchCycleQuery = WageBill::query()
            ->whereIn('branch_id', $branchIdsForPayroll);

        if ($selectedBranchId) {
            $branchCycleQuery->where(
                'branch_id',
                $selectedBranchId
            );
        }

        $branchCycles = $branchCycleQuery
            ->select('cycle_start')
            ->distinct()
            ->pluck('cycle_start');
    }

    $departmentalCycles = collect();

    if ($includeDepartmental) {
        $departmentalCycles = DepartmentalWageBill::query()
            ->select('cycle_start')
            ->distinct()
            ->pluck('cycle_start');
    }

    $cycles = $branchCycles
        ->merge($departmentalCycles)
        ->map(function ($cycle) {
            return Carbon::parse($cycle)->format('Y-m-d');
        })
        ->unique()
        ->sortDesc()
        ->values();

    // Use selected cycle, otherwise the latest available cycle.
    $selectedCycle = $request->input('cycle_start');

    if (!$selectedCycle && $cycles->isNotEmpty()) {
        $selectedCycle = $cycles->first();
    }

    /*
     * Retrieve existing branch wage bills.
     */
    $branchWageBills = collect();

    if ($includeBranchPayroll) {
        $branchQuery = WageBill::with([
            'employee',
            'branch',
        ])
            ->whereIn('branch_id', $branchIdsForPayroll);

        if ($selectedBranchId) {
            $branchQuery->where(
                'branch_id',
                $selectedBranchId
            );
        }

        if ($selectedCycle) {
            $branchQuery->whereDate(
                'cycle_start',
                $selectedCycle
            );
        }

        $branchWageBills = $branchQuery->get();
    }

    /*
     * Retrieve departmental wage bills.
     */
    $departmentalWageBills = collect();

    if ($includeDepartmental) {
        $departmentalQuery = DepartmentalWageBill::with([
            'employee',
        ]);

        if ($selectedCycle) {
            $departmentalQuery->whereDate(
                'cycle_start',
                $selectedCycle
            );
        }

        $departmentalWageBills = $departmentalQuery->get();

        // Retrieve office 67 for display in the Branch column.
        $departmentalOffice = Office::find(67);

        foreach ($departmentalWageBills as $departmentalBill) {

            // Map departmental basic salary to the existing salary column.
            $departmentalBill->predicted_salary =
                $departmentalBill->basic_salary;

            // Identify departmental records as office 67.
            $departmentalBill->branch_id = 67;

            // Supply the branch relationship expected by the existing view.
            $departmentalBill->setRelation(
                'branch',
                $departmentalOffice
            );
        }
    }

    /*
     * Combine both payroll sources into one collection.
     */
    $allWageBills = $branchWageBills
        ->merge($departmentalWageBills)
        ->sortBy(function ($bill) {
            return sprintf(
                '%010d-%010d',
                $bill->user_id,
                $bill->id
            );
        })
        ->values();

    /*
     * Calculate totals across all filtered records.
     */
    $totals = [
        'employees' => $allWageBills->count(),

        'predicted_salary' => $allWageBills->sum('predicted_salary'),

        'gross_pay' => $allWageBills->sum('gross_pay'),

        'paye' => $allWageBills->sum('paye'),

        'napsa' => $allWageBills->sum('napsa'),

        'nhima' => $allWageBills->sum('nhima'),

        'allowances' => $allWageBills->sum('allowances'),

        'advance_deductions' => $allWageBills->sum('advance_deductions'),

        'net_pay' => $allWageBills->sum('net_pay'),
    ];

    /*
     * Paginate the combined collection.
     */
    $perPage = 50;

    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $wageBills = new LengthAwarePaginator(
        $allWageBills
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values(),

        $allWageBills->count(),

        $perPage,

        $currentPage,

        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    return view('wage_bills.index', compact(
        'offices',
        'cycles',
        'selectedBranchId',
        'selectedCycle',
        'wageBills',
        'totals',
        'isAdminOrRisk',
        'isBranchManager'
    ));
}


        /**
     * Show the payroll adjustment screen.
     * Only Branch Managers can edit their own branch's draft/returned records.
     */
    public function edit($id)
    {
        $user = Sentinel::getUser();

        if (!$user) {
            abort(403);
        }

        $isBranchManager = $user->roles()
            ->where('roles.id', 4)
            ->exists();

        if (!$isBranchManager) {
            abort(403, 'Only Branch Managers can adjust wage bills.');
        }

        $wageBill = WageBill::with([
            'employee',
            'branch',
            'auditLogs.editor',
        ])
            ->where('id', $id)
            ->where('branch_id', $user->office_id)
            ->firstOrFail();

        if (!in_array($wageBill->status, ['draft', 'returned'])) {
            abort(403, 'This wage bill can no longer be edited.');
        }

        return view('wage_bills.edit', compact('wageBill'));
    }

    /**
     * Save payroll adjustments and record every change in the audit log.
     */
    public function update(Request $request, $id)
    {
        $user = Sentinel::getUser();

        if (!$user) {
            abort(403);
        }

        $isBranchManager = $user->roles()
            ->where('roles.id', 4)
            ->exists();

        if (!$isBranchManager) {
            abort(403, 'Only Branch Managers can adjust wage bills.');
        }

        $validated = $request->validate([
            'gross_pay' => 'required|numeric|min:0',
            'paye' => 'required|numeric|min:0',
            'napsa' => 'required|numeric|min:0',
            'nhima' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'advance_deductions' => 'required|numeric|min:0',
            'reason' => 'required|string|min:5|max:2000',
        ]);

        DB::transaction(function () use ($id, $user, $validated) {

            $wageBill = WageBill::where('id', $id)
                ->where('branch_id', $user->office_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($wageBill->status, ['draft', 'returned'])) {
                abort(403, 'This wage bill can no longer be edited.');
            }

            $editableFields = [
                'gross_pay',
                'paye',
                'napsa',
                'nhima',
                'allowances',
                'advance_deductions',
            ];

            $overrides = $wageBill->manual_overrides ?? [];
            $changes = [];

            foreach ($editableFields as $field) {

                $oldValue = round((float) $wageBill->{$field}, 2);
                $newValue = round((float) $validated[$field], 2);

                if ($oldValue !== $newValue) {

                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];

                    $wageBill->{$field} = $newValue;
                    $overrides[$field] = true;
                }
            }

            if (empty($changes)) {
                return;
            }

            // Net pay is always calculated, never manually entered.
            $oldNetPay = round((float) $wageBill->net_pay, 2);

            $newNetPay = round(
                (float) $wageBill->predicted_salary
                + (float) $wageBill->allowances
                - (float) $wageBill->advance_deductions,
                2
            );

            if ($newNetPay < 0) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'advance_deductions' => 'Advance deductions cannot exceed predicted salary plus allowances.',
    ]);
}

            $wageBill->net_pay = $newNetPay;
            $wageBill->manual_overrides = $overrides;

            $wageBill->save();

            // Audit every manually changed field.
            foreach ($changes as $field => $change) {

                WageBillAuditLog::create([
                    'wage_bill_id' => $wageBill->id,
                    'user_id' => $user->id,
                    'field_name' => $field,
                    'old_value' => $change['old'],
                    'new_value' => $change['new'],
                    'reason' => $validated['reason'],
                ]);
            }

            // Also record the resulting net pay change, if any.
            if ($oldNetPay !== $newNetPay) {

                WageBillAuditLog::create([
                    'wage_bill_id' => $wageBill->id,
                    'user_id' => $user->id,
                    'field_name' => 'net_pay',
                    'old_value' => $oldNetPay,
                    'new_value' => $newNetPay,
                    'reason' => 'Automatically recalculated: ' . $validated['reason'],
                ]);
            }
        });

        return redirect()
            ->route('wage-bills.edit', $id)
            ->with('success', 'Payroll adjustments saved successfully.');
    }


    public function submit(Request $request, $branchId, $cycleStart)
{
    $user = Sentinel::getUser();

    if (!$user) {
        abort(403);
    }

    $isBranchManager = $user->roles()
        ->where('roles.id', 4)
        ->exists();

    if (!$isBranchManager) {
        abort(403, 'Only Branch Managers can submit wage bills.');
    }

    // A Branch Manager can only submit their own branch.
    if ((int) $user->office_id !== (int) $branchId) {
        abort(403, 'You cannot submit a wage bill for another branch.');
    }

    $request->validate([
        'confirmation' => 'required|accepted',
    ]);

    // Validate the cycle date.
    try {
        $cycleDate = Carbon::createFromFormat('Y-m-d', $cycleStart);
    } catch (\Exception $e) {
        abort(404, 'Invalid payroll cycle.');
    }

    if (!$cycleDate || $cycleDate->format('Y-m-d') !== $cycleStart || $cycleDate->day !== 25) {
        abort(404, 'Invalid payroll cycle.');
    }

    DB::transaction(function () use ($branchId, $cycleStart, $user) {

        $wageBills = WageBill::where('branch_id', $branchId)
            ->whereDate('cycle_start', $cycleStart)
            ->lockForUpdate()
            ->get();

        if ($wageBills->isEmpty()) {
            abort(404, 'No wage bill records were found for this cycle.');
        }

        // Only draft or returned wage bills can be submitted.
        $invalidRecords = $wageBills->contains(function ($wageBill) {
            return !in_array($wageBill->status, ['draft', 'returned']);
        });

        if ($invalidRecords) {
            abort(422, 'This wage bill cannot be submitted because some records are not editable.');
        }

        // Make sure the Branch Manager has reviewed all employee records.
        $submissionNumber = WageBillSubmission::where('branch_id', $branchId)
            ->whereDate('cycle_start', $cycleStart)
            ->lockForUpdate()
            ->max('submission_number') + 1;

        $submission = WageBillSubmission::create([
            'branch_id' => $branchId,
            'cycle_start' => $cycleStart,
            'submitted_by' => $user->id,
            'submission_number' => $submissionNumber,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Create separate approval records for this submission.
        foreach (['administration', 'risk'] as $approvalType) {
            WageBillApproval::create([
                'submission_id' => $submission->id,
                'branch_id' => $branchId,
                'cycle_start' => $cycleStart,
                'approval_type' => $approvalType,
                'status' => 'pending',
            ]);
        }

        // Lock all employee payroll records by changing their status.
        WageBill::where('branch_id', $branchId)
            ->whereDate('cycle_start', $cycleStart)
            ->update([
                'status' => 'submitted',
           
            ]);
    });

    return redirect()
        ->route('wage-bills.index', [
            'branch_id' => $branchId,
            'cycle_start' => $cycleStart,
        ])
        ->with('success', 'Wage bill submitted successfully to Administration and Risk.');
}


public function approvals(Request $request)
{
    $user = Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403, 'You are not authorized to access payroll approvals.');
    }

    $request->validate([
        'status' => 'nullable|in:submitted,approved,returned',
        'approval_type' => 'nullable|in:administration,risk',
        'branch_id' => 'nullable|integer',
    ]);

    $query = WageBillSubmission::with([
        'branch',
        'submitter',
        'approvals.approver',
    ]);

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }

    if ($request->filled('approval_type')) {
        $approvalType = $request->approval_type;

        $query->whereHas('approvals', function ($q) use ($approvalType) {
            $q->where('approval_type', $approvalType);
        });
    }

    $submissions = $query
        ->orderByDesc('submitted_at')
        ->paginate(20)
        ->withQueryString();

    $branches = Office::where('active', 1)
        ->orderBy('name')
        ->get();

    $summary = [
        'pending_administration' => WageBillApproval::where('approval_type', 'administration')
            ->where('status', 'pending')
            ->whereHas('submission', function ($q) {
                $q->where('status', 'submitted');
            })
            ->count(),

        'pending_risk' => WageBillApproval::where('approval_type', 'risk')
            ->where('status', 'pending')
            ->whereHas('submission', function ($q) {
                $q->where('status', 'submitted');
            })
            ->count(),

        'approved' => WageBillSubmission::where('status', 'approved')->count(),

        'returned' => WageBillSubmission::where('status', 'returned')->count(),
    ];

    return view('wage_bills.approvals', compact(
        'submissions',
        'branches',
        'summary'
    ));
}


public function approvalShow($id)
{
    $user = Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403);
    }

    $submission = WageBillSubmission::with([
        'branch',
        'submitter',
        'approvals.approver',
    ])->findOrFail($id);

    $wageBills = WageBill::with([
        'employee',
        'auditLogs.editor',
    ])
        ->where('branch_id', $submission->branch_id)
        ->whereDate('cycle_start', $submission->cycle_start)
        ->orderBy('user_id')
        ->get();

    $totals = [
        'employees' => $wageBills->count(),
        'predicted_salary' => $wageBills->sum('predicted_salary'),
        'gross_pay' => $wageBills->sum('gross_pay'),
        'paye' => $wageBills->sum('paye'),
        'napsa' => $wageBills->sum('napsa'),
        'nhima' => $wageBills->sum('nhima'),
        'allowances' => $wageBills->sum('allowances'),
        'advance_deductions' => $wageBills->sum('advance_deductions'),
        'net_pay' => $wageBills->sum('net_pay'),
    ];

    // Map editable payroll fields to their original API values.
    $systemFieldMap = [
        'gross_pay' => 'system_gross_pay',
        'paye' => 'system_paye',
        'napsa' => 'system_napsa',
        'nhima' => 'system_nhima',
        'allowances' => null,
        'advance_deductions' => null,
    ];

    $adjustments = collect();

    foreach ($wageBills as $wageBill) {
        foreach ($systemFieldMap as $field => $systemField) {

            $systemValue = $systemField
                ? (float) $wageBill->{$systemField}
                : 0;

            $currentValue = (float) $wageBill->{$field};
            $difference = round($currentValue - $systemValue, 2);

            if (abs($difference) > 0.009) {
                $adjustments->push([
                    'employee' => $wageBill->employee,
                    'field' => $field,
                    'system_value' => $systemValue,
                    'current_value' => $currentValue,
                    'difference' => $difference,
                    'reason' => null,
                    'editor' => null,
                    'created_at' => null,
                    'type' => 'comparison',
                ]);
            }
        }

        // Include the complete audit history of manual changes.
        foreach ($wageBill->auditLogs as $log) {
            $adjustments->push([
                'employee' => $wageBill->employee,
                'field' => $log->field_name,
                'system_value' => null,
                'current_value' => null,
                'difference' => null,
                'reason' => $log->reason,
                'editor' => $log->editor,
                'created_at' => $log->created_at,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
                'type' => 'audit',
            ]);
        }
    }

    $adjustmentSummary = [
        'employees_adjusted' => $wageBills->filter(function ($wageBill) {
            return !empty($wageBill->manual_overrides);
        })->count(),

        'total_adjustment_records' => $wageBills->sum(function ($wageBill) {
            return $wageBill->auditLogs->count();
        }),

        'total_allowances' => $wageBills->sum('allowances'),
        'total_advance_deductions' => $wageBills->sum('advance_deductions'),
    ];

    return view('wage_bills.approval_show', compact(
        'submission',
        'wageBills',
        'totals',
        'adjustments',
        'adjustmentSummary'
    ));
}


public function approveWageBill(Request $request, $submissionId, $approvalType)
{
    $user = Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403);
    }

    if (!in_array($approvalType, ['administration', 'risk'])) {
        abort(404);
    }

    $validated = $request->validate([
        'comments' => 'nullable|string|max:2000',
    ]);

    DB::transaction(function () use ($submissionId, $approvalType, $user, $validated) {

        $submission = WageBillSubmission::where('id', $submissionId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($submission->status !== 'submitted') {
            abort(422, 'This wage bill is no longer awaiting approval.');
        }

        $approval = WageBillApproval::where('submission_id', $submission->id)
            ->where('approval_type', $approvalType)
            ->lockForUpdate()
            ->firstOrFail();

        if ($approval->status !== 'pending') {
            abort(422, 'This approval has already been actioned.');
        }

        $approval->update([
            'approver_id' => $user->id,
            'status' => 'approved',
            'comments' => $validated['comments'] ?? null,
            'actioned_at' => now(),
        ]);

        $approvals = WageBillApproval::where('submission_id', $submission->id)
            ->get();

        $bothApproved = $approvals->count() === 2 &&
            $approvals->every(function ($item) {
                return $item->status === 'approved';
            });

        if ($bothApproved) {

            $submission->update([
                'status' => 'approved',
            ]);

            WageBill::where('branch_id', $submission->branch_id)
                ->whereDate('cycle_start', $submission->cycle_start)
                ->update([
                    'status' => 'approved',
                ]);
        }
    });

    return redirect()
        ->route('wage-bills.approval-show', $submissionId)
        ->with('success', ucfirst($approvalType) . ' approval recorded successfully.');
}


public function returnWageBill(Request $request, $submissionId, $approvalType)
{
    $user = Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403);
    }

    if (!in_array($approvalType, ['administration', 'risk'])) {
        abort(404);
    }

    $validated = $request->validate([
        'comments' => 'required|string|min:5|max:2000',
    ]);

    DB::transaction(function () use ($submissionId, $approvalType, $user, $validated) {

        $submission = WageBillSubmission::where('id', $submissionId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($submission->status !== 'submitted') {
            abort(422, 'This wage bill is no longer awaiting approval.');
        }

        $approval = WageBillApproval::where('submission_id', $submission->id)
            ->where('approval_type', $approvalType)
            ->lockForUpdate()
            ->firstOrFail();

        if ($approval->status !== 'pending') {
            abort(422, 'This approval has already been actioned.');
        }

        $approval->update([
            'approver_id' => $user->id,
            'status' => 'returned',
            'comments' => $validated['comments'],
            'actioned_at' => now(),
        ]);

        // Close the other pending approval because the entire
        // submission is being returned to the Branch Manager.
        WageBillApproval::where('submission_id', $submission->id)
            ->where('id', '!=', $approval->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'returned',
                'comments' => 'Automatically closed because the submission was returned by ' . $approvalType . '.',
            ]);

        $submission->update([
            'status' => 'returned',
            'comments' => $validated['comments'],
        ]);

        WageBill::where('branch_id', $submission->branch_id)
            ->whereDate('cycle_start', $submission->cycle_start)
            ->update([
                'status' => 'returned',
            ]);
    });

    return redirect()
        ->route('wage-bills.approval-show', $submissionId)
        ->with('success', 'Wage bill returned to the Branch Manager for corrections.');
}


public function exportApprovedWageBill($branchId, $cycleStart)
{
    $user = Sentinel::getUser();

    if (!$user) {
        abort(403);
    }

    $isBranchManager = $user->roles()
        ->where('roles.id', 4)
        ->exists();

    if (!$isBranchManager) {
        abort(403, 'Only Branch Managers can download approved wage bills.');
    }

    // Managers can only download their own branch's report.
    if ((int) $user->office_id !== (int) $branchId) {
        abort(403, 'You cannot download another branch\'s wage bill.');
    }

    // Validate the payroll cycle.
    try {
        $cycleDate = Carbon::createFromFormat('Y-m-d', $cycleStart);
    } catch (\Exception $e) {
        abort(404, 'Invalid payroll cycle.');
    }

    if (
        !$cycleDate ||
        $cycleDate->format('Y-m-d') !== $cycleStart ||
        $cycleDate->day !== 25
    ) {
        abort(404, 'Invalid payroll cycle.');
    }

    $wageBills = WageBill::where('branch_id', $branchId)
        ->whereDate('cycle_start', $cycleStart);

    if (!$wageBills->exists()) {
        abort(404, 'No wage bill was found for this cycle.');
    }

    if ($wageBills->where('status', '!=', 'approved')->exists()) {
        abort(403, 'This wage bill has not been fully approved yet.');
    }

    $fileName = 'Approved_Wage_Bill_Branch_' .
        $branchId . '_' . $cycleStart . '.xlsx';

    return Excel::download(
        new ApprovedWageBillExport($branchId, $cycleStart),
        $fileName
    );
}


public function exportAllBranchesApproved(Request $request)
{
    $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403, 'You are not authorized to export this report.');
    }

    $validated = $request->validate([
        'cycle_start' => ['required', 'date_format:Y-m-d'],
    ]);

    $cycleStart = Carbon::createFromFormat(
        'Y-m-d',
        $validated['cycle_start']
    );

    if ($cycleStart->day !== 25) {
        return back()->with('error', 'Invalid payroll cycle selected.');
    }

    $hasApprovedRecords = \App\Models\WageBill::whereDate(
        'cycle_start',
        $cycleStart->format('Y-m-d')
    )
        ->where('status', 'approved')
        ->exists();

    if (!$hasApprovedRecords) {
        return back()->with(
            'error',
            'There are no approved wage bills for the selected payroll cycle.'
        );
    }

    $fileName = 'All_Branches_Approved_Wage_Bill_' .
        $cycleStart->format('Y-m-d') . '.xlsx';

    return Excel::download(
        new AllBranchesApprovedWageBillExport(
            $cycleStart->format('Y-m-d')
        ),
        $fileName
    );
}



public function exportPayeReport(Request $request)
{
    $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403, 'You are not authorized to export this report.');
    }

    $validated = $request->validate([
        'cycle_start' => ['required', 'date_format:Y-m-d'],
    ]);

    $cycleStart = \Carbon\Carbon::createFromFormat(
        'Y-m-d',
        $validated['cycle_start']
    );

    if ($cycleStart->day !== 25) {
        return back()->with('error', 'Invalid payroll cycle selected.');
    }

    $hasApprovedRecords = \App\Models\WageBill::whereDate(
        'cycle_start',
        $cycleStart->format('Y-m-d')
    )
        ->where('status', 'approved')
        ->exists();

    if (!$hasApprovedRecords) {
        return back()->with(
            'error',
            'There are no approved wage bills for the selected payroll cycle.'
        );
    }

    $fileName = 'PAYE_Report_' .
        $cycleStart->format('Y-m-d') . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(
        new ApprovedWageBillPayeExport(
            $cycleStart->format('Y-m-d')
        ),
        $fileName
    );
}


public function exportNapsaReport(Request $request)
{
    $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403, 'You are not authorized to export this report.');
    }

    $validated = $request->validate([
        'cycle_start' => ['required', 'date_format:Y-m-d'],
    ]);

    $cycleStart = \Carbon\Carbon::createFromFormat(
        'Y-m-d',
        $validated['cycle_start']
    );

    if ($cycleStart->day !== 25) {
        return back()->with('error', 'Invalid payroll cycle selected.');
    }

    $hasApprovedRecords = \App\Models\WageBill::whereDate(
        'cycle_start',
        $cycleStart->format('Y-m-d')
    )
        ->where('status', 'approved')
        ->exists();

    if (!$hasApprovedRecords) {
        return back()->with(
            'error',
            'There are no approved wage bills for the selected payroll cycle.'
        );
    }

    $fileName = 'NAPSA_Report_' .
        $cycleStart->format('Y-m-d') . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(
        new ApprovedWageBillNapsaExport(
            $cycleStart->format('Y-m-d')
        ),
        $fileName
    );
}



public function exportAccessBankReport(Request $request)
{


    $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();

    if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
        abort(403, 'You are not authorized to export this report.');
    }

    $validated = $request->validate([
        'cycle_start' => ['required', 'date_format:Y-m-d'],
    ]);

    $cycleStart = \Carbon\Carbon::createFromFormat(
        'Y-m-d',
        $validated['cycle_start']
    );

    if ($cycleStart->day !== 25) {
        return back()->with('error', 'Invalid payroll cycle selected.');
    }

    $hasApprovedRecords = \App\Models\WageBill::whereDate(
        'cycle_start',
        $cycleStart->format('Y-m-d')
    )
        ->where('status', 'approved')
        ->exists();

    if (!$hasApprovedRecords) {
        return back()->with(
            'error',
            'There are no approved wage bills for the selected payroll cycle.'
        );
    }

    $fileName = 'Access_Bank_Payroll_' .
        $cycleStart->format('Y-m-d') . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(
        new ApprovedWageBillAccessBankExport(
            $cycleStart->format('Y-m-d')
        ),
        $fileName
    );
}





}
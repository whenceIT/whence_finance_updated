<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Advance;
use App\Models\DepartmentalWageBill;
use App\Models\DepartmentalWageBillAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class DepartmentalWageBillController extends Controller
{
    /**
     * Only Administration (role ID 1) can prepare departmental payroll.
     */
    private function authorizePayroll()
    {
        $user = Sentinel::getUser();

        if (!$user || !$user->roles()->where('roles.id', 1)->exists()) {
            abort(403, 'You are not authorised to access departmental payroll.');
        }

        return $user;
    }

    /**
     * Default payroll cycle: 25th to 24th.
     */
    private function getDefaultCycleStart()
    {
        $today = Carbon::today();

        if ($today->day >= 25) {
            return $today->copy()->day(25);
        }

        return $today->copy()->subMonthNoOverflow()->day(25);
    }

    /**
     * Calculate PAYE using the 2026 Zambian monthly tax bands.
     */
    private function calculatePaye($grossPay)
    {
        $grossPay = max(0, (float) $grossPay);

        if ($grossPay <= 5100) {
            return 0;
        }

        if ($grossPay <= 7100) {
            return round(($grossPay - 5100) * 0.20, 2);
        }

        if ($grossPay <= 9200) {
            return round(
                400 + (($grossPay - 7100) * 0.30),
                2
            );
        }

        return round(
            1030 + (($grossPay - 9200) * 0.37),
            2
        );
    }

    /**
     * Calculate employee NAPSA.
     *
     * 5% of gross earnings, subject to the 2026
     * monthly earnings ceiling of K37,236.
     */
    private function calculateNapsa($grossPay)
    {
        $napsaCeiling = 37236;
        $employeeRate = 0.05;

        $contributionBase = min(
            max(0, (float) $grossPay),
            $napsaCeiling
        );

        return round($contributionBase * $employeeRate, 2);
    }

    /**
     * Calculate employee NHIMA.
     *
     * CURRENT APPROACH:
     * 1% of gross pay.
     *
     * FUTURE REGULATION:
     * Change the calculation base to basic salary
     * once the published regulation is finalised.
     */
    private function calculateNhima($basicSalary, $grossPay)
    {
        $nhimaRate = 0.01;

        // Current approach: 1% of gross pay.
        $calculationBase = $grossPay;

        // Future regulation:
        // $calculationBase = $basicSalary;

        return round(
            max(0, (float) $calculationBase) * $nhimaRate,
            2
        );
    }

    /**
     * Retrieve the total pending advance instalments
     * for one employee.
     */
    private function getEmployeeAdvanceDeductions($userId)
    {
        return round(
            (float) Advance::where('user_id', $userId)
                ->where('status', 'pending')
                ->sum('installment_amount'),
            2
        );
    }

    /**
     * Calculate all payroll figures.
     */
    private function calculatePayrollFigures(
        $basicSalary,
        $allowances,
        $advanceDeductions
    ) {
        $basicSalary = round((float) $basicSalary, 2);
        $allowances = round((float) $allowances, 2);
        $advanceDeductions = round((float) $advanceDeductions, 2);

        $grossPay = round($basicSalary + $allowances, 2);

        $paye = $this->calculatePaye($grossPay);
        $napsa = $this->calculateNapsa($grossPay);
        $nhima = $this->calculateNhima($basicSalary, $grossPay);

        $netPay = round(
            $grossPay - $paye - $napsa - $nhima - $advanceDeductions,
            2
        );

        return [
            'gross_pay' => $grossPay,
            'paye' => $paye,
            'napsa' => $napsa,
            'nhima' => $nhima,
            'advance_deductions' => $advanceDeductions,
            'net_pay' => $netPay,
        ];
    }

    /**
     * Display departmental payroll.
     */
    public function index(Request $request)
    {
        $this->authorizePayroll();

        $request->validate([
            'cycle_start' => 'nullable|date_format:Y-m-d',
        ]);

        if ($request->filled('cycle_start')) {
            $cycleStart = Carbon::createFromFormat(
                'Y-m-d',
                $request->cycle_start
            );
        } else {
            $cycleStart = $this->getDefaultCycleStart();
        }

        if ($cycleStart->day !== 25) {
            return redirect()
                ->route('departmental-wage-bills.index')
                ->with('error', 'The payroll cycle must start on the 25th of a month.');
        }

        $cycleStart = $cycleStart->toDateString();

        // Departmental employees are identified by office_id = 67.
        $employees = User::where('office_id', 67)
            ->orderBy('first_name')
            ->get();

        // Retrieve all pending advance instalments for departmental employees.
        $advanceDeductions = Advance::where('status', 'pending')
            ->whereIn('user_id', $employees->pluck('id'))
            ->select(
                'user_id',
                DB::raw('SUM(installment_amount) as total_installments')
            )
            ->groupBy('user_id')
            ->pluck('total_installments', 'user_id');

        // Retrieve payroll records for the selected cycle.
        $wageBills = DepartmentalWageBill::where(
            'cycle_start',
            $cycleStart
        )
            ->get()
            ->keyBy('user_id');

        return view(
            'departmental_wage_bills.index',
            compact(
                'employees',
                'wageBills',
                'advanceDeductions',
                'cycleStart'
            )
        );
    }

    /**
     * Save or update an individual employee's departmental payroll.
     */
    public function store(Request $request)
    {
        $admin = $this->authorizePayroll();

        $validated = $request->validate([
            'user_id' => 'required|integer',
            'cycle_start' => 'required|date_format:Y-m-d',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:2000',
        ]);

        $cycleStart = Carbon::createFromFormat(
            'Y-m-d',
            $validated['cycle_start']
        );

        if ($cycleStart->day !== 25) {
            return back()
                ->withInput()
                ->with('error', 'The payroll cycle must start on the 25th of a month.');
        }

        $employee = User::where('id', $validated['user_id'])
            ->where('office_id', 67)
            ->first();

        if (!$employee) {
            abort(404, 'Departmental employee not found.');
        }

        $basicSalary = round((float) $validated['basic_salary'], 2);
        $allowances = round((float) ($validated['allowances'] ?? 0), 2);

        // Retrieve pending advance instalments directly from the database.
        // No advance amount is accepted from the browser.
        $advanceDeductions = $this->getEmployeeAdvanceDeductions(
            $employee->id
        );

        // Calculate all payroll figures on the server.
        $figures = $this->calculatePayrollFigures(
            $basicSalary,
            $allowances,
            $advanceDeductions
        );

        if ($figures['net_pay'] < 0) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The deductions cannot exceed the employee gross pay.'
                );
        }

        DB::transaction(function () use (
            $validated,
            $admin,
            $basicSalary,
            $allowances,
            $figures
        ) {
            $wageBill = DepartmentalWageBill::where(
                'user_id',
                $validated['user_id']
            )
                ->where('cycle_start', $validated['cycle_start'])
                ->lockForUpdate()
                ->first();

            if ($wageBill && $wageBill->status !== 'draft') {
                abort(
                    403,
                    'This payroll record is no longer editable.'
                );
            }

            $values = [
                'basic_salary' => $basicSalary,
                'allowances' => $allowances,
                'gross_pay' => $figures['gross_pay'],
                'paye' => $figures['paye'],
                'napsa' => $figures['napsa'],
                'nhima' => $figures['nhima'],
                'advance_deductions' => $figures['advance_deductions'],
                'net_pay' => $figures['net_pay'],
            ];

            if (!$wageBill) {
                $wageBill = DepartmentalWageBill::create(
                    array_merge(
                        [
                            'user_id' => $validated['user_id'],
                            'cycle_start' => $validated['cycle_start'],
                            'status' => 'draft',
                        ],
                        $values
                    )
                );

                // Record the initial values in the audit log.
                foreach ($values as $field => $newValue) {
                    DepartmentalWageBillAuditLog::create([
                        'departmental_wage_bill_id' => $wageBill->id,
                        'user_id' => $admin->id,
                        'field_name' => $field,
                        'old_value' => null,
                        'new_value' => $newValue,
                        'reason' => $validated['reason'],
                    ]);
                }
            } else {
                // Record only fields that have changed.
                foreach ($values as $field => $newValue) {
                    $oldValue = $wageBill->{$field};

                    if ((float) $oldValue !== (float) $newValue) {
                        DepartmentalWageBillAuditLog::create([
                            'departmental_wage_bill_id' => $wageBill->id,
                            'user_id' => $admin->id,
                            'field_name' => $field,
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'reason' => $validated['reason'],
                        ]);
                    }
                }

                $wageBill->update($values);
            }
        });

        return redirect()
            ->route('departmental-wage-bills.index', [
                'cycle_start' => $validated['cycle_start'],
            ])
            ->with('success', 'Departmental payroll saved successfully.');
    }



/**
 * Submit the complete departmental payroll for approval.
 */
public function submit(Request $request)
{
    $admin = $this->authorizePayroll();

    $validated = $request->validate([
        'cycle_start' => 'required|date_format:Y-m-d',
        'comments' => 'nullable|string|max:2000',
    ]);

    $cycleStart = Carbon::createFromFormat(
        'Y-m-d',
        $validated['cycle_start']
    );

    if ($cycleStart->day !== 25) {
        return back()->with(
            'error',
            'The payroll cycle must start on the 25th of a month.'
        );
    }

    $cycleStart = $cycleStart->toDateString();

    try {
        DB::transaction(function () use (
            $cycleStart,
            $validated,
            $admin
        ) {
            // Get every employee belonging to the department.
            $employees = User::where('office_id', 67)
                ->orderBy('id')
                ->get(['id', 'first_name', 'last_name']);

            if ($employees->isEmpty()) {
                throw new \Exception(
                    'No departmental employees were found.'
                );
            }

            $employeeIds = $employees->pluck('id');

            // Lock the payroll records for this cycle.
            $wageBills = DepartmentalWageBill::where(
                'cycle_start',
                $cycleStart
            )
                ->whereIn('user_id', $employeeIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('user_id');

            // Identify employees without payroll records.
            $missingEmployees = $employees->filter(function ($employee) use (
                $wageBills
            ) {
                return !$wageBills->has($employee->id);
            });

            if ($missingEmployees->isNotEmpty()) {
                $names = $missingEmployees->map(function ($employee) {
                    return trim(
                        $employee->first_name . ' ' . $employee->last_name
                    );
                })->implode(', ');

                throw new \Exception(
                    'Payroll cannot be submitted. The following employees '
                    . 'have no payroll records: ' . $names
                );
            }

            // Only draft payroll records can be submitted.
            $nonDraftRecords = $wageBills->filter(function ($wageBill) {
                return $wageBill->status !== 'draft';
            });

            if ($nonDraftRecords->isNotEmpty()) {
                throw new \Exception(
                    'Payroll cannot be submitted because one or more '
                    . 'employee records are not in draft status.'
                );
            }

            // Determine the next submission number for this cycle.
            $lastSubmissionNumber = DB::table(
                'departmental_wage_bill_submissions'
            )
                ->where('cycle_start', $cycleStart)
                ->max('submission_number');

            $submissionNumber = ((int) $lastSubmissionNumber) + 1;

            $now = Carbon::now();

            // Create the submission.
            $submissionId = DB::table(
                'departmental_wage_bill_submissions'
            )->insertGetId([
                'cycle_start' => $cycleStart,
                'submitted_by' => $admin->id,
                'submission_number' => $submissionNumber,
                'status' => 'submitted',
                'comments' => $validated['comments'] ?? null,
                'submitted_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Create independent Administration and Risk approvals.
            DB::table('departmental_wage_bill_approvals')->insert([
                [
                    'submission_id' => $submissionId,
                    'approval_type' => 'administration',
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'submission_id' => $submissionId,
                    'approval_type' => 'risk',
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            // Lock all employee payroll records by changing their status.
            DepartmentalWageBill::where('cycle_start', $cycleStart)
                ->whereIn('user_id', $employeeIds)
                ->update([
                    'status' => 'submitted',
                    'updated_at' => $now,
                ]);
        });

    } catch (\Exception $e) {
        return back()
            ->with('error', $e->getMessage());
    }

    return redirect()
        ->route('departmental-wage-bills.index', [
            'cycle_start' => $cycleStart,
        ])
        ->with(
            'success',
            'Departmental payroll submitted successfully for Administration and Risk approval.'
        );
}

}

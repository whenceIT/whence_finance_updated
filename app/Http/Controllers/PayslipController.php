<?php

namespace App\Http\Controllers;

use App\Models\WageBill;
use App\Models\Office;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class PayslipController extends Controller
{
    /**
     * Download an individual employee payslip.
     */
    public function download($id)
    {
        $user = Sentinel::getUser();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Check user roles.
        $isAdminRisk = $user->roles()
            ->where('roles.id', 1)
            ->exists();

        $isBranchManager = $user->roles()
            ->where('roles.id', 4)
            ->exists();

        if (!$isAdminRisk && !$isBranchManager) {
            abort(403, 'You do not have permission to download payslips.');
        }

        // Retrieve the employee's wage bill.
        $wageBill = WageBill::with(['employee', 'branch'])
            ->findOrFail($id);

        // Branch Managers can only access their own branch.
        if (
            $isBranchManager &&
            (int) $user->office_id !== (int) $wageBill->branch_id
        ) {
            abort(403, 'You cannot access payslips for another branch.');
        }

        // Only approved wage bills can generate payslips.
        if ($wageBill->status !== 'approved') {
            abort(403, 'Payslips are only available for fully approved wage bills.');
        }

        // Confirm the entire branch wage bill has been approved.
        $allApproved = WageBill::where('branch_id', $wageBill->branch_id)
            ->whereDate('cycle_start', $wageBill->cycle_start)
            ->where('status', '!=', 'approved')
            ->doesntExist();

        if (!$allApproved) {
            abort(403, 'The entire wage bill has not yet been approved.');
        }

        // Calculate payroll cycle end date (25th to 24th).
        $cycleStart = Carbon::parse($wageBill->cycle_start);
        $cycleEnd = $cycleStart->copy()->addMonth()->day(24);

        $data = [
            'wageBill' => $wageBill,
            'employee' => $wageBill->employee,
            'branch' => $wageBill->branch,
            'cycleStart' => $cycleStart,
            'cycleEnd' => $cycleEnd,
            'generatedAt' => now(),
        ];

        // Generate PDF.
        $pdf = Pdf::loadView('wage_bills.payslip', $data)
            ->setPaper('a4', 'portrait');

        $employeeName = $wageBill->employee
            ? $wageBill->employee->first_name . '_' . $wageBill->employee->last_name
            : 'Employee_' . $wageBill->user_id;

        $fileName = 'Payslip_' .
            preg_replace('/[^A-Za-z0-9_-]/', '_', $employeeName) .
            '_' . $cycleStart->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
}
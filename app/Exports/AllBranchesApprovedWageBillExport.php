<?php
namespace App\Exports;

use App\Models\WageBill;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllBranchesApprovedWageBillExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $cycleStart;

    public function __construct($cycleStart)
    {
        $this->cycleStart = $cycleStart;
    }

    public function collection()
    {
        $wageBills = WageBill::with(['employee', 'branch'])
            ->whereDate('cycle_start', $this->cycleStart)
            ->where('status', 'approved')
            ->orderBy('branch_id')
            ->orderBy('user_id')
            ->get();

        $rows = $wageBills->map(function ($wageBill) {
            return [
                'Branch' => $wageBill->branch->name ?? 'N/A',
                'Employee ID' => $wageBill->user_id,
                'Employee Name' => trim(
                    ($wageBill->employee->first_name ?? '') . ' ' .
                    ($wageBill->employee->last_name ?? '')
                ),
                'Predicted Salary' => (float) $wageBill->predicted_salary,
                'Gross Pay' => (float) $wageBill->gross_pay,
                'PAYE' => (float) $wageBill->paye,
                'NAPSA' => (float) $wageBill->napsa,
                'NHIMA' => (float) $wageBill->nhima,
                'Allowances' => (float) $wageBill->allowances,
                'Advance Deductions' => (float) $wageBill->advance_deductions,
                'Net Pay' => (float) $wageBill->net_pay,
            ];
        });

        if ($rows->isNotEmpty()) {
            $rows->push([
                'Branch' => 'GRAND TOTAL',
                'Employee ID' => '',
                'Employee Name' => '',
                'Predicted Salary' => $wageBills->sum('predicted_salary'),
                'Gross Pay' => $wageBills->sum('gross_pay'),
                'PAYE' => $wageBills->sum('paye'),
                'NAPSA' => $wageBills->sum('napsa'),
                'NHIMA' => $wageBills->sum('nhima'),
                'Allowances' => $wageBills->sum('allowances'),
                'Advance Deductions' => $wageBills->sum('advance_deductions'),
                'Net Pay' => $wageBills->sum('net_pay'),
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Branch',
            'Employee ID',
            'Employee Name',
            'Predicted Salary',
            'Gross Pay',
            'PAYE',
            'NAPSA',
            'NHIMA',
            'Allowances',
            'Advance Deductions',
            'Net Pay',
        ];
    }
}

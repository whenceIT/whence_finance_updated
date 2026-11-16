<?php

namespace App\Exports;

use App\Models\WageBill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApprovedWageBillExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $branchId;
    protected $cycleStart;

    public function __construct($branchId, $cycleStart)
    {
        $this->branchId = $branchId;
        $this->cycleStart = $cycleStart;
    }

    public function collection()
    {
        $wageBills = WageBill::with('employee')
            ->where('branch_id', $this->branchId)
            ->whereDate('cycle_start', $this->cycleStart)
            ->where('status', 'approved')
            ->orderBy('user_id')
            ->get();

        $rows = $wageBills->map(function ($wageBill) {
            return [
                'employee_id' => $wageBill->user_id,
                'employee_name' => $wageBill->employee
                    ? $wageBill->employee->first_name . ' ' . $wageBill->employee->last_name
                    : 'Unknown Employee',
                'predicted_salary' => (float) $wageBill->predicted_salary,
                'gross_pay' => (float) $wageBill->gross_pay,
                'paye' => (float) $wageBill->paye,
                'napsa' => (float) $wageBill->napsa,
                'nhima' => (float) $wageBill->nhima,
                'allowances' => (float) $wageBill->allowances,
                'advance_deductions' => (float) $wageBill->advance_deductions,
                'net_pay' => (float) $wageBill->net_pay,
            ];
        });

        // Add totals at the bottom of the report.
        $rows->push([
            'employee_id' => '',
            'employee_name' => 'TOTAL',
            'predicted_salary' => $wageBills->sum('predicted_salary'),
            'gross_pay' => $wageBills->sum('gross_pay'),
            'paye' => $wageBills->sum('paye'),
            'napsa' => $wageBills->sum('napsa'),
            'nhima' => $wageBills->sum('nhima'),
            'allowances' => $wageBills->sum('allowances'),
            'advance_deductions' => $wageBills->sum('advance_deductions'),
            'net_pay' => $wageBills->sum('net_pay'),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
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

    public function title(): string
    {
        return 'Approved Wage Bill';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1F4E78'],
                ],
            ],
            $lastRow => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
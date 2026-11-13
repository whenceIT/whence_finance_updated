<?php
namespace App\Exports;

use App\Models\WageBill;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApprovedWageBillPayeExport implements FromCollection, WithHeadings, ShouldAutoSize
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

        return $wageBills->map(function ($wageBill) {
            $employee = $wageBill->employee;

            $fullName = $employee
                ? trim(
                    ($employee->first_name ?? '') . ' ' .
                    ($employee->last_name ?? '')
                )
                : '';

            $grossPay = (float) $wageBill->gross_pay;

            return [
                'tpin' => $employee->tpin ?? '',
                'fullName' => $fullName,
                'employment' => 'Permanent',
                'grossEmoluments' => $grossPay,
                'chargeableEmoluments' => $grossPay,
                'totaltaxCredit' => '0',
                'taxDeducted' => (float) $wageBill->paye,
                'taxAdjusted' => '0',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'tpin',
            'fullName',
            'employment',
            'grossEmoluments',
            'chargeableEmoluments',
            'totaltaxCredit',
            'taxDeducted',
            'taxAdjusted',
        ];
    }
}

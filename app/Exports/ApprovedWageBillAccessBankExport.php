<?php
namespace App\Exports;

use App\Models\WageBill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApprovedWageBillAccessBankExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $cycleStart;

    public function __construct($cycleStart)
    {
        $this->cycleStart = $cycleStart;
    }

    public function collection()
    {
        $cycleDate = Carbon::parse($this->cycleStart);

        $narration = $cycleDate->format('F') . ' Salary';

        $wageBills = WageBill::with('employee')
            ->whereDate('cycle_start', $this->cycleStart)
            ->where('status', 'approved')
            ->orderBy('branch_id')
            ->orderBy('user_id')
            ->get();

        return $wageBills->map(function ($wageBill) use ($narration) {
            $employee = $wageBill->employee;

            $fullName = $employee
                ? trim(
                    ($employee->first_name ?? '') . ' ' .
                    ($employee->last_name ?? '')
                )
                : '';

            return [
                $fullName,
                $employee->bank_sort_code ?? '',
                $employee->bank ?? '',
                (float) $wageBill->net_pay,
                $narration,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Beneficiary Name',
            'Sort Code',
            'Beneficiary Account No',
            'Amount',
            'Naration',
        ];
    }
}

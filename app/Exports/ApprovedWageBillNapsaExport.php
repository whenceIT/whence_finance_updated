<?php
namespace App\Exports;

use App\Models\WageBill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApprovedWageBillNapsaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $cycleStart;

    public function __construct($cycleStart)
    {
        $this->cycleStart = $cycleStart;
    }

    public function collection()
    {
        $cycleDate = Carbon::parse($this->cycleStart);

        $wageBills = WageBill::with('employee')
            ->whereDate('cycle_start', $this->cycleStart)
            ->where('status', 'approved')
            ->orderBy('branch_id')
            ->orderBy('user_id')
            ->get();

        return $wageBills->map(function ($wageBill) use ($cycleDate) {
            $employee = $wageBill->employee;

            $napsaAmount = (float) $wageBill->napsa;

        
return [
    '5149944',
    $cycleDate->format('Y'),
    $cycleDate->format('m'),
    $employee->ssn ?? '',
    $employee->nrc_id ?? '',
    $employee->first_name ?? '',
    $employee->last_name ?? '',
    $employee->date_of_brith ?? '',
    $napsaAmount,
    $napsaAmount,
];

        });
    }

    public function headings(): array
    {
        return [
            'company napsa number',
            'year',
            'month',
            'ssn',
            'nrc number',
            'first_name',
            'last_name',
            'DOB',
            'NAPSA',
            'NAPSA',
        ];
    }
}

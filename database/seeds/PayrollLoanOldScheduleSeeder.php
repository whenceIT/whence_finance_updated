<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollLoanOldSchedule;

class PayrollLoanOldScheduleSeeder extends Seeder
{
    /**
     * Seed the payroll_loan_old_schedule table.
     *
     * Disbursement | 9 Months | 12 Months | 18 Months | 24 Months
     * K5,000       | K756     | K646      | K512      | K448
     * K6,000       | K887     | K755      | K593      | K518
     * K8,000       | K1,149   | K973      | K757      | K657
     * K10,000      | K1,411   | K1,237    | K967      | K842
     * K12,000      | —        | —         | K1,131    | K981
     * K14,000      | —        | —         | K1,295    | K1,120
     * K16,000      | —        | —         | K1,460    | K1,259
     * K18,000      | —        | —         | K1,678    | K1,439
     * K20,000      | —        | —         | K1,848    | K1,582
     *
     * 9-month and 12-month figures are only available up to K10,000.
     * 18-month and 24-month figures are available up to K20,000.
     */
    public function run()
    {
        PayrollLoanOldSchedule::truncate();

        $schedules = [
            [
                'disbursement_amount'  => 5000.00,
                'repayment_9_months'   => 756.00,
                'repayment_12_months'  => 646.00,
                'repayment_18_months'  => 512.00,
                'repayment_24_months'  => 448.00,
            ],
            [
                'disbursement_amount'  => 6000.00,
                'repayment_9_months'   => 887.00,
                'repayment_12_months'  => 755.00,
                'repayment_18_months'  => 593.00,
                'repayment_24_months'  => 518.00,
            ],
            [
                'disbursement_amount'  => 8000.00,
                'repayment_9_months'   => 1149.00,
                'repayment_12_months'  => 973.00,
                'repayment_18_months'  => 757.00,
                'repayment_24_months'  => 657.00,
            ],
            [
                'disbursement_amount'  => 10000.00,
                'repayment_9_months'   => 1411.00,
                'repayment_12_months'  => 1237.00,
                'repayment_18_months'  => 967.00,
                'repayment_24_months'  => 842.00,
            ],
            [
                'disbursement_amount'  => 12000.00,
                'repayment_9_months'   => null,
                'repayment_12_months'  => null,
                'repayment_18_months'  => 1131.00,
                'repayment_24_months'  => 981.00,
            ],
            [
                'disbursement_amount'  => 14000.00,
                'repayment_9_months'   => null,
                'repayment_12_months'  => null,
                'repayment_18_months'  => 1295.00,
                'repayment_24_months'  => 1120.00,
            ],
            [
                'disbursement_amount'  => 16000.00,
                'repayment_9_months'   => null,
                'repayment_12_months'  => null,
                'repayment_18_months'  => 1460.00,
                'repayment_24_months'  => 1259.00,
            ],
            [
                'disbursement_amount'  => 18000.00,
                'repayment_9_months'   => null,
                'repayment_12_months'  => null,
                'repayment_18_months'  => 1678.00,
                'repayment_24_months'  => 1439.00,
            ],
            [
                'disbursement_amount'  => 20000.00,
                'repayment_9_months'   => null,
                'repayment_12_months'  => null,
                'repayment_18_months'  => 1848.00,
                'repayment_24_months'  => 1582.00,
            ],
        ];

        foreach ($schedules as $row) {
            PayrollLoanOldSchedule::create($row);
        }
    }
}

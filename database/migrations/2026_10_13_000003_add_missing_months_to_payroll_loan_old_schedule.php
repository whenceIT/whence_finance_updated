<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingMonthsToPayrollLoanOldSchedule extends Migration
{
    public function up()
    {
        Schema::table('payroll_loan_old_schedule', function (Blueprint $table) {
            for ($m = 1; $m <= 60; $m++) {
                $column = 'repayment_' . $m . '_months';
                if (!Schema::hasColumn('payroll_loan_old_schedule', $column)) {
                    $table->decimal($column, 12, 2)->nullable()->after('disbursement_amount');
                }
            }
        });
    }

    public function down()
    {
        Schema::table('payroll_loan_old_schedule', function (Blueprint $table) {
            $existing = ['repayment_9_months', 'repayment_12_months', 'repayment_18_months', 'repayment_24_months'];
            for ($m = 1; $m <= 60; $m++) {
                if (in_array('repayment_' . $m . '_months', $existing)) {
                    continue;
                }
                $column = 'repayment_' . $m . '_months';
                if (Schema::hasColumn('payroll_loan_old_schedule', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}

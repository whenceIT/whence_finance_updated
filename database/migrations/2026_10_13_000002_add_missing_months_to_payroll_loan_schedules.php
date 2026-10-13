<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingMonthsToPayrollLoanSchedules extends Migration
{
    public function up()
    {
        Schema::table('payroll_loan_schedules', function (Blueprint $table) {
            for ($m = 1; $m <= 60; $m++) {
                $column = 'months_' . $m;
                if (!Schema::hasColumn('payroll_loan_schedules', $column)) {
                    $table->unsignedInteger($column)->nullable()->after('loan_amount');
                }
            }
        });
    }

    public function down()
    {
        Schema::table('payroll_loan_schedules', function (Blueprint $table) {
            for ($m = 1; $m <= 60; $m++) {
                if ($m <= 3 || $m == 6 || $m == 9 || $m == 12 || $m == 18 || $m == 24) {
                    continue;
                }
                $column = 'months_' . $m;
                if (Schema::hasColumn('payroll_loan_schedules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}


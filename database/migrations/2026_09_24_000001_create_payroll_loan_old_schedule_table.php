<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollLoanOldScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_loan_old_schedule', function (Blueprint $table) {
            $table->id();
            $table->decimal('disbursement_amount', 12, 2)->comment('Loan disbursement amount in Kwacha');
            $table->decimal('repayment_9_months', 12, 2)->nullable()->comment('Monthly repayment for 9-month term');
            $table->decimal('repayment_12_months', 12, 2)->nullable()->comment('Monthly repayment for 12-month term');
            $table->decimal('repayment_18_months', 12, 2)->nullable()->comment('Monthly repayment for 18-month term');
            $table->decimal('repayment_24_months', 12, 2)->nullable()->comment('Monthly repayment for 24-month term');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_loan_old_schedule');
    }
}

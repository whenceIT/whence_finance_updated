<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('recorded_by');
            $table->string('receipt_number')->unique();

            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque', 'payroll_deduction']);
            $table->date('payment_date');
            $table->string('reference')->nullable(); // transaction ref, cheque number, etc.

            // Attribution breakdown (calculated from case attribution percentages)
            $table->decimal('recoveries_dept_amount', 15, 2)->default(0);
            $table->decimal('origin_branch_amount', 15, 2)->default(0);
            $table->decimal('supporting_branch_amount', 15, 2)->default(0);

            $table->boolean('is_settlement')->default(false);
            $table->boolean('is_full_payment')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['recovery_case_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_payments');
    }
};

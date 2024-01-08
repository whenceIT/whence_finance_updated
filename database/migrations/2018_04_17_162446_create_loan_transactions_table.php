<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('payment_type_id')->nullable();
            $table->enum('transaction_type',
                [
                    'repayment',
                    'repayment_disbursement',
                    'write_off',
                    'write_off_recovery',
                    'disbursement',
                    'interest_accrual',
                    'fee_accrual',
                    'penalty_accrual',
                    'deposit',
                    'withdrawal',
                    'manual_entry',
                    'pay_charge',
                    'transfer_fund',
                    'interest',
                    'income',
                    'fee',
                    'disbursement_fee',
                    'installment_fee',
                    'specified_due_date_fee',
                    'overdue_maturity',
                    'overdue_installment_fee',
                    'loan_rescheduling_fee',
                    'penalty',
                    'interest_waiver',
                    'charge_waiver'
                ])->default('repayment')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('payment_detail_id')->nullable();
            $table->integer('charge_id')->nullable();
            $table->integer('loan_repayment_schedule_id')->nullable();
            $table->decimal('debit', 65, 4)->nullable();
            $table->decimal('credit', 65, 4)->nullable();
            $table->decimal('balance', 65, 4)->nullable();
            $table->decimal('amount', 65, 4)->nullable();
            $table->tinyInteger('reversible')->default(0);
            $table->tinyInteger('reversed')->default(0);
            $table->enum('reversal_type',
                [
                    'system',
                    'user',
                    'none'
                ])->default('none');
            $table->enum('payment_apply_to',
                [
                    'interest',
                    'principal',
                    'fees',
                    'penalty',
                    'regular'
                ])->default('regular')->nullable();
            $table->enum('status',
                [
                    'pending',
                    'approved',
                    'declined'
                ])->default('pending')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->decimal('interest', 65, 4)->nullable();
            $table->decimal('principal', 65, 4)->nullable();
            $table->decimal('fee', 65, 4)->nullable();
            $table->decimal('penalty', 65, 4)->nullable();
            $table->decimal('overpayment', 65, 4)->nullable();
            $table->date('date')->nullable();

            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->text('receipt')->nullable();
            $table->decimal('principal_derived', 65, 4)->nullable();
            $table->decimal('interest_derived', 65, 4)->nullable();
            $table->decimal('fees_derived', 65, 4)->nullable();
            $table->decimal('penalty_derived', 65, 4)->nullable();
            $table->decimal('overpayment_derived', 65, 4)->nullable();
            $table->decimal('unrecognized_income_derived', 65, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_transactions');
    }
}

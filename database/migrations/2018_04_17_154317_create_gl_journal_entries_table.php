<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gl_journal_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->nullable();
            $table->integer('gl_account_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->enum('transaction_type',
                [
                    'disbursement',
                    'accrual',
                    'deposit',
                    'withdrawal',
                    'manual_entry',
                    'pay_charge',
                    'transfer_fund',
                    'expense',
                    'payroll',
                    'income',
                    'fee',
                    'penalty',
                    'interest',
                    'dividend',
                    'guarantee',
                    'write_off',
                    'repayment',
                    'repayment_disbursement',
                    'repayment_recovery',
                    'interest_accrual',
                    'fee_accrual',
                    'savings',
                    'shares',
                    'asset',
                    'asset_income',
                    'asset_expense',
                    'asset_depreciation',
                ])->default('repayment')->nullable();
            $table->enum('transaction_sub_type',
                [
                    'overpayment',
                    'repayment_interest',
                    'repayment_principal',
                    'repayment_fees',
                    'repayment_penalty',
                ])->nullable();
            $table->decimal('debit', 65, 4)->nullable();
            $table->decimal('credit', 65, 4)->nullable();
            $table->tinyInteger('reversed')->default(0);
            $table->text('name')->nullable();
            $table->string('reference')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('loan_transaction_id')->nullable();
            $table->integer('savings_transaction_id')->nullable();
            $table->integer('savings_id')->nullable();
            $table->integer('shares_transaction_id')->nullable();
            $table->integer('payroll_transaction_id')->nullable();
            $table->integer('payment_detail_id')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->integer('gl_closure_id')->nullable();
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->tinyInteger('reconciled')->default(0);
            $table->tinyInteger('manual_entry')->default(0);
            $table->tinyInteger('approved')->default(1);
            $table->integer('approved_by_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gl_journal_entries');
    }
}

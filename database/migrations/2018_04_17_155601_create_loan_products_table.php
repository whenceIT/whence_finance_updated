<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('fund_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('decimals')->default(2);
            $table->decimal('minimum_principal', 65, 4)->nullable();
            $table->decimal('default_principal', 65, 4)->nullable();
            $table->decimal('maximum_principal', 65, 4)->nullable();
            $table->integer('minimum_loan_term')->nullable();
            $table->integer('default_loan_term')->nullable();
            $table->integer('maximum_loan_term')->nullable();
            $table->integer('repayment_frequency')->nullable();
            $table->enum('repayment_frequency_type', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->decimal('minimum_interest_rate', 65, 4)->nullable();
            $table->decimal('default_interest_rate', 65, 4)->nullable();
            $table->decimal('maximum_interest_rate', 65, 4)->nullable();
            $table->enum('interest_rate_type', ['day', 'week', 'month', 'year'])->nullable();
            $table->integer('grace_on_interest_charged')->nullable();
            $table->integer('grace_on_principal')->nullable();
            $table->integer('grace_on_interest_payment')->nullable();
            $table->tinyInteger('allow_custom_grace')->default(0);
            $table->tinyInteger('allow_standing_instuctions')->default(0);
            $table->enum('interest_method', ['flat', 'declining_balance'])->nullable();
            $table->enum('armotization_method', ['equal_installment', 'equal_principal'])->nullable();
            $table->enum('interest_calculation_period_type', ['daily', 'same'])->default('same');
            $table->enum('year_days', ['actual', '360', '364', '365'])->default('365');
            $table->enum('month_days', ['actual', '30', '31'])->default('30');
            $table->enum('loan_transaction_strategy', ['penalty_fees_interest_principal', 'principal_interest_penalty_fees', 'interest_principal_penalty_fees'])->default('interest_principal_penalty_fees');
            $table->tinyInteger('include_in_cycle')->default(0);
            $table->tinyInteger('lock_guarantee')->default(0);
            $table->tinyInteger('allocate_overpayments')->default(0);
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->enum('accounting_rule', ['none', 'cash', 'accrual_periodic', 'accrual_upfront'])->default('cash');
            $table->integer('npa_days')->nullable();
            $table->integer('arrears_grace_days')->nullable();
            $table->tinyInteger('npa_suspend_income')->default(0);
            $table->integer('gl_account_fund_source_id')->nullable();
            $table->integer('gl_account_loan_portfolio_id')->nullable();
            $table->integer('gl_account_receivable_interest_id')->nullable();
            $table->integer('gl_account_receivable_fee_id')->nullable();
            $table->integer('gl_account_receivable_penalty_id')->nullable();
            $table->integer('gl_account_loan_over_payments_id')->nullable();
            $table->integer('gl_account_suspended_income_id')->nullable();
            $table->integer('gl_account_income_interest_id')->nullable();
            $table->integer('gl_account_income_fee_id')->nullable();
            $table->integer('gl_account_income_penalty_id')->nullable();
            $table->integer('gl_account_income_recovery_id')->nullable();
            $table->integer('gl_account_loans_written_off_id')->nullable();
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
        Schema::dropIfExists('loan_products');
    }
}

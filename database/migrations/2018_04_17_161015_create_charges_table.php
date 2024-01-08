<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('currency_id')->nullable();
            $table->enum('product', array('loan', 'savings', 'shares', 'client'));
            $table->enum('charge_type',
                array(
                    'disbursement',
                    'disbursement_repayment',
                    'specified_due_date',
                    'installment_fee',
                    'overdue_installment_fee',
                    'loan_rescheduling_fee',
                    'overdue_maturity',
                    'savings_activation',
                    'withdrawal_fee',
                    'annual_fee',
                    'monthly_fee',
                    'activation',
                    'shares_purchase',
                    'shares_redeem',
                ));
            $table->enum('charge_option',
                array(
                    'flat',
                    'percentage',
                    'installment_principal_due',
                    'installment_principal_interest_due',
                    'installment_interest_due',
                    'installment_total_due',
                    'total_due',
                    'principal_due',
                    'interest_due',
                    'total_outstanding',
                    'original_principal'
                ));
            $table->tinyInteger('charge_frequency')->default(0);
            $table->enum('charge_frequency_type',
                array(
                    'days',
                    'weeks',
                    'months',
                    'years',
                ))->default('days');
            $table->integer('charge_frequency_amount')->default(0);
            $table->decimal('amount', 65, 2)->nullable();
            $table->decimal('minimum_amount', 65, 2)->nullable();
            $table->decimal('maximum_amount', 65, 2)->nullable();
            $table->enum('charge_payment_mode',
                array(
                    'regular',
                    'account_transfer',
                ))->default('regular');
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('penalty')->default(0);
            $table->tinyInteger('override')->default(0);
            $table->integer('gl_account_income_id')->nullable();
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
        Schema::dropIfExists('charges');
    }
}

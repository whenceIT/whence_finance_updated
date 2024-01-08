<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('charge_id')->nullable();
            $table->tinyInteger('penalty')->default(0);
            $table->tinyInteger('waived')->default(0);
            $table->enum('charge_type',
                array(
                    'disbursement',
                    'disbursement_repayment',
                    'specified_due_date',
                    'installment_fee',
                    'overdue_installment_fee',
                    'loan_rescheduling_fee',
                    'overdue_maturity',
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
                    'original_principal'
                ));
            $table->decimal('amount', 65, 2)->nullable();
            $table->decimal('amount_paid', 65, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->integer('grace_period')->default(0);
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
        Schema::dropIfExists('loan_charges');
    }
}

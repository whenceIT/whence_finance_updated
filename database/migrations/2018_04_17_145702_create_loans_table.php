<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('client_type', ['client', 'group'])->default('client');
            $table->integer('loan_product_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('fund_id')->nullable();
            $table->integer('loan_purpose_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('decimals')->default(2);
            $table->string('account_number')->nullable();
            $table->string('external_id')->nullable();
            $table->integer('loan_officer_id')->nullable();
            $table->decimal('principal', 65, 4)->nullable();
            $table->decimal('applied_amount', 65, 4)->nullable();
            $table->decimal('approved_amount', 65, 4)->nullable();
            $table->decimal('principal_derived', 65, 4)->nullable();
            $table->decimal('interest_derived', 65, 4)->nullable();
            $table->decimal('fees_derived', 65, 4)->nullable();
            $table->decimal('penalty_derived', 65, 4)->nullable();
            $table->decimal('disbursement_fees', 65, 4)->nullable();
            $table->decimal('processing_fee', 65, 4)->nullable();
            $table->integer('loan_term')->nullable();
            $table->enum('loan_term_type', ['days', 'weeks', 'months','years'])->nullable();
            $table->integer('repayment_frequency')->nullable();
            $table->enum('repayment_frequency_type', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->tinyInteger('override_interest')->default(0)->nullable();
            $table->decimal('interest_rate', 65, 4)->nullable();
            $table->decimal('override_interest_rate', 65, 4)->nullable();
            $table->enum('interest_rate_type', ['day', 'week', 'month', 'year'])->nullable();
            $table->date('expected_disbursement_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->date('expected_maturity_date')->nullable();
            $table->date('expected_first_repayment_date')->nullable();
            $table->integer('repayments_number')->nullable();
            $table->date('first_repayment_date')->nullable();
            $table->enum('interest_method', ['flat', 'declining_balance'])->nullable();
            $table->enum('armotization_method', ['equal_installment', 'equal_principal'])->nullable();
            $table->integer('grace_on_interest_charged')->nullable();
            $table->integer('grace_on_principal')->nullable();
            $table->integer('grace_on_interest_payment')->nullable();
            $table->enum('status', array(
                'new',
                'pending',
                'approved',
                'need_changes',
                'disbursed',
                'declined',
                'rejected',
                'withdrawn',
                'written_off',
                'closed',
                'pending_reschedule',
                'rescheduled',
                'paid'
            ))->default('pending');
            $table->integer('created_by_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('need_changes_by_id')->nullable();
            $table->integer('withdrawn_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->integer('written_off_by_id')->nullable();
            $table->integer('disbursed_by_id')->nullable();
            $table->integer('rescheduled_by_id')->nullable();
            $table->integer('closed_by_id')->nullable();
            $table->date('created_date')->nullable();
            $table->date('modified_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('need_changes_date')->nullable();
            $table->date('withdrawn_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->date('written_off_date')->nullable();
            $table->date('rescheduled_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->text('notes')->nullable();
            $table->text('approved_notes')->nullable();
            $table->text('declined_notes')->nullable();
            $table->text('written_off_notes')->nullable();
            $table->text('disbursed_notes')->nullable();
            $table->text('withdrawn_notes')->nullable();
            $table->text('rescheduled_notes')->nullable();
            $table->text('closed_notes')->nullable();
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
        Schema::dropIfExists('loans');
    }
}

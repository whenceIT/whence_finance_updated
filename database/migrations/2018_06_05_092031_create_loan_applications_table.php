<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('client_type', ['client', 'group'])->default('client');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('loan_id')->unsigned()->nullable();
            $table->integer('loan_purpose_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('office_id')->unsigned()->nullable();
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('loan_product_id');
            $table->decimal('amount', 65, 4)->default(0.00);
            $table->enum('status', array(
                'approved',
                'pending',
                'declined'
            ))->default('pending');
            $table->text('guarantor_ids')->nullable();
            $table->integer('loan_term')->nullable();
            $table->enum('loan_term_type', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->text('approved_notes')->nullable();
            $table->text('declined_notes')->nullable();
            $table->date('declined_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('loan_applications');
    }
}

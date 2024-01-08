<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProvisioningCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_provisioning_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->text('name')->nullable();
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->integer('percentage')->nullable();
            $table->integer('gl_account_liability_id')->nullable();
            $table->integer('gl_account_expense_id')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('loan_provisioning_criteria');
    }
}

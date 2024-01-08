<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTransactionRepaymentScheduleMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_transaction_repayment_schedule_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_repayment_schedule_id')->nullable();
            $table->integer('loan_transaction_id')->nullable();
            $table->decimal('interest', 65, 4)->nullable();
            $table->decimal('principal', 65, 4)->nullable();
            $table->decimal('fee', 65, 4)->nullable();
            $table->decimal('penalty', 65, 4)->nullable();
            $table->decimal('overpayment', 65, 4)->nullable();
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
        Schema::dropIfExists('loan_transaction_repayment_schedule_mappings');
    }
}

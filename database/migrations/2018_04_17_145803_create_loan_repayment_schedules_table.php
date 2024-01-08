<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRepaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayment_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('installment')->nullable();
            $table->date('due_date')->nullable();
            $table->date('from_date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->decimal('principal', 65, 4)->nullable();
            $table->decimal('principal_waived', 65, 4)->nullable();
            $table->decimal('principal_written_off', 65, 4)->nullable();
            $table->decimal('principal_paid', 65, 4)->nullable();
            $table->decimal('interest', 65, 4)->nullable();
            $table->decimal('interest_waived', 65, 4)->nullable();
            $table->decimal('interest_written_off', 65, 4)->nullable();
            $table->decimal('interest_paid', 65, 4)->nullable();
            $table->decimal('fees', 65, 4)->nullable();
            $table->decimal('fees_waived', 65, 4)->nullable();
            $table->decimal('fees_written_off', 65, 4)->nullable();
            $table->decimal('fees_paid', 65, 4)->nullable();
            $table->decimal('penalty', 65, 4)->nullable();
            $table->decimal('penalty_waived', 65, 4)->nullable();
            $table->decimal('penalty_written_off', 65, 4)->nullable();
            $table->decimal('penalty_paid', 65, 4)->nullable();
            $table->decimal('total_due', 65, 4)->nullable();
            $table->decimal('total_paid_advance', 65, 4)->nullable();
            $table->decimal('total_paid_late', 65, 4)->nullable();
            $table->tinyInteger('paid')->default(0);
            $table->integer('modified_by_id')->nullable();
            $table->integer('created_by_id')->nullable();
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
        Schema::dropIfExists('loan_repayment_schedules');
    }
}

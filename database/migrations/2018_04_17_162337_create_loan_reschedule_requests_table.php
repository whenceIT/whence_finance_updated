<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRescheduleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_reschedule_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->decimal('principal', 65, 4)->nullable();
            $table->enum('status', array(
                'pending',
                'approved',
                'rejected',
            ))->default('pending');
            $table->integer('created_by_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('rejected_by_id')->nullable();
            $table->date('created_date')->nullable();
            $table->date('modified_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('rejected_date')->nullable();
            $table->date('reschedule_from_date')->nullable();
            $table->integer('recalculate_interest')->default(0);
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
        Schema::dropIfExists('loan_reschedule_requests');
    }
}

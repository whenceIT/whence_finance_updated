<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportSchedulerRunHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_scheduler_run_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_schedule_id')->nullable();
            $table->date('report_start_date')->nullable();
            $table->string('report_start_time')->nullable();
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
        Schema::dropIfExists('report_scheduler_run_history');
    }
}

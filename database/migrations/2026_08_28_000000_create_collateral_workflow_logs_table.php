<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollateralWorkflowLogsTable extends Migration
{
    public function up()
    {
         Schema::dropIfExists('collateral_workflow_logs');
        Schema::create('collateral_workflow_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('collateral_id');
            $table->string('from_status');
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('collateral_workflow_logs');
    }
}

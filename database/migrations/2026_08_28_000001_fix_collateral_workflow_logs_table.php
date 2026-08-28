<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCollateralWorkflowLogsTable extends Migration
{
    public function up()
    {
        Schema::table('collateral_workflow_logs', function (Blueprint $table) {
            $table->dropForeign(['collateral_id']);
            $table->dropForeign(['user_id']);

            $table->unsignedBigInteger('collateral_id')->change();
            $table->unsignedInteger('user_id')->change();

            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('collateral_workflow_logs', function (Blueprint $table) {
            $table->dropForeign(['collateral_id']);
            $table->dropForeign(['user_id']);

            $table->unsignedInteger('collateral_id')->change();
            $table->unsignedInteger('user_id')->change();
        });
    }
}

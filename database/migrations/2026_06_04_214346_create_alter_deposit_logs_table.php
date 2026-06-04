<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_log', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit_id')->nullable()->after('deposit_method');
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_deposit_log', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->dropColumn('deposit_id');
        });
    }
};

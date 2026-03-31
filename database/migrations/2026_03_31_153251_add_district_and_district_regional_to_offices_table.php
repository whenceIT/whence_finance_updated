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
        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedInteger('district_id')->nullable()->after('province_id');
            $table->unsignedInteger('district_regional_id')->nullable()->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['district_regional_id']);
            $table->dropForeign(['district_id']);
            $table->dropColumn(['district_regional_id', 'district_id']);
        });
    }
};

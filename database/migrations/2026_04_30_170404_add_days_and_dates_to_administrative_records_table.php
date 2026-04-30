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
        Schema::table('administrative_records', function (Blueprint $table) {
            $table->json('absence_dates')->nullable()->after('number_of_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('administrative_records', function (Blueprint $table) {
            $table->dropColumn(['number_of_days', 'absence_dates']);
        });
    }
};

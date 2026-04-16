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
        Schema::table('notifix', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('district_id')->nullable()->after('office_id');
            $table->unsignedBigInteger('province_id')->nullable()->after('district_id');
            $table->unsignedBigInteger('to_id')->nullable()->after('province_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifix', function (Blueprint $table) {
            $table->dropColumn(['office_id', 'district_id', 'province_id', 'to_id']);
        });
    }
};

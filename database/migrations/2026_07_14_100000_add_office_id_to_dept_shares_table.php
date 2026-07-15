<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('recoveries_dept_excalated_shares', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('recovery_payment_id');
        });
    }

    public function down()
    {
        Schema::table('recoveries_dept_excalated_shares', function (Blueprint $table) {
            $table->dropColumn('office_id');
        });
    }
};
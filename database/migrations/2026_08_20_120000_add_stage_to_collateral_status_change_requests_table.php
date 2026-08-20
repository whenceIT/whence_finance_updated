<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->string('stage')->nullable()->after('new_status');
        });
    }

    public function down()
    {
        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
};

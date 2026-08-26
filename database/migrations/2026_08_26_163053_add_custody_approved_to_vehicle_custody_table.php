<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustodyApprovedToVehicleCustodyTable extends Migration
{
    public function up()
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->unsignedTinyInteger('custody_approved')->nullable()->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->dropColumn('custody_approved');
        });
    }
}

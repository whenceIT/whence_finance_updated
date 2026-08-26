<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->decimal('disposal_costs', 15, 2)->default(0.00)->after('sold_price');
        });
    }

    public function down()
    {
        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->dropColumn('disposal_costs');
        });
    }
};

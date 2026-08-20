<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->text('disposal_costs')->nullable()->change();
        });

        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->text('disposal_costs')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->decimal('disposal_costs', 65, 4)->default(0.0000)->change();
        });

        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->decimal('disposal_costs', 15, 2)->default(0.00)->change();
        });
    }
};

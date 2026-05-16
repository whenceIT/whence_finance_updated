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
        // Schema::table('collateral_status_change_requests', function (Blueprint $table) {
        //     $table->decimal('sold_price', 15, 2)->nullable()->after('reason');
        //     $table->decimal('penalty', 15, 2)->nullable()->after('sold_price');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collateral_status_change_requests', function (Blueprint $table) {
            $table->dropColumn(['sold_price', 'penalty']);
        });
    }
};

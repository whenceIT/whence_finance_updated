<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->unsignedBigInteger('vetted_valuation_by')->nullable()->after('vetted_valuation_cost');
            $table->tinyInteger('vetted_valuation_status')->default(0)->after('vetted_valuation_by');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn(['vetted_valuation_by', 'vetted_valuation_status']);
        });
    }
};

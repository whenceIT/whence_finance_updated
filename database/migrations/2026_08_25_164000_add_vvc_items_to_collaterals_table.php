<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->json('vvc_items')->nullable()->after('vetted_valuation_cost');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn('vvc_items');
        });
    }
};

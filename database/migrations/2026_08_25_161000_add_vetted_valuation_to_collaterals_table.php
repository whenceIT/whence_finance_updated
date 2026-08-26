<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->decimal('vetted_valuation', 15, 2)->nullable()->after('current_worth');
            $table->decimal('vetted_valuation_cost', 15, 2)->nullable()->after('vetted_valuation');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn(['vetted_valuation', 'vetted_valuation_cost']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->date('pledged_at')->nullable()->after('date_purchased');
            $table->date('seized_at')->nullable()->after('pledged_at');
            $table->date('valuated_at')->nullable()->after('seized_at');
            $table->date('listed_at')->nullable()->after('valuated_at');
            $table->date('sold_at')->nullable()->after('listed_at');
            $table->date('written_off_at')->nullable()->after('sold_at');
            $table->date('released_at')->nullable()->after('written_off_at');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn([
                'pledged_at',
                'seized_at',
                'valuated_at',
                'listed_at',
                'sold_at',
                'written_off_at',
                'released_at',
            ]);
        });
    }
};

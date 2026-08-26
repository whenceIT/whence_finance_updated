<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->enum('stage', ['pledged', 'brought_in', 'seized'])->nullable()->after('status');
            $table->text('stage_icon')->nullable()->after('stage');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn(['stage', 'stage_icon']);
        });
    }
};

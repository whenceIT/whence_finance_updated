<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->decimal('approved_value', 65, 4)->nullable()->after('current_worth');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn('approved_value');
        });
    }
};

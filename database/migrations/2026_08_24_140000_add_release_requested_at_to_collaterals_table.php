<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->date('release_requested_at')->nullable()->after('released_at');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn('release_requested_at');
        });
    }
};

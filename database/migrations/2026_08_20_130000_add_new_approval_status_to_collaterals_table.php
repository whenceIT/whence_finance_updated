<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->unsignedTinyInteger('new_approval_status')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn('new_approval_status');
        });
    }
};

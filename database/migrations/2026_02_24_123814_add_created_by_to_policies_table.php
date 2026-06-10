<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('policies') && !Schema::hasColumn('policies', 'created_by')) {
            Schema::table('policies', function (Blueprint $table) {
                $table->integer('created_by')->nullable()->after('access_level');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('policies') && Schema::hasColumn('policies', 'created_by')) {
            Schema::table('policies', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
};
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
        if (!Schema::hasColumn('clients', 'approved_dormant')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->integer('approved_dormant')->default(0)->after('blacklisted');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('clients', 'approved_dormant')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('approved_dormant');
            });
        }
    }
};

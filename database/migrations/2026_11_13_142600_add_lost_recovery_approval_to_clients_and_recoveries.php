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
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('lost_recovery_approval', ['pending', 'approved', 'lost'])->default('lost')->after('lost_recovery_notes');
        });

        Schema::table('creative_lost_client_recoveries', function (Blueprint $table) {
            $table->enum('lost_recovery_approval', ['pending', 'approved', 'lost'])->default('lost')->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('lost_recovery_approval');
        });

        Schema::table('creative_lost_client_recoveries', function (Blueprint $table) {
            $table->dropColumn('lost_recovery_approval');
        });
    }
};

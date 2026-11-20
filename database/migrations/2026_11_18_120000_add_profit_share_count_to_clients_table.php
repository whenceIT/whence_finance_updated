<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('profit_share_count')->default(0)->after('lost_recovery_approval');
        });
    }

    public function down()
    {
        DB::table('clients')
            ->whereNull('lost_recovery_approval')
            ->update(['lost_recovery_approval' => 'lost']);

        Schema::table('clients', function (Blueprint $table) {
            $table->enum('lost_recovery_approval', ['pending', 'approved', 'lost'])
                ->nullable(false)
                ->default('lost')
                ->change();
            $table->dropColumn('profit_share_count');
        });
    }
};

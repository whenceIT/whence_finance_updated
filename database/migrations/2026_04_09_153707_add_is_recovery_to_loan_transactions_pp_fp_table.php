<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loan_transactions_pp_fp', function (Blueprint $table) {
            $table->string('is_recovery')->default(0)->after('id');
        });
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->string('is_recovery')->default(0)->after('id');
        });
    }

    public function down()
    {
        Schema::table('loan_transactions_pp_fp', function (Blueprint $table) {
            $table->dropColumn('is_recovery');
        });
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->dropColumn('is_recovery');
        });
    }
};

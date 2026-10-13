<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecoveryCaseIdToUnitSharesTable extends Migration
{
    public function up()
    {
        Schema::table('unit_shares', function (Blueprint $table) {
            $table->unsignedBigInteger('recovery_case_id')->nullable()->after('loan_id');
            $table->index('recovery_case_id');
        });
    }

    public function down()
    {
        Schema::table('unit_shares', function (Blueprint $table) {
            $table->dropIndex(['recovery_case_id']);
            $table->dropColumn('recovery_case_id');
        });
    }
}

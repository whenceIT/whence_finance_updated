<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCollateralToLoanTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->tinyInteger('is_collateral')->default(0);
        });
    }

    public function down()
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->dropColumn('is_collateral');
        });
    }
}

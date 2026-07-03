<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeAndContributionToProvincialTransactionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('provincial_transactions')) {
        Schema::table('provincial_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('province_id');
            $table->string('contribution')->nullable()->after('payment_method');
        });
        
        }
    }

    public function down()
    {
        Schema::table('provincial_transactions', function (Blueprint $table) {
            $table->dropColumn('office_id');
            $table->dropColumn('contribution');
        });
    }
}
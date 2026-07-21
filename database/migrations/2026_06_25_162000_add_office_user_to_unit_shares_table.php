<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeUserToUnitSharesTable extends Migration
{
    public function up()
    {
        Schema::table('unit_shares', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('loan_txn_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('office_id');
            $table->text('notes')->nullable()->after('user_id');
            
            $table->index(['office_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::table('unit_shares', function (Blueprint $table) {
            $table->dropColumn(['office_id', 'user_id', 'notes']);
        });
    }
}
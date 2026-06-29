<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProvincialTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('provincial_transactions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved'])->default('pending')->after('recorded_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down()
    {
        Schema::table('provincial_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
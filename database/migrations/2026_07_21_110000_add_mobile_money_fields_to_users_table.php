<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_money_number')->nullable()->after('bank_account_number');
            $table->string('receiver_name')->nullable()->after('mobile_money_number');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile_money_number', 'receiver_name']);
        });
    }
};

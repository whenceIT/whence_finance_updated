<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyerDetailsToVehiclesTable extends Migration
{
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dateTime('sold_at')->nullable()->after('status');
            $table->string('buyer_fullname')->nullable()->after('sold_at');
            $table->string('buyer_phone')->nullable()->after('buyer_fullname');
            $table->string('buyer_nrc_number')->nullable()->after('buyer_phone');
            $table->string('buyer_sex')->nullable()->after('buyer_nrc_number');
            $table->string('buyer_location')->nullable()->after('buyer_sex');
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'sold_at',
                'buyer_fullname',
                'buyer_phone',
                'buyer_nrc_number',
                'buyer_sex',
                'buyer_location'
            ]);
        });
    }
}

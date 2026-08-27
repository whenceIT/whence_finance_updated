<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyerDetailsToCollateralsTable extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('buyer_name')->nullable()->after('sold_price');
            $table->string('buyer_phone')->nullable()->after('buyer_name');
            $table->string('buyer_nrc')->nullable()->after('buyer_phone');
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_phone', 'buyer_nrc']);
        });
    }
}

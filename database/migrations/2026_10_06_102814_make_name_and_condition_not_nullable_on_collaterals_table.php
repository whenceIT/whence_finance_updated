<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNameAndConditionNotNullableOnCollateralsTable extends Migration
{
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('condition')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('condition')->nullable()->change();
        });
    }
}

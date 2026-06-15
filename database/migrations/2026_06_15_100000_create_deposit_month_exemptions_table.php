<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deposit_month_exemptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('deposit_type_id')->nullable();
            $table->integer('no_months_exclude')->default(0);
            $table->timestamps();
            $table->unique(['office_id', 'deposit_type_id']);
        });

        if (Schema::hasColumn('offices', 'deposit_months_exempted')) {
            Schema::table('offices', function (Blueprint $table) {
                $table->dropColumn('deposit_months_exempted');
            });
        }
    }

    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            if (!Schema::hasColumn('offices', 'deposit_months_exempted')) {
                $table->integer('deposit_months_exempted')->default(0)->after('district_regional_id');
            }
        });

        Schema::dropIfExists('deposit_month_exemptions');
    }
};

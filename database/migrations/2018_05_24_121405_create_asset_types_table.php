<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('gl_account_fixed_asset_id')->nullable();
            $table->integer('gl_account_asset_id')->nullable();
            $table->integer('gl_account_contra_asset_id')->nullable();
            $table->integer('gl_account_expense_id')->nullable();
            $table->integer('gl_account_liability_id')->nullable();
            $table->integer('gl_account_income_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_types');
    }
}

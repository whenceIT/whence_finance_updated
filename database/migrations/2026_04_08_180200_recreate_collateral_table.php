<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('collateral');

        Schema::create('collateral', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('loan_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('collateral_type_id')->nullable();
            $table->string('name')->nullable();
            $table->string('serial')->nullable();
            $table->decimal('value', 65, 4)->nullable();
            $table->decimal('initial_price', 65, 4)->nullable();
            $table->decimal('current_worth', 65, 4)->nullable();
            $table->string('status')->nullable();
            $table->string('condition')->nullable();
            $table->date('date_purchased')->nullable();
            $table->date('date_resold')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->text('description')->nullable();
            $table->text('picture')->nullable();
            $table->text('gallery')->nullable();
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
        Schema::dropIfExists('collateral');
    }
}
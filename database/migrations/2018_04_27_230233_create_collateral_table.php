<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collateral', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('collateral_type_id')->nullable();
            $table->string('name')->nullable();
            $table->string('serial')->nullable();
            $table->decimal('value', 65, 4)->nullable();
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

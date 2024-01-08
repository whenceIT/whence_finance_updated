<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_office_id')->nullable();
            $table->integer('to_office_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('amount',65,8)->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('office_transactions');
    }
}

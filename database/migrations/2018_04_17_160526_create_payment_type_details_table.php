<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTypeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_type_details', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type',
                [
                    'loan',
                    'savings',
                    'share',
                    'client',
                    'journal'
                ])->nullable();
            $table->integer('reference_id');
            $table->string('account_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('routing_code')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('bank')->nullable();
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
        Schema::dropIfExists('payment_type_details');
    }
}

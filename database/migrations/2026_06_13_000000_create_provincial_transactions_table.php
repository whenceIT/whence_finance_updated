<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincialTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('provincial_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']);
            $table->unsignedBigInteger('province_id');
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('provincial_transactions');
    }
}
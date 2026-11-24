<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeDailyIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('creative_daily_incomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable()->index();
            $table->date('income_date')->index();
            $table->string('income_category')->index();
            $table->string('income_source')->nullable();
            $table->string('reference_number')->nullable()->index();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->unsignedInteger('recorded_by_user_id')->nullable()->index();
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'Mobile Money', 'Card', 'Cheque', 'Other'])->default('Cash');
            $table->string('received_from')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('branch_id')->references('id')->on('offices')->onDelete('set null');
            $table->foreign('recorded_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('creative_daily_incomes');
    }
}
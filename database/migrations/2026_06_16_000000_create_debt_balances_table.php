<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_balances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('deposit_type_id');
            $table->unsignedBigInteger('balance')->default(0);
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('deposit_type_id')->references('id')->on('deposit_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_balances');
    }
};

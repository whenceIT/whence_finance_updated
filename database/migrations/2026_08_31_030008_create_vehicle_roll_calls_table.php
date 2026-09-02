<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_roll_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->date('verification_date')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->boolean('location_confirmed')->default(false);
            $table->boolean('vehicle_present')->default(false);
            $table->text('condition')->nullable();
            $table->integer('current_mileage')->nullable();
            $table->text('photos')->nullable();
            $table->enum('status', ['verified', 'missing', 'damaged', 'relocated'])->default('verified');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'verification_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_roll_calls');
    }
};

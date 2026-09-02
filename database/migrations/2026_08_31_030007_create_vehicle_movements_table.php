<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('previous_location')->nullable();
            $table->string('new_location')->nullable();
            $table->date('movement_date')->nullable();
            $table->unsignedBigInteger('authorized_by_id')->nullable();
            $table->unsignedBigInteger('moved_by_id')->nullable();
            $table->text('reason')->nullable();
            $table->text('condition')->nullable();
            $table->text('photos')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_movements');
    }
};

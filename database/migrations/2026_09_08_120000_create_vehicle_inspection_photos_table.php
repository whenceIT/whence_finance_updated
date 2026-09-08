<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_inspection_id');
            $table->string('photo_url');
            $table->timestamps();

            $table->foreign('vehicle_inspection_id')
                  ->references('id')->on('vehicle_inspections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_inspection_photos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_category_id');
            $table->unsignedBigInteger('training_material_id');
            $table->timestamps();

            $table->foreign('course_category_id')
                  ->references('id')
                  ->on('course_categories')
                  ->onDelete('cascade');

            $table->foreign('training_material_id')
                  ->references('id')
                  ->on('training_materials')
                  ->onDelete('cascade');

            $table->unique(['course_category_id', 'training_material_id'], 'cat_mat_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_material');
    }
};

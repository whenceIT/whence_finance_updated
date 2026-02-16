<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_material_id');
            $table->string('topic_name');
            $table->string('topic_type'); // video, pdf, ppt, document
            $table->string('file_path')->nullable();
            $table->string('duration')->nullable(); // in minutes
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('training_material_id')
                  ->references('id')
                  ->on('training_materials')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_topics');
    }
};

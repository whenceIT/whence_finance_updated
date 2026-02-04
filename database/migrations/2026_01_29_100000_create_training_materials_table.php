<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('material_type', ['document', 'audio', 'video'])->default('document');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds for audio/video');
            $table->enum('department', ['Operations', 'Recoveries', 'Administration', 'Finance', 'IT', 'HR', 'Legal', 'Compliance', 'General'])->nullable();
            $table->string('category')->nullable();
            $table->enum('target_role', ['all', '1', '4', '6', '3', '5', '10'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('material_type');
            $table->index('department');
            $table->index('category');
            $table->index('target_role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_materials');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table already exists with different structure - skip
        if (Schema::hasTable('general_uploads')) {
            return;
        }
        
        Schema::create('general_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('path');
            $table->enum('type', ['video', 'audio', 'book', 'paper', 'document', 'image', 'other'])->default('other');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('category')->default('other');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_uploads');
    }
}

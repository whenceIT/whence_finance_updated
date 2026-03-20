<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralUploadPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_upload_position', function (Blueprint $table) {
            $table->unsignedBigInteger('general_upload_id');
            $table->unsignedInteger('position_id');
            
            $table->foreign('general_upload_id')
                  ->references('id')
                  ->on('general_uploads')
                  ->onDelete('cascade');
            
            $table->foreign('position_id')
                  ->references('id')
                  ->on('positions') // Assuming positions table exists
                  ->onDelete('cascade');
            
            $table->primary(['general_upload_id', 'position_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_upload_position');
    }
}
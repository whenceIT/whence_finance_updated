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
        // Table already exists with different structure - skip
        if (Schema::hasTable('general_upload_position')) {
            return;
        }

        Schema::create('general_upload_position', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('general_upload_id');
            $table->unsignedInteger('position_id');
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
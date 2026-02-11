<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTrainingMaterialsFileColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
        });
    }
}

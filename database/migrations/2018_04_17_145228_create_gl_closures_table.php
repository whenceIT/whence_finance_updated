<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gl_closures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->date('closing_date');
            $table->integer('modified_by_id')->nullable();
            $table->string('gl_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gl_closures');
    }
}

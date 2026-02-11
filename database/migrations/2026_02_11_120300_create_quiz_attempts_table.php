<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('score')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('percentage')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('quiz_id');
            $table->index('user_id');
            
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
        Schema::dropIfExists('quiz_attempts');
    }
}

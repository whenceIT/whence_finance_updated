<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_topic_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70);
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('course_topic_id')->references('id')->on('course_topics')->onDelete('cascade');
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
        Schema::dropIfExists('quizzes');
    }
}

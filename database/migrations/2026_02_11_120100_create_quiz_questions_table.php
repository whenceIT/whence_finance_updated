<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->string('question_type')->default('multiple_choice')->comment('multiple_choice, true_false, short_answer');
            $table->integer('sort_order')->default(0);
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
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
        Schema::dropIfExists('quiz_questions');
    }
}

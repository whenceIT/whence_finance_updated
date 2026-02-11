<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_question_id');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('quiz_question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
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
        Schema::dropIfExists('quiz_options');
    }
}

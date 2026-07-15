<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_quiz_user_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->enum('selected_answer', ['A', 'B', 'C', 'D']);
            $table->boolean('is_correct');
            $table->timestamp('answered_at')->useCurrent();
            
            $table->foreign('attempt_id')->references('id')->on('policy_quiz_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('policy_quiz_questions');
            $table->unique(['attempt_id', 'question_id']); // One answer per question per attempt
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_quiz_user_answers');
    }
};
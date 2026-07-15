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
        Schema::create('policy_quiz_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('policy_quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->datetime('started_at');
            $table->datetime('completed_at')->nullable();
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->timestamps();
            
            $table->unique(['policy_quiz_id', 'user_id']); // One active attempt per user per quiz
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_quiz_attempts');
    }
};
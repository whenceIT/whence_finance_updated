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
        Schema::create('policy_of_the_day', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('content'); // Short digestible content
            $table->text('full_content')->nullable(); // Optional full content
            $table->unsignedBigInteger('policy_id')->nullable(); // Link to actual policy
            $table->unsignedBigInteger('created_by');
            $table->date('scheduled_date')->nullable(); // For scheduled policies
            $table->boolean('is_active')->default(true);
            $table->boolean('is_random')->default(false); // If true, can be randomly selected
            $table->integer('display_order')->default(0);
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
        Schema::dropIfExists('policy_of_the_day');
    }
};

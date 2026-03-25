<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_views', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'upload', 'topic', 'course', etc.
            $table->unsignedBigInteger('user_id'); // Who viewed the content
            $table->unsignedBigInteger('item_id'); // The ID of the content (upload_id, topic_id, etc.)
            $table->timestamps();

            // Add indexes for faster queries
            $table->index(['type', 'item_id']);
            $table->index('user_id');
            $table->unique(['type', 'item_id', 'user_id'], 'unique_view'); // Prevent duplicate views
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_views');
    }
}

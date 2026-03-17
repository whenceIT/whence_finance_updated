<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneralTopicAndPositionToGeneralUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('general_topic_id')->nullable()->after('uploaded_by');
            $table->foreign('general_topic_id')->references('id')->on('general_topics')->onDelete('set null');
            
            $table->unsignedInteger('position_id')->nullable()->after('general_topic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_uploads', function (Blueprint $table) {
            $table->dropForeign(['general_topic_id']);
            $table->dropColumn(['general_topic_id', 'position_id']);
        });
    }
}

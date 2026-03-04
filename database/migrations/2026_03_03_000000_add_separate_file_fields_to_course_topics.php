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
        Schema::table('course_topics', function (Blueprint $table) {
            $table->string('video_file_path')->nullable()->after('file_path');
            $table->string('audio_file_path')->nullable()->after('video_file_path');
            $table->string('pdf_file_path')->nullable()->after('audio_file_path');
            $table->string('ppt_file_path')->nullable()->after('pdf_file_path');
            $table->string('document_file_path')->nullable()->after('ppt_file_path');
            $table->string('file_name')->nullable()->after('document_file_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn('video_file_path');
            $table->dropColumn('audio_file_path');
            $table->dropColumn('pdf_file_path');
            $table->dropColumn('ppt_file_path');
            $table->dropColumn('document_file_path');
            $table->dropColumn('file_name');
        });
    }
};

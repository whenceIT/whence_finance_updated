<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->text('video_file_path')->nullable()->change();
            $table->text('audio_file_path')->nullable()->change();
            $table->text('pdf_file_path')->nullable()->change();
            $table->text('ppt_file_path')->nullable()->change();
            $table->text('document_file_path')->nullable()->change();
            $table->text('file_name')->nullable()->change();
            $table->text('file_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->string('video_file_path')->nullable()->change();
            $table->string('audio_file_path')->nullable()->change();
            $table->string('pdf_file_path')->nullable()->change();
            $table->string('ppt_file_path')->nullable()->change();
            $table->string('document_file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('file_path')->nullable()->change();
        });
    }
};

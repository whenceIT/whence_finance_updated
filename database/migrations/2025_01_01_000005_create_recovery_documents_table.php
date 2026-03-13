<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->string('document_type'); // e.g. 'summons', 'court_order', 'demand_letter', 'payment_receipt'
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_documents');
    }
};

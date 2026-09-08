<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_ownership_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->enum('ownership_type', ['individual', 'letter_of_sale', 'company'])->default('individual');
            $table->string('registered_owner_name')->nullable();
            $table->string('seller_name')->nullable();
            $table->string('seller_nrc')->nullable();
            $table->string('seller_phone')->nullable();
            $table->string('witness_1_name')->nullable();
            $table->string('witness_1_nrc')->nullable();
            $table->string('witness_2_name')->nullable();
            $table->string('witness_2_nrc')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_registration_number')->nullable();
            $table->text('directors')->nullable();
            $table->string('authorized_representative_name')->nullable();
            $table->string('authorized_representative_nrc')->nullable();
            $table->string('ownership_documents_path')->nullable();
            $table->boolean('verified')->default(false);
            $table->unsignedBigInteger('verified_by_id')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'ownership_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_ownership_records');
    }
};

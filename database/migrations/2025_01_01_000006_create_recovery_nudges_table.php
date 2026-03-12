<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_nudges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('sent_by');
            $table->enum('channel', ['sms', 'whatsapp']);
            $table->string('phone_number');
            $table->text('message');
            $table->enum('status', ['queued', 'sent', 'delivered', 'failed'])->default('queued');
            $table->json('gateway_response')->nullable(); // Response from Africa's Talking API
            $table->timestamps();

            $table->index(['recovery_case_id', 'created_at']);
            $table->index('sent_by');


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_nudges');
    }
};

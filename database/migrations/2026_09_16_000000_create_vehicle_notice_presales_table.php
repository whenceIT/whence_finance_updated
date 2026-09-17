<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_notice_presales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_id');
            $table->date('notice_generated_date')->nullable();
            $table->date('notice_served_date')->nullable();
            $table->string('service_method')->default('SMS');
            $table->string('officer_issuing')->default('System Generated');
            $table->date('deadline_to_client')->nullable();
            $table->boolean('client_settled')->default(false);
            $table->boolean('client_presented_buyer')->default(false);
            $table->text('buyer_details')->nullable();
            $table->text('outcome_after_expiry')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_notice_presales');
    }
};

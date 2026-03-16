<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('performed_by');

            $table->enum('activity_type', [
                'status_change',
                'payment_received',
                'field_visit',
                'phone_call',
                'sms_sent',
                'whatsapp_sent',
                'legal_filing',
                'court_hearing',
                'asset_seizure',
                'skip_trace_attempt',
                'guarantor_contact',
                'payment_plan_negotiated',
                'case_handover',
                'note_added',
                'document_uploaded',
                'cost_incurred',
            ]);

            $table->string('description');
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->decimal('amount_collected', 15, 2)->nullable(); // payment amount
            $table->decimal('cost_incurred', 15, 2)->nullable();
            $table->string('outcome')->nullable(); // e.g. 'delivered', 'failed' for SMS
            $table->json('metadata')->nullable(); // flexible extra data per activity type
            $table->timestamps();

            $table->index(['recovery_case_id', 'created_at']);
            $table->index('performed_by');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_activities');
    }
};

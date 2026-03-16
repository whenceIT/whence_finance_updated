<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarantors linked to recovery cases
        Schema::create('recovery_guarantors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('id_number')->nullable();
            $table->text('address')->nullable();
            $table->string('employer')->nullable();
            $table->string('relationship_to_client')->nullable();

            // Contact tracking
            $table->integer('contact_attempts')->default(0);
            $table->timestamp('last_contacted_at')->nullable();
            $table->string('last_outcome')->nullable(); // cooperative | unresponsive | promised | unknown
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // Skip trace leads
        Schema::create('skip_trace_leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('created_by');

            $table->string('lead_type'); // social_media | old_number | community | reference | data_app | field
            $table->text('description');
            $table->string('new_phone')->nullable();
            $table->text('new_address')->nullable();
            $table->string('source')->nullable();       // e.g. "Facebook search", "Guarantor re-interview"
            $table->boolean('verified')->default(false);
            $table->boolean('led_to_location')->default(false);
            $table->decimal('cost_incurred', 15, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

        });

        // Legal documents / filings per case
        Schema::create('legal_filings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('created_by');

            $table->string('filing_type'); // demand_letter | summons | WoD | garnishee | judgment | caveat | auction
            $table->string('reference_number')->nullable();
            $table->string('law_firm')->nullable();
            $table->date('filed_date');
            $table->date('hearing_date')->nullable();
            $table->string('court_name')->nullable();
            $table->string('outcome')->nullable(); // pending | won | lost | settled | withdrawn
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('amount_recovered', 15, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_filings');
        Schema::dropIfExists('skip_trace_leads');
        Schema::dropIfExists('recovery_guarantors');
    }
};

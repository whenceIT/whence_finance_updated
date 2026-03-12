<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('case_number')->unique(); // e.g. RC-2025-00001
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('origin_branch_id');
            $table->unsignedBigInteger('supporting_branch_id')->nullable();
            $table->unsignedBigInteger('assigned_specialist_id')->nullable();
            $table->unsignedBigInteger('loan_id');

            // Category — maps to the 5 recovery parameters
            $table->enum('category', [
                'cross_branch',   // Runaway clients
                'escalated',      // Escalated from Loan Consultants
                'dormant',        // Dormant account revival
                'legal',          // Legal recovery
                'skip_trace',     // Skip tracing
            ]);

            // LMS status tags (as described in operational guidelines)
            $table->enum('status', [
                // Cross-branch statuses
                'runaway_pending_confirmation',
                'runaway_active_recovery',
                'recovered_runaway',
                // Escalated statuses
                'escalated_handover',
                'escalated_in_review',
                'escalated_active_recovery',
                'recovered_post_escalation',
                // Dormant statuses
                'dormant_for_revival',
                'recovery_revived',
                // Legal statuses
                'pre_litigation_review',
                'legal_filed',
                'legal_active',
                'legal_judgment_won',
                'recovered_legal',
                // Skip trace statuses
                'skip_trace_required',
                'skip_trace_digital_review',
                'skip_trace_contact_reengagement',
                'skip_trace_field_intel_active',
                'located_for_recovery',
                // Generic
                'closed',
                'written_off',
            ])->default('escalated_handover');

            $table->decimal('loan_outstanding_amount', 15, 2);
            $table->decimal('amount_recovered', 15, 2)->default(0);
            $table->decimal('recovery_costs', 15, 2)->default(0);
            $table->decimal('settlement_amount', 15, 2)->nullable(); // if a settlement was negotiated

            // Escalation source
            $table->unsignedBigInteger('escalated_by_user_id')->nullable(); // LC who escalated
            $table->date('escalation_date')->nullable();
            $table->integer('days_past_due_at_escalation')->nullable();
            $table->integer('lc_contact_attempts')->nullable(); // how many attempts LC made

            // Attribution split (stored as percentages, must sum to 100)
            $table->decimal('recoveries_dept_attribution_pct', 5, 2)->default(100.00);
            $table->decimal('origin_branch_attribution_pct', 5, 2)->default(0.00);
            $table->decimal('supporting_branch_attribution_pct', 5, 2)->default(0.00);

            // Legal-specific fields
            $table->string('legal_reference_number')->nullable();
            $table->string('lawyer_firm')->nullable();
            $table->date('legal_filed_date')->nullable();
            $table->date('court_date')->nullable();
            $table->decimal('legal_costs_incurred', 15, 2)->default(0);
            $table->enum('enforcement_type', [
                'garnishee_order',
                'warrant_of_distress',
                'writ_of_execution',
                'charging_order',
                'judgment_debtor_summons',
                'none',
            ])->default('none');

            // Skip trace fields
            $table->string('skip_trace_tracking_code')->nullable();
            $table->boolean('client_located')->default(false);
            $table->date('located_date')->nullable();
            $table->decimal('skip_trace_costs', 15, 2)->default(0);

            // Dormant-specific
            $table->date('last_payment_date')->nullable();
            $table->integer('dormant_days')->nullable();
            $table->string('revival_method')->nullable(); // e.g. 'sms_campaign', 'field_visit', 'phone_call'

            // Runaway / cross-branch
            $table->string('client_last_known_location')->nullable();
            $table->string('client_new_location')->nullable();
            $table->boolean('joint_field_visit_done')->default(false);

            $table->text('notes')->nullable();
            $table->date('target_resolution_date')->nullable();
            $table->date('resolved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['category', 'status']);
            $table->index(['assigned_specialist_id', 'status']);
            $table->index(['origin_branch_id']);
            $table->index('case_number');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_cases');
    }
};

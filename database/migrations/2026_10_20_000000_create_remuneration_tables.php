<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remuneration_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('tier_number');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('position_remuneration_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_position_id');
            $table->unsignedBigInteger('remuneration_tier_id');
            $table->decimal('gross_pay', 12, 2);
            $table->decimal('target_net_pay', 12, 2)->nullable();
            $table->char('currency', 3)->default('ZMW');
            $table->string('threshold_type', 50)->nullable();
            $table->decimal('threshold_amount', 14, 2)->nullable();
            $table->string('threshold_operator', 10)->nullable();
            $table->decimal('threshold_upper_amount', 14, 2)->nullable();
            $table->string('threshold_period', 20)->nullable();
            $table->integer('min_branches')->nullable();
            $table->integer('max_branches')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->unique(['job_position_id', 'remuneration_tier_id', 'effective_from'], 'prc_pos_tier_date_uq');
        });

        Schema::create('remuneration_components', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->string('component_type', 30);
            $table->string('payment_method', 30)->nullable();
            $table->string('tax_treatment', 30)->nullable();
            $table->string('paid_by', 20);
            $table->string('calculation_type', 20);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('position_remuneration_component_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('remuneration_config_id');
            $table->unsignedBigInteger('remuneration_component_id');
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 7, 4)->nullable();
            $table->string('calculation_base', 50)->nullable();
            $table->boolean('is_included_in_gross')->default(false);
            $table->boolean('is_included_in_company_cost')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['remuneration_config_id', 'remuneration_component_id'], 'prcv_cfg_comp_uq');
        });

        Schema::create('remuneration_criteria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('remuneration_config_id');
            $table->string('criterion_name', 150);
            $table->string('criterion_code', 60);
            $table->string('measurement_type', 40);
            $table->string('operator', 15);
            $table->decimal('target_value', 14, 2)->nullable();
            $table->string('measurement_period', 20);
            $table->boolean('is_mandatory')->default(true);
            $table->string('verification_source', 100)->nullable();
            $table->timestamps();

            $table->unique(['remuneration_config_id', 'criterion_code'], 'rc_cfg_code_uq');
        });

        Schema::create('remuneration_housing_bands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_position_id');
            $table->unsignedBigInteger('remuneration_tier_id');
            $table->decimal('monthly_limit', 12, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            
        });

        Schema::create('remuneration_bonus_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_position_id')->nullable();
            $table->string('bonus_name', 150);
            $table->string('bonus_type', 30);
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 7, 4)->nullable();
            $table->string('performance_metric', 100)->nullable();
            $table->decimal('minimum_target', 12, 2)->nullable();
            $table->string('period', 20);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();

        });

        Schema::create('employee_remuneration_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('job_position_id');
            $table->unsignedBigInteger('remuneration_config_id');
            $table->boolean('acting_capacity')->default(false);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('approval_status', 20)->default('pending');
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            });

        Schema::create('remuneration_assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_remuneration_assignment_id');
            $table->date('assessment_period');
            $table->json('actual_performance')->nullable();
            $table->decimal('achievement_percentage', 7, 2)->nullable();
            $table->string('assessment_status', 30)->default('pending');
            $table->text('corrective_plan')->nullable();
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('decision', 30)->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remuneration_assessments');
        Schema::dropIfExists('employee_remuneration_assignments');
        Schema::dropIfExists('remuneration_bonus_rules');
        Schema::dropIfExists('remuneration_housing_bands');
        Schema::dropIfExists('remuneration_criteria');
        Schema::dropIfExists('position_remuneration_component_values');
        Schema::dropIfExists('remuneration_components');
        Schema::dropIfExists('position_remuneration_configs');
        Schema::dropIfExists('remuneration_tiers');
    }
};

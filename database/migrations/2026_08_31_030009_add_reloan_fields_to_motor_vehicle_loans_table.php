<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motor_vehicle_loans', function (Blueprint $table) {
            $table->unsignedBigInteger('referral_branch_id')->nullable()->after('current_custodian_alternative_contact');
            $table->unsignedBigInteger('referral_officer_id')->nullable()->after('referral_branch_id');
            $table->date('referral_date')->nullable()->after('referral_officer_id');
            $table->text('referral_notes')->nullable()->after('referral_date');
            $table->unsignedBigInteger('parent_loan_id')->nullable()->after('referral_notes');
            $table->boolean('is_top_up')->default(false)->after('parent_loan_id');
            $table->boolean('is_reloan')->default(false)->after('is_top_up');
            $table->string('loan_band')->nullable()->after('is_reloan');
            $table->string('incentive_status')->default('pending')->after('loan_band');
        });
    }

    public function down(): void
    {
        Schema::table('motor_vehicle_loans', function (Blueprint $table) {
            $table->dropColumn([
                'referral_branch_id', 'referral_officer_id', 'referral_date', 'referral_notes',
                'parent_loan_id', 'is_top_up', 'is_reloan', 'loan_band', 'incentive_status'
            ]);
        });
    }
};

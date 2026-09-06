<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('id');
            $table->unsignedBigInteger('loan_consultant_id')->nullable()->after('loan_officer_id');
            $table->unsignedBigInteger('branch_assessor_id')->nullable()->after('loan_consultant_id');
            $table->unsignedBigInteger('current_custodian_id')->nullable()->after('branch_assessor_id');
            $table->string('vehicle_status')->nullable()->after('current_custodian_id');
            $table->string('custody_status')->nullable()->after('vehicle_status');
            $table->string('current_storage_location')->nullable()->after('custody_status');
            $table->string('current_custodian_phone')->nullable()->after('current_storage_location');
            $table->string('current_custodian_nrc')->nullable()->after('current_custodian_phone');
            $table->string('current_custodian_alternative_contact')->nullable()->after('current_custodian_nrc');
            $table->decimal('vehicle_value', 18, 2)->nullable()->after('approved_amount');
            $table->decimal('ltv_percent', 5, 2)->nullable()->after('vehicle_value');
            $table->decimal('requested_amount', 18, 2)->nullable()->after('ltv_percent');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_id', 'loan_consultant_id', 'branch_assessor_id',
                'current_custodian_id', 'vehicle_status', 'custody_status',
                'current_storage_location', 'current_custodian_phone',
                'current_custodian_nrc', 'current_custodian_alternative_contact',
                'vehicle_value', 'ltv_percent', 'requested_amount'
            ]);
        });
    }
};

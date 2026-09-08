<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->boolean('is_motor_vehicle')->default(false)->after('name');
            $table->decimal('service_fee', 18, 2)->nullable()->after('maximum_principal');
            $table->decimal('processing_fee', 18, 2)->nullable()->after('service_fee');
            $table->decimal('insurance_fee', 18, 2)->nullable()->after('processing_fee');
            $table->decimal('valuation_fee', 18, 2)->nullable()->after('insurance_fee');
            $table->decimal('inspection_fee', 18, 2)->nullable()->after('valuation_fee');
            $table->decimal('penalty_rate', 5, 2)->nullable()->after('inspection_fee');
            $table->decimal('recovery_charges', 18, 2)->nullable()->after('penalty_rate');
            $table->date('effective_date')->nullable()->after('recovery_charges');
            $table->string('version')->nullable()->after('effective_date');
            $table->boolean('is_active')->default(false)->after('version');
            $table->unsignedBigInteger('approved_by_id')->nullable()->after('is_active');
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');

            $table->index(['is_motor_vehicle', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->dropIndex(['is_motor_vehicle', 'is_active']);
            $table->dropColumn([
                'is_motor_vehicle',
                'service_fee',
                'processing_fee',
                'insurance_fee',
                'valuation_fee',
                'inspection_fee',
                'penalty_rate',
                'recovery_charges',
                'effective_date',
                'version',
                'is_active',
                'approved_by_id',
                'approved_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mv_loan_product_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('min_loan_amount', 18, 2)->nullable();
            $table->decimal('max_loan_amount', 18, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('interest_type')->nullable();
            $table->integer('tenure_months')->nullable();
            $table->decimal('service_fee', 18, 2)->nullable();
            $table->decimal('processing_fee', 18, 2)->nullable();
            $table->decimal('insurance_fee', 18, 2)->nullable();
            $table->decimal('valuation_fee', 18, 2)->nullable();
            $table->decimal('inspection_fee', 18, 2)->nullable();
            $table->decimal('penalty_rate', 5, 2)->nullable();
            $table->decimal('recovery_charges', 18, 2)->nullable();
            $table->date('effective_date')->nullable();
            $table->string('version')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['code', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mv_loan_product_configurations');
    }
};

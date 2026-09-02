<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_valuations', function (Blueprint $table) {
            $table->unsignedBigInteger('valuator_id')->nullable()->after('vehicle_id');
            $table->string('valuation_company')->nullable()->after('valuator_id');
            $table->decimal('valuation_cost', 18, 2)->nullable()->after('forced_sale_value');
            $table->date('expiry_date')->nullable()->after('valuation_cost');
            $table->string('report_file_path')->nullable()->after('report_file');
            $table->text('photos')->nullable()->after('report_file_path');
            $table->text('supporting_documents')->nullable()->after('photos');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_valuations', function (Blueprint $table) {
            $table->dropColumn([
                'valuator_id', 'valuation_company', 'valuation_cost', 'expiry_date',
                'report_file_path', 'photos', 'supporting_documents'
            ]);
        });
    }
};

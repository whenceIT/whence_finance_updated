<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compliance_screenings', function (Blueprint $table) {
            $table->index(['client_id', 'motor_vehicle_loan_id', 'status'], 'compliance_screenings_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('compliance_screenings', function (Blueprint $table) {
            $table->dropIndex('compliance_screenings_status_idx');
        });
    }
};

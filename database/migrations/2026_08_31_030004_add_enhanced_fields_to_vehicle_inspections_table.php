<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->integer('mileage')->nullable()->after('vehicle_id');
            $table->text('mechanical_condition')->nullable()->after('condition_notes');
            $table->text('interior_condition')->nullable()->after('mechanical_condition');
            $table->text('exterior_condition')->nullable()->after('interior_condition');
            $table->text('tyres_condition')->nullable()->after('exterior_condition');
            $table->text('battery_condition')->nullable()->after('tyres_condition');
            $table->text('accessories_condition')->nullable()->after('battery_condition');
            $table->string('report_file_path')->nullable()->after('accessories_condition');
            $table->text('inspection_photos')->nullable()->after('report_file_path');
            $table->integer('condition_score')->nullable()->after('inspection_photos');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->dropColumn([
                'mileage', 'mechanical_condition', 'interior_condition', 'exterior_condition',
                'tyres_condition', 'battery_condition', 'accessories_condition',
                'report_file_path', 'inspection_photos', 'condition_score'
            ]);
        });
    }
};

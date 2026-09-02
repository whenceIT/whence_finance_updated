<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->string('house_owner_name')->nullable()->after('garage_contact_phone');
            $table->string('house_owner_nrc')->nullable()->after('house_owner_name');
            $table->string('house_owner_phone')->nullable()->after('house_owner_nrc');
            $table->string('alternative_contact_name')->nullable()->after('house_owner_phone');
            $table->string('alternative_contact_phone')->nullable()->after('alternative_contact_name');
            $table->date('storage_start_date')->nullable()->after('alternative_contact_phone');
            $table->date('storage_end_date')->nullable()->after('storage_start_date');
            $table->text('gps_location')->nullable()->after('garage_gps');
            $table->text('location_description')->nullable()->after('gps_location');
            $table->timestamp('intake_date')->nullable()->after('location_description');
            $table->boolean('keys_received')->default(false)->after('intake_date');
            $table->boolean('documents_received')->default(false)->after('keys_received');
            $table->boolean('accessories_received')->default(false)->after('documents_received');
            $table->integer('fuel_level')->nullable()->after('accessories_received');
            $table->text('intake_photos')->nullable()->after('fuel_level');
            $table->string('signed_intake_form_path')->nullable()->after('intake_photos');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->dropColumn([
                'house_owner_name', 'house_owner_nrc', 'house_owner_phone',
                'alternative_contact_name', 'alternative_contact_phone', 'storage_start_date',
                'storage_end_date', 'gps_location', 'location_description', 'intake_date',
                'keys_received', 'documents_received', 'accessories_received', 'fuel_level',
                'intake_photos', 'signed_intake_form_path'
            ]);
        });
    }
};

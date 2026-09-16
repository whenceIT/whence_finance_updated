<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->decimal('storage_charges', 15, 2)->nullable()->after('signed_intake_form_path');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_custody', function (Blueprint $table) {
            $table->dropColumn('storage_charges');
        });
    }
};

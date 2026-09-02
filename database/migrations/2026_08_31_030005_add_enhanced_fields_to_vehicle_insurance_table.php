<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_insurance', function (Blueprint $table) {
            $table->decimal('premium', 18, 2)->nullable()->after('insured_value');
            $table->string('cover_type')->nullable()->after('premium');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_insurance', function (Blueprint $table) {
            $table->dropColumn(['premium', 'cover_type']);
        });
    }
};

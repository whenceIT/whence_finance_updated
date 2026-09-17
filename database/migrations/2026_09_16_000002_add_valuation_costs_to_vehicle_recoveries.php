<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_recoveries', function (Blueprint $table) {
            $table->decimal('valuation_costs', 15, 2)->default(0)->after('penalties');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_recoveries', function (Blueprint $table) {
            $table->dropColumn('valuation_costs');
        });
    }
};

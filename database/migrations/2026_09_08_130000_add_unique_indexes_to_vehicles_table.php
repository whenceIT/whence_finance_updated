<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unique('registration_number');
            $table->unique('engine_number');
            $table->unique('chassis_number');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['registration_number']);
            $table->dropUnique(['engine_number']);
            $table->dropUnique(['chassis_number']);
        });
    }
};

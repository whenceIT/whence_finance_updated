<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_types', function (Blueprint $table) {
            $table->string('method')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_types', function (Blueprint $table) {
            $table->dropIfExists('method');
        });
    }
};
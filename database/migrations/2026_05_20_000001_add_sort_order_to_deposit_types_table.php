<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_types', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('name');
            $table->decimal('monthly_amount')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_types', function (Blueprint $table) {
            $table->dropIfExists('sort_order');
            $table->dropIfExists('monthly_amount');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_matrices', function (Blueprint $table) {
            $table->index(['level', 'branch_id', 'district_id', 'province_id', 'is_active'], 'approval_matrices_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('approval_matrices', function (Blueprint $table) {
            $table->dropIndex('approval_matrices_active_idx');
        });
    }
};

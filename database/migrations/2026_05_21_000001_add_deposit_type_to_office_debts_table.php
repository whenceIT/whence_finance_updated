<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a nullable deposit_type_id column to office_debts so each debt record
     * can be associated with a specific deposit type.
     */
    public function up(): void
    {
        Schema::table('office_debts', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit_type_id')->nullable()->after('office_id');
            $table->index('deposit_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('office_debts', function (Blueprint $table) {
            $table->dropForeign(['deposit_type_id']);
            $table->dropColumn('deposit_type_id');
        });
    }
};

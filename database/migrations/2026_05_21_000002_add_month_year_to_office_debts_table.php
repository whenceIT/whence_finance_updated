<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Month and year of the debt record (e.g. the billing / inventory period
     * the obligation relates to).
     */
    public function up(): void
    {
        Schema::table('office_debts', function (Blueprint $table) {
            $table->unsignedTinyInteger('debt_month')->nullable()->after('debt_status');
            $table->unsignedSmallInteger('debt_year')->nullable()->after('debt_month');
            $table->index(['debt_year', 'debt_month']);
        });
    }

    public function down(): void
    {
        Schema::table('office_debts', function (Blueprint $table) {
            $table->dropIndex(['debt_year', 'debt_month']);
            $table->dropColumn(['debt_month', 'debt_year']);
        });
    }
};

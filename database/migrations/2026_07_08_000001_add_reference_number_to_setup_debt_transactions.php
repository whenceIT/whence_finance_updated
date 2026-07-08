<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setup_debt_transactions') && !Schema::hasColumn('setup_debt_transactions', 'reference_number')) {
            Schema::table('setup_debt_transactions', function (Blueprint $table) {
                $table->string('reference_number')->nullable()->after('transaction_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setup_debt_transactions') && Schema::hasColumn('setup_debt_transactions', 'reference_number')) {
            Schema::table('setup_debt_transactions', function (Blueprint $table) {
                $table->dropColumn('reference_number');
            });
        }
    }
};

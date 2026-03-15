<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recovery_payments', function (Blueprint $table) {
            // Add payment_reference column (alternative name for reference)
            if (!Schema::hasColumn('recovery_payments', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('reference');
            }
            
            // Add bank_name column
            if (!Schema::hasColumn('recovery_payments', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('payment_reference');
            }
            
            // Add outstanding_before column
            if (!Schema::hasColumn('recovery_payments', 'outstanding_before')) {
                $table->decimal('outstanding_before', 15, 2)->nullable()->after('bank_name');
            }
            
            // Add outstanding_after column
            if (!Schema::hasColumn('recovery_payments', 'outstanding_after')) {
                $table->decimal('outstanding_after', 15, 2)->nullable()->after('outstanding_before');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recovery_payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'bank_name',
                'outstanding_before',
                'outstanding_after',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            // Add a composite index to improve query performance when checking for existing audits
            // This helps prevent duplicate audits for the same office and period
            $table->index(['office_id', 'audit_date', 'period_start', 'period_end'], 'audit_unique_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            $table->dropIndex('audit_unique_period_idx');
        });
    }
};

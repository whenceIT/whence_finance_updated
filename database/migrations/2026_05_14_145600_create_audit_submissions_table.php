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
        Schema::create('audit_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('auditor_id');
            $table->date('audit_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('auditor_name');
            $table->text('audit_scope')->nullable(); // JSON or comma separated
            $table->text('opening_remarks')->nullable();
            $table->string('audit_type')->nullable();
            $table->string('unannounced')->nullable();
            $table->string('manager_present')->nullable();
            $table->string('manager_name')->nullable();
            $table->integer('fail_count')->default(0);
            $table->string('risk_rating'); // low, medium, high, critical
            $table->text('notes')->nullable();

            // Section 2 items
            for ($i = 1; $i <= 10; $i++) {
                $table->string("s2_{$i}")->nullable();
                $table->text("s2_{$i}_notes")->nullable();
            }
            $table->text('s2_notes')->nullable();

            // Section 3 items
            $table->integer('s3_total_active')->nullable();
            $table->integer('s3_incomplete_files')->nullable();
            for ($i = 1; $i <= 7; $i++) {
                $table->string("s3_{$i}")->nullable();
                $table->text("s3_{$i}_notes")->nullable();
            }
            $table->text('s3_notes')->nullable();

            // Section 4 items
            $table->decimal('s4_system_collections', 15, 2)->nullable();
            $table->decimal('s4_wallet_collections', 15, 2)->nullable();
            for ($i = 1; $i <= 2; $i++) {
                $table->string("s4_{$i}")->nullable();
                $table->text("s4_{$i}_notes")->nullable();
            }
            $table->text('s4_notes')->nullable();

            // Section 5 items
            for ($i = 1; $i <= 7; $i++) {
                $table->string("s5_{$i}")->nullable();
                $table->text("s5_{$i}_notes")->nullable();
            }
            $table->text('s5_notes')->nullable();

            // Section 6 items
            $table->integer('s6_total_staff')->nullable();
            for ($i = 1; $i <= 8; $i++) {
                $table->string("s6_{$i}")->nullable();
                $table->text("s6_{$i}_notes")->nullable();
            }
            $table->text('s6_notes')->nullable();

            // Section 7 items
            for ($i = 1; $i <= 8; $i++) {
                $table->string("s7_{$i}")->nullable();
                $table->text("s7_{$i}_notes")->nullable();
            }
            $table->text('s7_notes')->nullable();

            // Section 8 items
            for ($i = 1; $i <= 6; $i++) {
                $table->string("s8_{$i}")->nullable();
                $table->text("s8_{$i}_notes")->nullable();
            }
            $table->text('s8_notes')->nullable();

            // Section 9 items
            for ($i = 1; $i <= 2; $i++) {
                $table->string("s9_{$i}")->nullable();
                $table->text("s9_{$i}_notes")->nullable();
            }
            $table->text('s9_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_submissions');
    }
};

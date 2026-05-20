<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the office_debts table.
     *
     * Tracks per-branch debt obligations so that:
     * - Admins can record which branches are carrying an outstanding debt.
     * - The outstanding balance reduces progressively as qualifying deposits are made.
     */
    public function up(): void
    {
        Schema::create('office_debts', function (Blueprint $table) {
            $table->id();

            // Branch this debt belongs to (nullable = unassigned / soft-removable when branch is deleted).
            // Must be unsigned INT to match offices.id (int(10) unsigned on this installation).
            $table->unsignedInteger('office_id')->nullable();

            $table->foreign('office_id')
                  ->references('id')
                  ->on('offices')
                  ->nullOnDelete();

            // Display label for this branch's current standing (colour / semantic toggle)
            $table->string('debt_status', 30)->default('owing');

            // Original debt amount allocated to this branch (immutable record of the initial figure)
            $table->unsignedBigInteger('original_amount')->default(0);

            // Balance still outstanding after qualifying deposits have been credited
            $table->unsignedBigInteger('outstanding_amount')->default(0);

            // Optional free-text notes — e.g. flag a debt manually waived, under dispute, or converted
            $table->text('notes')->nullable();

            $table->timestamps();

            // Per-office look-ups: list debt rows for a specific branch instantly
            $table->index('office_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_debts');
    }
};

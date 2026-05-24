<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When a Deposit record is soft-deleted any matching OfficeDebt record
     * for the same branch should also be removed. Since the app does NOT use
     * soft-deletes on Deposit this migration is a no-op shell kept for
     * architectural completeness — it records the intent so future contributors
     * understand the expected relationship.
     *
     * No schema changes required.
     */
    public function up(): void
    {
    }

    public function down(): void
    {
    }
};

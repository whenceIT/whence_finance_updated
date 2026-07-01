<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('setup_debt_transactions')) {
            Schema::create('setup_debt_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('setup_debt_cost_id');
                $table->unsignedBigInteger('office_id');
                $table->decimal('amount', 15, 2)->default(0);
                $table->date('transaction_date')->default(now());
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_debt_transactions');
    }
};

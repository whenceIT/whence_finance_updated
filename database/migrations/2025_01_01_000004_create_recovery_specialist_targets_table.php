<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_specialist_targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('specialist_id');
            $table->enum('category', ['cross_branch', 'escalated', 'dormant', 'legal', 'skip_trace']);
            $table->year('year');
            $table->tinyInteger('month'); // 1–12
            $table->decimal('target_amount', 15, 2);
            $table->integer('target_cases')->default(0);
            $table->timestamps();

            $table->unique(['specialist_id', 'category', 'year', 'month'], 'rst_specialist_category_year_month_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_specialist_targets');
    }
};

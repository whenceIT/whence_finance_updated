<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_recoveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->text('recovery_actions')->nullable();
            $table->text('communications')->nullable();
            $table->text('promises_arrangements')->nullable();
            $table->decimal('repossession_costs', 15, 2)->default(0);
            $table->decimal('legal_costs', 15, 2)->default(0);
            $table->decimal('other_expenses', 15, 2)->default(0);
            $table->decimal('penalties', 15, 2)->default(0);
            $table->string('current_recovery_stage')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_recoveries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('loan_amount');
            $table->unsignedInteger('months_3');
            $table->unsignedInteger('months_6');
            $table->unsignedInteger('months_9');
            $table->unsignedInteger('months_12');
            $table->unsignedInteger('months_18');
            $table->unsignedInteger('months_24');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_loan_schedules');
    }
};

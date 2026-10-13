<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveryCostsTable extends Migration
{
    public function up()
    {
        Schema::create('recovery_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('case_id')->nullable();    // recovery_cases.id is bigint
            $table->unsignedInteger('office_id')->nullable();     // offices.id is int
            $table->unsignedInteger('recorded_by')->nullable();   // users.id is int
            $table->date('cost_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('case_id')->references('id')->on('recovery_cases')->onDelete('set null');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recovery_costs');
    }
}

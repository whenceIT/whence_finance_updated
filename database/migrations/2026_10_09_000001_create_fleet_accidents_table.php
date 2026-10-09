<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetAccidentsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('fleet_accidents');
        Schema::create('fleet_accidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('fleet_id');
            $table->date('accident_date');
            $table->string('driver')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('police_report_number')->nullable();
            $table->string('insurance_claim_number')->nullable();
            $table->text('repair_details')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('document_path')->nullable();   // uploaded supporting doc
            $table->string('recorded_by')->nullable();    // user who recorded it
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fleet_accidents');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetServiceRecordsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('fleet_service_records');
        Schema::create('fleet_service_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('fleet_id');
            $table->date('service_date');
            $table->string('service_type');           // e.g. Oil Change, Tyre Rotation, Repair
            $table->text('description')->nullable();  // work done
            $table->text('parts_replaced')->nullable();
            $table->string('workshop')->nullable();   // workshop / service provider
            $table->unsignedInteger('odometer_reading')->nullable(); // km
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('document_path')->nullable(); // invoice / receipt
            $table->string('recorded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fleet_service_records');
    }
}

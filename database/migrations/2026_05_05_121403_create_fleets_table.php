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
        Schema::create('fleets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vehicle_id')->nullable()->index();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('assigned_to')->nullable();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->string('color')->nullable();
            $table->date('date_purchased')->nullable();
            $table->date('insurance_expire_date')->nullable();
            $table->date('last_service_date')->nullable();
            $table->decimal('current_value', 12, 2)->default(0);
            $table->enum('white_book', ['available', 'none'])->default('none');
            $table->string('vehicle_status')->nullable();
            $table->date('last_maintenance')->nullable();
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
        Schema::dropIfExists('fleets');
    }
};

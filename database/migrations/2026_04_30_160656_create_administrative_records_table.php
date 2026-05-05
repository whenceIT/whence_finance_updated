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
        Schema::create('administrative_records', function (Blueprint $table) {
            $table->bigIncrement('id');
            $table->unsignedInteger('employee_id');
            $table->enum('record_type', ['disciplinary', 'health', 'career']);
            $table->string('disciplinary_type')->nullable();
            $table->string('warning_type')->nullable();
            $table->string('warning_level')->nullable();
            $table->string('health_type')->nullable();
            $table->string('incident_type')->nullable();
            $table->string('career_type')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->date('recording_date')->nullable();
            $table->text('comments')->nullable();
            $table->integer('number_of_days')->nullable();
            $table->json('absence_dates')->nullable();
            $table->enum('status', ['pending', 'active', 'declined'])->default('pending');
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users');

            $table->foreign('employee_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrative_records');
    }
};

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('savings_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('loan_application_id')->nullable();
            $table->tinyInteger('is_client')->default(0);
            $table->integer('client_relationship_id')->nullable();
            $table->decimal('amount', 65, 4)->nullable();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unspecified'])->nullable();
            $table->date('dob')->nullable();
            $table->string('street')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('picture')->nullable();
            $table->string('work')->nullable();
            $table->text('work_address')->nullable();
            $table->tinyInteger('lock_funds')->default(0);
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
        Schema::dropIfExists('guarantors');
    }
}

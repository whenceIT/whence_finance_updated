<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('referred_by_id')->nullable();
            $table->string('account_no')->nullable();
            $table->string('external_id')->nullable();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('incorporation_number')->nullable();
            $table->string('display_name')->nullable();
            $table->string('picture')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unspecified'])->nullable();
            $table->enum('client_type', ['individual', 'business', 'ngo', 'other'])->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'declined', 'closed'])->default('pending');
            $table->enum('marital_status', ['married', 'single', 'divorced', 'widowed', 'unspecified'])->nullable();
            $table->date('dob')->nullable();
            $table->string('street')->nullable();
            $table->string('ward')->nullable();
            $table->string('district')->nullable();
            $table->string('region')->nullable();
            $table->text('address')->nullable();
            $table->date('joined_date')->nullable();
            $table->date('activated_date')->nullable();
            $table->date('reactivated_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->text('declined_reason')->nullable();
            $table->text('closed_reason')->nullable();
            $table->date('closed_date')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->text('inactive_reason')->nullable();
            $table->date('inactive_date')->nullable();
            $table->integer('inactive_by_id')->nullable();
            $table->integer('activated_by_id')->nullable();
            $table->integer('reactivated_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->integer('closed_by_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->nullable();
            $table->string('name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('external_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->date('joined_date')->nullable();
            $table->date('activated_date')->nullable();
            $table->date('reactivated_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->text('declined_reason')->nullable();
            $table->text('closed_reason')->nullable();
            $table->date('closed_date')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->integer('activated_by_id')->nullable();
            $table->integer('reactivated_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->integer('closed_by_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('street')->nullable();
            $table->string('ward')->nullable();
            $table->string('district')->nullable();
            $table->string('region')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'declined', 'closed'])->default('pending');
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
        Schema::dropIfExists('groups');
    }
}

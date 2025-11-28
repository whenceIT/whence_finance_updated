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
        Schema::create('resignation_letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->date('resignation_date');
            $table->text('reason');
            $table->string('letter_path')->nullable();
            $table->enum('status', ['pending', 'manager_approved', 'admin_approved', 'declined'])->default('pending');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('manager_comment')->nullable();
            $table->text('admin_comment')->nullable();
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
        Schema::dropIfExists('resignation_letters');
    }
};

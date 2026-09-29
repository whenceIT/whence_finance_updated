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
        Schema::create('office_blocking_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('office_id');
            $table->unsignedInteger('blocked_count')->default(1);
            $table->text('reason')->nullable();
            $table->timestamp('last_blocked_at')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->unique('office_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_blocking_histories');
    }
};

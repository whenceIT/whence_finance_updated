<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recoveries_dept_excalated_shares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('recovery_case_id');
            $table->unsignedBigInteger('recovery_payment_id')->nullable();
            $table->decimal('dept_share_amount', 12, 2);
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recoveries_dept_excalated_shares');
    }
};
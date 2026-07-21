<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedTinyInteger('pscan')->default(0);
            $table->unsignedTinyInteger('cscan')->default(0);
        });
    }

    public function down()
    {
    }
};

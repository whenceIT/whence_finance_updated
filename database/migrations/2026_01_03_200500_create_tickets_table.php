<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $row) {
            $row->increments('id');
            $row->string('name');
            $row->text('description')->nullable();
            $row->dateTime('datetime_open');
            $row->dateTime('datetime_close')->nullable();
            $row->integer('opened_by')->unsigned()->nullable();
            $row->integer('assigned_to')->unsigned()->nullable();
            $row->integer('closed_by')->unsigned()->nullable();
            $row->string('status')->default('open');
            $row->string('priority')->default('medium');
            $row->string('department')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}

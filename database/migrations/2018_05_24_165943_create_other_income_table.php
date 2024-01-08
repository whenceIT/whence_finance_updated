<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_income', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->unsigned()->nullable();
            $table->integer('created_by_id')->unsigned()->nullable();
            $table->integer('other_income_type_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->decimal('amount', 65, 2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->text('notes')->nullable();
            $table->text('files')->nullable();
            $table->enum('status',
                array('pending', 'approved', 'declined'))->default('approved');
            $table->date('approved_date')->nullable();
            $table->integer('approved_by_id')->unsigned()->nullable();
            $table->date('declined_date')->nullable();
            $table->integer('declined_by_id')->unsigned()->nullable();
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
        Schema::dropIfExists('other_income');
    }
}

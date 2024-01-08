<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_id')->unsigned();
            $table->integer('payroll_template_meta_id')->unsigned()->nullable();
            $table->decimal('value', 65, 2)->nullable();
            $table->tinyInteger('is_tax')->default(0)->nullable();
            $table->tinyInteger('is_percentage')->default(0)->nullable();
            $table->enum('position',
                array('top_left', 'top_right', 'bottom_left', 'bottom_right'))->default('bottom_left')->nullable();
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
        Schema::dropIfExists('payroll_meta');
    }
}

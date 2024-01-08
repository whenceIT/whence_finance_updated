<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollTemplateMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_template_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_template_id')->unsigned();
            $table->string('name')->nullable();
            $table->enum('position',
                array('top_left', 'top_right', 'bottom_left', 'bottom_right', 'none'))->default('bottom_left')->nullable();
            $table->enum('type',
                array('addition', 'deduction'))->default('addition')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->tinyInteger('is_tax')->default(0);
            $table->tinyInteger('is_percentage')->default(0);
            $table->enum('tax_on',
                array('net', 'gross'))->default('net')->nullable();
            $table->decimal('default_value', 65, 2)->nullable();
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
        Schema::dropIfExists('payroll_template_meta');
    }
}

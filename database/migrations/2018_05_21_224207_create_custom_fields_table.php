<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->string('category')->nullable();
            $table->string('name')->nullable();
            $table->enum('field_type', ['number', 'textfield', 'date', 'decimal', 'textarea', 'checkbox', 'radiobox', 'select'])->default('textfield');
            $table->tinyInteger('required')->default(0);
            $table->text('radio_box_values')->nullable();
            $table->text('checkbox_values')->nullable();
            $table->text('select_values')->nullable();
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
        Schema::dropIfExists('custom_fields');
    }
}

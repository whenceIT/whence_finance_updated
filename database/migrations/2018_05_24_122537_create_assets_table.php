<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->integer('asset_type_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->string('name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 65, 2)->nullable();
            $table->decimal('value', 65, 2)->nullable();
            $table->integer('life_span')->nullable();
            $table->decimal('salvage_value', 65, 2)->nullable();
            $table->text('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->text('files')->nullable();
            $table->text('purchase_year')->nullable();
            $table->enum('status', ["active", "inactive", "sold", "damaged", "written_off"])->nullable();
            $table->tinyInteger('active')->default(0)->nullable();
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
        Schema::dropIfExists('assets');
    }
}

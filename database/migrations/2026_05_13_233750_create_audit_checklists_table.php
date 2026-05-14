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
        Schema::create('audit_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // e.g., 's1', 's2'
            $table->string('item_id'); // e.g., 's1_audit_date'
            $table->text('label');
            $table->text('check_text')->nullable();
            $table->text('verify_text');
            $table->text('flag_text');
            $table->string('input_type')->default('text'); // text, date, select
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('audit_checklists');
    }
};

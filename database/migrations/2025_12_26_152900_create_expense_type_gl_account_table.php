<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseTypeGlAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_type_gl_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('expense_type_id');
            $table->unsignedBigInteger('gl_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_type_gl_account');
    }
}

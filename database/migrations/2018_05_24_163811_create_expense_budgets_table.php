<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->unsigned()->nullable();
            $table->integer('office_id')->unsigned()->nullable();
            $table->integer('expense_type_id')->nullable();
            $table->string('name')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 65, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status',
                array('pending', 'approved', 'declined'))->default('approved');
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
        Schema::dropIfExists('expense_budgets');
    }
}

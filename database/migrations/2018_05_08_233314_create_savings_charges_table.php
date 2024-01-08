<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('savings_id')->nullable();
            $table->integer('charge_id')->nullable();
            $table->tinyInteger('penalty')->default(0);
            $table->tinyInteger('waived')->default(0);
            $table->enum('charge_type',
                array(
                    'savings_activation',
                    'withdrawal_fee',
                    'annual_fee',
                    'monthly_fee',
                    'specified_due_date',
                ));
            $table->enum('charge_option',
                array(
                    'flat',
                    'percentage',
                ));
            $table->decimal('amount', 65, 2)->nullable();
            $table->decimal('amount_paid', 65, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->integer('grace_period')->default(0);
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
        Schema::dropIfExists('savings_charges');
    }
}

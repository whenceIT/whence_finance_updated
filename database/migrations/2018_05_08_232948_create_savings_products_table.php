<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('decimals')->default(2);
            $table->decimal('interest_rate', 65, 4)->nullable();
            $table->tinyInteger('allow_overdraft')->default(0);
            $table->decimal('minimum_balance', 65, 4)->nullable();
            $table->enum('interest_compounding_period', ['daily', 'monthly', 'quarterly', 'biannual', 'annually'])->nullable();
            $table->enum('interest_posting_period', ['monthly', 'quarterly', 'biannual', 'annually'])->nullable();
            $table->enum('interest_calculation_type', ['daily', 'average'])->nullable();
            $table->tinyInteger('allow_transfer_withdrawal_fee')->default(0);
            $table->decimal('opening_balance', 65, 4)->nullable();
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->enum('year_days', ['360', '365'])->default('365');
            $table->enum('accounting_rule', ['none', 'cash'])->default('cash');
            $table->integer('gl_account_savings_reference_id')->nullable();
            $table->integer('gl_account_overdraft_portfolio_id')->nullable();
            $table->integer('gl_account_savings_control_id')->nullable();
            $table->integer('gl_account_interest_on_savings_id')->nullable();
            $table->integer('gl_account_savings_written_off_id')->nullable();
            $table->integer('gl_account_income_interest_id')->nullable();
            $table->integer('gl_account_income_fee_id')->nullable();
            $table->integer('gl_account_income_penalty_id')->nullable();
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
        Schema::dropIfExists('savings_products');
    }
}

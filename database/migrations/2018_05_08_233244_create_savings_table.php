<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('client_type', ['client', 'group'])->default('client');
            $table->integer('client_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('field_officer_id')->nullable();
            $table->integer('savings_product_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('account_number')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('decimals')->default(2);
            $table->decimal('interest_rate', 65, 4)->nullable();
            $table->tinyInteger('allow_overdraft')->default(0);
            $table->decimal('minimum_balance', 65, 4)->nullable();
            $table->decimal('overdraft_limit', 65, 4)->nullable();
            $table->enum('interest_compounding_period', ['daily', 'monthly', 'quarterly', 'biannual', 'annually'])->nullable();
            $table->enum('interest_posting_period', ['monthly', 'quarterly', 'biannual', 'annually'])->nullable();
            $table->tinyInteger('allow_transfer_withdrawal_fee')->default(0);
            $table->decimal('opening_balance', 65, 4)->nullable();
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->enum('year_days', ['360', '365'])->default('365');
            $table->enum('status', array(
                'pending',
                'approved',
                'closed',
                'declined',
                'withdrawn'
            ))->default('pending');
            $table->integer('created_by_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('closed_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->date('created_date')->nullable();
            $table->date('modified_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->text('notes')->nullable();
            $table->text('approved_notes')->nullable();
            $table->text('declined_notes')->nullable();
            $table->text('closed_notes')->nullable();
            $table->decimal('balance', 65, 4)->nullable();
            $table->decimal('deposits', 65, 4)->nullable();
            $table->decimal('interest_earned', 65, 4)->nullable();
            $table->decimal('interest_posted', 65, 4)->nullable();
            $table->decimal('interest_overdraft', 65, 4)->nullable();
            $table->decimal('withdrawals', 65, 4)->nullable();
            $table->decimal('fees', 65, 4)->nullable();
            $table->decimal('penalty', 65, 4)->nullable();
            $table->date('start_interest_calculation_date')->nullable();
            $table->date('last_interest_calculation_date')->nullable();
            $table->date('next_interest_calculation_date')->nullable();
            $table->date('next_interest_posting_date')->nullable();
            $table->date('last_interest_posting_date')->nullable();
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
        Schema::dropIfExists('savings');
    }
}

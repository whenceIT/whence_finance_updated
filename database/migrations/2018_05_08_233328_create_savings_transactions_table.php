<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('modified_by_id')->nullable();
            $table->integer('payment_detail_id')->nullable();
            $table->integer('savings_id')->unsigned()->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->decimal('debit', 65, 4)->nullable();
            $table->decimal('credit', 65, 4)->nullable();
            $table->decimal('balance', 65, 4)->nullable();
            $table->enum('transaction_type', array(
                'deposit',
                'withdrawal',
                'bank_fees',
                'interest',
                'dividend',
                'guarantee',
                'guarantee_restored',
                'fees_payment',
                'transfer_loan',
                'transfer_savings',
                'specified_due_date_fee'
            ))->nullable();
            $table->tinyInteger('reversible')->default(0);
            $table->tinyInteger('reversed')->default(0);
            $table->enum('reversal_type',
                [
                    'system',
                    'user',
                    'none'
                ])->default('none');
            $table->enum('status',
                [
                    'pending',
                    'approved',
                    'declined'
                ])->default('pending')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->tinyInteger('system_interest')->default(0);
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->text('notes')->nullable();
            $table->date('balance_date')->nullable();
            $table->integer('balance_days')->nullable();
            $table->integer('cumulative_balance_days')->nullable();
            $table->decimal('cumulative_balance', 65, 4)->nullable();
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
        Schema::dropIfExists('savings_transactions');
    }
}

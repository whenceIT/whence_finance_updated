<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeLoanTransactionsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('office_loan_transactions');
        Schema::create('office_loan_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('loan_id');
            $table->unsignedInteger('office_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->unsignedInteger('approved_by')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_loan_transactions');
    }
}

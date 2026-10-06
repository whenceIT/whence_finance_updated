<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeLoanTransactionsTable extends Migration
{
    public function up()
    {
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

            $table->index('loan_id');
            $table->index('office_id');
            $table->index('approved_by');
            $table->index('status');

            $table->foreign('loan_id')
                  ->references('id')->on('office_loans')
                  ->onDelete('cascade');

            $table->foreign('office_id')
                  ->references('id')->on('offices')
                  ->onDelete('restrict');

            $table->foreign('approved_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_loan_transactions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionTypeToOfficeLoanTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('office_loan_transactions', function (Blueprint $table) {
            $table->enum('transaction_type', [
                'disbursement',
                'interest_initial',
                'penalty',
                'topup',
                'waiver',
            ])->default('disbursement')->after('status');
        });
    }

    public function down()
    {
        Schema::table('office_loan_transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
}

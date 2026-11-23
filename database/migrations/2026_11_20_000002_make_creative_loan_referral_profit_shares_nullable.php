<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCreativeLoanReferralProfitSharesNullable extends Migration
{
    public function up()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->decimal('profit_share', 10, 2)->default(0.00)->nullable()->change();
            $table->decimal('amount_paid', 10, 2)->default(0.00)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->decimal('profit_share', 10, 2)->default(0.00)->nullable(false)->change();
            $table->decimal('amount_paid', 10, 2)->default(0.00)->nullable(false)->change();
        });
    }
}

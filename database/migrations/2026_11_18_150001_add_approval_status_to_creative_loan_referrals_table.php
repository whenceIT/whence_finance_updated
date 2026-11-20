<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalStatusToCreativeLoanReferralsTable extends Migration
{
    public function up()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'declined'])
                ->default('pending');
        });
    }

    public function down()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
}

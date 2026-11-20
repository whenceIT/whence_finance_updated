<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientProfileFieldsToCreativeLoanReferralsTable extends Migration
{
    public function up()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->string('fn_name')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('client_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('address')->nullable();
            $table->string('working_place')->nullable();
            $table->string('working_position')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('employer')->nullable();
            $table->string('business_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('creative_loan_referrals', function (Blueprint $table) {
            $table->dropColumn([
                'fn_name',
                'lname',
                'email',
                'dob',
                'gender',
                'client_type',
                'marital_status',
                'address',
                'working_place',
                'working_position',
                'salary',
                'employer',
                'business_name',
            ]);
        });
    }
}

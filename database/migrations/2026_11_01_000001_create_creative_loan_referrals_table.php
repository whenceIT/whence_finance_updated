<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCreativeLoanReferralsTable extends Migration
{
    public function up()
    {
        Schema::create('creative_loan_referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referral_reference')->unique();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('client_name');
            $table->string('nrc_number')->nullable()->index();
            $table->string('contact_number')->nullable();
            $table->string('referring_department')->nullable();
            $table->date('referral_date');
            $table->unsignedBigInteger('referring_officer_id')->nullable();
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->string('loan_status')->default('Pending');
            $table->string('repayment_status')->default('Unpaid');
            $table->decimal('profit_share', 10, 2)->default(0.00);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('creative_loan_referrals'); }
}

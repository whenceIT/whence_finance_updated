<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCreativeLostClientRecoveriesTable extends Migration
{
    public function up()
    {
        Schema::create('creative_lost_client_recoveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('recovery_reference')->unique();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('client_name');
            $table->string('nrc_number')->nullable()->index();
            $table->string('contact_number')->nullable();
            $table->date('recovery_date');
            $table->unsignedBigInteger('responsible_staff_id')->nullable();
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->string('repayment_status')->default('Unpaid');
            $table->decimal('profit_share', 10, 2)->default(0.00);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('creative_lost_client_recoveries'); }
}

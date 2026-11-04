<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeCustomerCareCasesTable extends Migration
{
    public function up()
    {
        Schema::create('creative_customer_care_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('case_reference')->unique();
            $table->unsignedBigInteger('branch_id')->unsigned()->nullable();
            $table->unsignedBigInteger('loan_id')->unsigned()->nullable();
            $table->string('client_name');
            $table->string('nrc_number')->nullable()->index();
            $table->string('contact_number')->nullable();
            $table->string('complaint_type');
            $table->text('complaint_description');
            $table->unsignedBigInteger('reported_by_user_id')->unsigned()->nullable();
            $table->date('date_reported');
            $table->unsignedBigInteger('assigned_to_user_id')->unsigned()->nullable();
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Escalated'])->default('Open');
            $table->text('actions_taken')->nullable();
            $table->text('resolution_details')->nullable();
            $table->date('resolution_date')->nullable();
            $table->text('follow_up_actions')->nullable();
            $table->unsignedBigInteger('escalated_to')->unsigned()->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('creative_customer_care_cases');
    }
}

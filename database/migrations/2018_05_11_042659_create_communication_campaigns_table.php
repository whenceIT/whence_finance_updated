<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->enum('type', ['sms', 'email'])->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->date('report_start_date')->nullable();
            $table->string('report_start_time')->nullable();
            $table->enum('recurrence_type', ['none', 'schedule'])->nullable();
            $table->enum('recur_frequency', ['days', 'months', 'weeks', 'years'])->nullable();
            $table->string('recur_interval')->nullable();
            $table->text('email_recipients')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('message')->nullable();
            $table->enum('email_attachment_file_format', ['pdf', 'csv', 'xls'])->nullable();
            $table->enum('recipients_category', ['all_clients', 'active_clients', 'prospective_clients', 'active_loans', 'loans_in_arrears', 'overdue_loans', 'happy_birthday'])->nullable();
            $table->enum('report_attachment', [
                'loan_schedule',
                'loan_statement',
                'savings_statement',
                'audit_report',
                'group_indicator_report',
            ])->nullable();
            $table->string('from_day')->nullable();
            $table->string('to_day')->nullable();
            $table->string('office_id')->nullable();
            $table->string('loan_officer_id')->nullable();
            $table->string('gl_account_id')->nullable();
            $table->string('manual_entries')->nullable();
            $table->string('loan_status')->nullable();
            $table->string('loan_product_id')->nullable();
            $table->date('last_run_date')->nullable();
            $table->date('next_run_date')->nullable();
            $table->date('last_run_time')->nullable();
            $table->date('next_run_time')->nullable();
            $table->integer('number_of_runs')->default(0);
            $table->integer('number_of_recipients')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('sent')->default(0);
            $table->enum('status', ['pending', 'active', 'declined', 'inactive'])->default('pending');
            $table->integer('approved_by_id')->nullable();
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
        Schema::dropIfExists('communication_campaigns');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportSchedulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_scheduler', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by_id')->nullable();
            $table->text('description')->nullable();
            $table->date('report_start_date')->nullable();
            $table->string('report_start_time')->nullable();
            $table->enum('recurrence_type', ['none', 'schedule'])->nullable();
            $table->enum('recur_frequency', ['daily', 'monthly', 'weekly', 'yearly'])->nullable();
            $table->string('recur_interval')->nullable();
            $table->text('email_recipients')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_message')->nullable();
            $table->enum('email_attachment_file_format', ['pdf', 'csv', 'xls'])->nullable();
            $table->enum('report_category', ['client_report', 'loan_report', 'financial_report', 'group_report', 'savings_report', 'organisation_report'])->nullable();
            $table->enum('report_name', [
                'disbursed_loans_report',
                'loan_portfolio_report',
                'expected_repayments_report',
                'repayments_report',
                'collection_report',
                'arrears_report',
                'balance_sheet',
                'trial_balance',
                'profit_and_loss',
                'cash_flow',
                'provisioning',
                'historical_income_statement',
                'journals_report',
                'accrued_interest',
                'client_numbers_report',
                'clients_overview',
                'top_clients_report',
                'loan_sizes_report',
                'group_report',
                'group_breakdown',
                'savings_account_report',
                'savings_balance_report',
                'savings_transaction_report',
                'fixed_term_maturity_report',
                'products_summary',
                'individual_indicator_report',
                'loan_officer_performance_report',
                'audit_report',
                'group_indicator_report',
            ])->nullable();
            $table->enum('start_date_type', ['date_picker', 'today', 'yesterday', 'tomorrow'])->nullable();
            $table->date('start_date')->nullable();
            $table->enum('end_date_type', ['date_picker', 'today', 'yesterday', 'tomorrow'])->nullable();
            $table->date('end_date')->nullable();
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
            $table->tinyInteger('active')->default(1);
            $table->enum('status', ['pending', 'approved','declined'])->default('pending');
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
        Schema::dropIfExists('report_scheduler');
    }
}

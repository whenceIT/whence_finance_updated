<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->comment('administration or bank_account');
            $table->string('gl_account_code')->nullable();
            $table->timestamps();
        });

        Schema::create('administration_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->string('reference_number')->nullable();
            $table->text('comments')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('gl_account_code')->nullable();
            $table->date('expense_date');
            $table->unsignedBigInteger('entered_by')->nullable();
            $table->string('bank_charge_type')->nullable()->comment('Monthly Bank Charges, Transaction Fees, Transfer Charges, SMS Charges');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bank_account_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bank_account_id');
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->string('reference_number')->nullable();
            $table->text('comments')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('gl_account_code')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('entered_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_account_expenses');
        Schema::dropIfExists('administration_expenses');
        Schema::dropIfExists('expense_categories');
    }
};
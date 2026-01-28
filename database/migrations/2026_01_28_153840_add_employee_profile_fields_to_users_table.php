<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal details
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->date('date_of_joining')->nullable()->after('date_of_birth');
            $table->string('marital_status')->nullable()->after('date_of_joining');

            // Employment details
            $table->string('company')->nullable()->after('marital_status');
            $table->string('employee_number')->nullable()->unique()->after('company');
            $table->string('department')->nullable()->after('employee_number');
            $table->string('designation')->nullable()->after('department');
            $table->string('branch')->nullable()->after('designation');

            // Salary & banking
            $table->string('salary_currency', 10)->nullable()->after('branch');
            $table->string('salary_mode')->nullable()->after('salary_currency');
            $table->string('bank_name')->nullable()->after('salary_mode');
            $table->string('bank_account_number')->nullable()->after('bank_name');

            // Health info
            $table->text('health_details')->nullable()->after('bank_account_number');
            $table->string('health_insurance_provider')->nullable()->after('health_details');
            $table->string('health_insurance_number')->nullable()->after('health_insurance_provider');

            // External experience
            $table->string('external_company')->nullable()->after('health_insurance_number');
            $table->string('external_designation')->nullable()->after('external_company');
            $table->string('external_contact')->nullable()->after('external_designation');
            $table->decimal('external_total_experience', 5, 2)->nullable()->after('external_contact');

            // Internal movement
            $table->string('internal_branch')->nullable()->after('external_total_experience');
            $table->string('internal_designation')->nullable()->after('internal_branch');
            $table->date('internal_from_date')->nullable()->after('internal_designation');
            $table->date('internal_to_date')->nullable()->after('internal_from_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'date_of_joining',
                'company',
                'employee_number',
                'department',
                'designation',
                'branch',
                'salary_currency',
                'salary_mode',
                'bank_name',
                'bank_account_number',
                'marital_status',
                'health_details',
                'health_insurance_provider',
                'health_insurance_number',
                'external_company',
                'external_designation',
                'external_contact',
                'external_total_experience',
                'internal_branch',
                'internal_designation',
                'internal_from_date',
                'internal_to_date',
            ]);
        });
    }
};

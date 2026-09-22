<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recruitment tracking fields for each vacancy record so the system can
     * calculate how long a vacancy has remained open and show its status.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('vacancies', 'date_arose')) {
                $table->date('date_arose')->nullable()->after('num_of_vacancies');
            }
            if (!Schema::hasColumn('vacancies', 'reason')) {
                $table->string('reason')->nullable()->after('date_arose');
            }
            if (!Schema::hasColumn('vacancies', 'recruitment_status')) {
                $table->string('recruitment_status')->default('Open')->after('reason');
            }
            if (!Schema::hasColumn('vacancies', 'num_of_applicants')) {
                $table->integer('num_of_applicants')->default(0)->after('recruitment_status');
            }
            if (!Schema::hasColumn('vacancies', 'num_of_shortlisted')) {
                $table->integer('num_of_shortlisted')->default(0)->after('num_of_applicants');
            }
            if (!Schema::hasColumn('vacancies', 'interview_status')) {
                $table->string('interview_status')->nullable()->after('num_of_shortlisted');
            }
            if (!Schema::hasColumn('vacancies', 'selected_candidate')) {
                $table->string('selected_candidate')->nullable()->after('interview_status');
            }
            if (!Schema::hasColumn('vacancies', 'offer_status')) {
                $table->string('offer_status')->nullable()->after('selected_candidate');
            }
            if (!Schema::hasColumn('vacancies', 'expected_reporting_date')) {
                $table->date('expected_reporting_date')->nullable()->after('offer_status');
            }
            if (!Schema::hasColumn('vacancies', 'actual_reporting_date')) {
                $table->date('actual_reporting_date')->nullable()->after('expected_reporting_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn([
                'date_arose',
                'reason',
                'recruitment_status',
                'num_of_applicants',
                'num_of_shortlisted',
                'interview_status',
                'selected_candidate',
                'offer_status',
                'expected_reporting_date',
                'actual_reporting_date',
            ]);
        });
    }
};

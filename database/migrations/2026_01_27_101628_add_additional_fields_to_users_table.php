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
            if (!Schema::hasColumn('users', 'salutation')) {
                $table->string('salutation')->nullable();
            }
            if (!Schema::hasColumn('users', 'employment_type')) {
                $table->string('employment_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'mobile_number')) {
                $table->string('mobile_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'personal_email')) {
                $table->string('personal_email')->nullable();
            }
            if (!Schema::hasColumn('users', 'current_address')) {
                $table->text('current_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'relation_to_emergency')) {
                $table->string('relation_to_emergency')->nullable();
            }
            if (!Schema::hasColumn('users', 'reports_to')) {
                $table->unsignedBigInteger('reports_to')->nullable();
            }
            if (!Schema::hasColumn('users', 'confirmation_date')) {
                $table->date('confirmation_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'qualification')) {
                $table->string('qualification')->nullable();
            }
            if (!Schema::hasColumn('users', 'school_university')) {
                $table->string('school_university')->nullable();
            }
            if (!Schema::hasColumn('users', 'level_of_education')) {
                $table->string('level_of_education')->nullable();
            }
            if (!Schema::hasColumn('users', 'year_completed')) {
                $table->year('year_completed')->nullable();
            }
            if (!Schema::hasColumn('users', 'major')) {
                $table->string('major')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'salutation')) {
                $table->dropColumn('salutation');
            }
            if (Schema::hasColumn('users', 'employment_type')) {
                $table->dropColumn('employment_type');
            }
            if (Schema::hasColumn('users', 'mobile_number')) {
                $table->dropColumn('mobile_number');
            }
            if (Schema::hasColumn('users', 'personal_email')) {
                $table->dropColumn('personal_email');
            }
            if (Schema::hasColumn('users', 'current_address')) {
                $table->dropColumn('current_address');
            }
            if (Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->dropColumn('emergency_contact_name');
            }
            if (Schema::hasColumn('users', 'emergency_phone')) {
                $table->dropColumn('emergency_phone');
            }
            if (Schema::hasColumn('users', 'relation_to_emergency')) {
                $table->dropColumn('relation_to_emergency');
            }
            if (Schema::hasColumn('users', 'reports_to')) {
                $table->dropColumn('reports_to');
            }
            if (Schema::hasColumn('users', 'confirmation_date')) {
                $table->dropColumn('confirmation_date');
            }
            if (Schema::hasColumn('users', 'qualification')) {
                $table->dropColumn('qualification');
            }
            if (Schema::hasColumn('users', 'school_university')) {
                $table->dropColumn('school_university');
            }
            if (Schema::hasColumn('users', 'level_of_education')) {
                $table->dropColumn('level_of_education');
            }
            if (Schema::hasColumn('users', 'year_completed')) {
                $table->dropColumn('year_completed');
            }
            if (Schema::hasColumn('users', 'major')) {
                $table->dropColumn('major');
            }
        });
    }
};

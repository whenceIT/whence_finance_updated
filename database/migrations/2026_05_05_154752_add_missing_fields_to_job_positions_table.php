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
        Schema::table('job_positions', function (Blueprint $table) {
            $table->boolean('is_vacant')->default(false)->after('date_added');
            $table->integer('num_of_vacancies')->default(0)->after('is_vacant');
            $table->integer('num_of_active')->default(0)->after('num_of_vacancies');
            $table->unsignedBigInteger('department_id')->nullable()->after('num_of_active');
            $table->date('posted_date')->nullable()->after('department_id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_positions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['is_vacant', 'num_of_vacancies', 'num_of_active', 'department_id', 'posted_date']);
        });
    }
};

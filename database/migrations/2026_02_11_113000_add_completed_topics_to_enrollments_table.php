<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletedTopicsToEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->json('completed_topics')->nullable()->after('progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('completed_topics');
        });
    }
}

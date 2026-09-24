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
            $table->integer('approved')->default(0)->after('num_of_active')
                  ->comment('Approved headcount for this position');
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
            $table->dropColumn('approved');
        });
    }
};

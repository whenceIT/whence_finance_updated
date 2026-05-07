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
            if (!Schema::hasColumn('job_positions', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('job_positions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            $table->softDeletes();
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
            $table->dropSoftDeletes();
            if (Schema::hasColumn('job_positions', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('job_positions', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};

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
        Schema::table('administrative_records', function (Blueprint $table) {
            if (!Schema::hasColumn('administrative_records', 'approved_by')) {
                $table->unsignedInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('users');
            }
            if (!Schema::hasColumn('administrative_records', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
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
        Schema::table('administrative_records', function (Blueprint $table) {
            if (Schema::hasColumn('administrative_records', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('administrative_records', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};

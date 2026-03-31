<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToGeneralUploadPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('general_upload_position')) {
            Schema::table('general_upload_position', function (Blueprint $table) {
                if (!Schema::hasColumn('general_upload_position', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('general_upload_position', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('general_upload_position')) {
            Schema::table('general_upload_position', function (Blueprint $table) {
                $table->dropColumn(['created_at', 'updated_at']);
            });
        }
    }
}
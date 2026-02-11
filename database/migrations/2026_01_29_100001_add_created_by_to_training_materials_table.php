<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByToTrainingMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('target_role');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropColumn('created_by');
        });
    }
}

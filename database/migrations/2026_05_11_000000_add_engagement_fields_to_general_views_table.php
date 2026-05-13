<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEngagementFieldsToGeneralViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_views', function (Blueprint $table) {
            $table->boolean('opened')->default(false)->after('item_id');
            $table->unsignedInteger('duration')->default(0)->after('opened');
            $table->string('completion_status')->nullable()->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_views', function (Blueprint $table) {
            $table->dropColumn(['opened', 'duration', 'completion_status']);
        });
    }
}

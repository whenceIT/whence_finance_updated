<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->renameColumn('unit_share_count', 'shared');
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->renameColumn('shared', 'unit_share_count');
        });
    }
};

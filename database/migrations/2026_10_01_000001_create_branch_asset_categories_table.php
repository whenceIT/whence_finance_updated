<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('icon')->nullable()->default('fa-tag');
            $table->boolean('allows_individual_tracking')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_categories');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetInventoriesTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('office_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('working')->default(0);
            $table->unsignedInteger('damaged')->default(0);
            $table->unsignedInteger('missing')->default(0);
            $table->unsignedInteger('under_repair')->default(0);
            $table->timestamp('last_verified_at')->nullable();
            $table->unsignedInteger('last_verified_by')->nullable();
            $table->timestamps();

            $table->unique(['office_id', 'category_id']);
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('branch_asset_categories')->onDelete('cascade');
            $table->foreign('last_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_inventories');
    }
}

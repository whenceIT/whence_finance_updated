<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetIndividualItemsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_individual_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_id');
            $table->string('serial_number')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->enum('condition', ['Working', 'Damaged', 'Missing'])->default('Working');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('inventory_id')->references('id')->on('branch_asset_inventories')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_individual_items');
    }
}

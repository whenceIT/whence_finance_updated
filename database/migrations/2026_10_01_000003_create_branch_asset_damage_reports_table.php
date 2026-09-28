<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetDamageReportsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_damage_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('office_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('quantity_affected')->default(1);
            $table->date('reported_date');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->enum('status', ['Reported', 'Assessed', 'Sent for Repair', 'Repaired', 'Closed'])->default('Reported');
            $table->unsignedInteger('reported_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('branch_asset_categories')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_damage_reports');
    }
}

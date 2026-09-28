<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetRepairsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_repairs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('damage_report_id');
            $table->date('repair_date')->nullable();
            $table->decimal('repair_cost', 10, 2)->nullable()->default(0);
            $table->string('repair_provider')->nullable();
            $table->text('description')->nullable();
            $table->string('invoice_path')->nullable();
            $table->date('date_returned')->nullable();
            $table->string('condition_after')->nullable()->default('Working');
            $table->timestamps();

            $table->foreign('damage_report_id')->references('id')->on('branch_asset_damage_reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_repairs');
    }
}

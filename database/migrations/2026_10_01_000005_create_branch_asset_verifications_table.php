<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAssetVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_asset_verifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('office_id');
            $table->string('period'); // e.g. "October 2026"
            $table->unsignedInteger('requested_by')->nullable();
            $table->unsignedInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['Pending', 'Submitted', 'Acknowledged'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_asset_verifications');
    }
}

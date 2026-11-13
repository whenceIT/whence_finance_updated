<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeJobApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('creative_job_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nrc_number')->index();
            $table->string('full_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('position_applied_for');
            $table->string('recommending_department')->nullable();
            $table->unsignedBigInteger('recommended_by_user_id')->unsigned()->nullable();
            $table->date('date_recommended')->nullable();
            $table->enum('status', ['Recommended', 'Under Review', 'Approved', 'Rejected', 'Employed', 'Not Employed'])->default('Recommended');
            $table->timestamp('admin_notified_at')->nullable();
            $table->date('employment_date')->nullable();
            $table->boolean('is_currently_employed')->default(false);
            $table->timestamp('qualifying_completed_at')->nullable();
            $table->timestamp('reward_notified_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->unsigned()->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('creative_job_applications');
    }
}

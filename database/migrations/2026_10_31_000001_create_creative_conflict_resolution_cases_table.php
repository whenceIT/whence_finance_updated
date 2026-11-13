<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeConflictResolutionCasesTable extends Migration
{
    public function up()
    {
        Schema::create('creative_conflict_resolution_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('case_reference')->unique();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('client_name');
            $table->string('nrc_number')->nullable()->index();
            $table->string('contact_number')->nullable();
            $table->string('complaint_type');
            $table->text('complaint_description');
            $table->unsignedBigInteger('reported_by_user_id')->nullable();
            $table->date('date_reported');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Escalated'])->default('Open');
            $table->text('resolution_details')->nullable();
            $table->date('resolution_date')->nullable();
            $table->decimal('charge_amount', 10, 2)->default(20.00);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('creative_conflict_resolution_cases');
    }
}

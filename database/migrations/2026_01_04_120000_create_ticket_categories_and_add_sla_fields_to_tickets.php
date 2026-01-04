<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketCategoriesAndAddSlaFieldsToTickets extends Migration
{
    public function up()
    {
        // create categories table
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('priority_default')->nullable();
            $table->integer('sla_days')->nullable();
            $table->timestamps();
        });

        // seed initial categories
        DB::table('ticket_categories')->insert([
            ['name' => 'Disciplinary Case', 'priority_default' => 'High', 'sla_days' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Payroll Query', 'priority_default' => 'High', 'sla_days' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transfer Request', 'priority_default' => 'Medium', 'sla_days' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Policy Clarification', 'priority_default' => 'Medium', 'sla_days' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'General Admin', 'priority_default' => 'Low', 'sla_days' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // add new fields to tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('issue_category_id')->nullable()->after('department');
            $table->dateTime('date_raised')->nullable()->after('datetime_open');
            $table->integer('sla_days')->nullable()->after('priority');
            $table->dateTime('due_date')->nullable()->after('sla_days');
            $table->dateTime('date_closed')->nullable()->after('datetime_close');
            $table->boolean('sla_met')->default(false)->after('date_closed');

            $table->foreign('issue_category_id')->references('id')->on('ticket_categories')->onDelete('set null');
        });

        // backfill dates using existing columns where possible
        DB::statement("UPDATE tickets SET date_raised = datetime_open WHERE date_raised IS NULL");
        DB::statement("UPDATE tickets SET date_closed = datetime_close WHERE date_closed IS NULL");
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['issue_category_id']);
            $table->dropColumn(['issue_category_id', 'date_raised', 'sla_days', 'due_date', 'date_closed', 'sla_met']);
        });

        Schema::dropIfExists('ticket_categories');
    }
}

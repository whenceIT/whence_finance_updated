<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Stores each submitted cash-audit wizard response from a Branch Manager.
 */
class CreateCashAuditSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('cash_audit_submissions', function (Blueprint $table) {
            $table->increments('id');

            // Who submitted
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('office_id');

            // ── Step 1: Cash Balance ──────────────────────────────────
            $table->string('branch_name')->nullable();
            $table->string('district_manager_name')->nullable();

            // Cash denominations (physical cash)
            $table->decimal('cash_5',   15, 2)->default(0);
            $table->decimal('cash_10',  15, 2)->default(0);
            $table->decimal('cash_20',  15, 2)->default(0);
            $table->decimal('cash_50',  15, 2)->default(0);
            $table->decimal('cash_100', 15, 2)->default(0);
            $table->decimal('cash_200', 15, 2)->default(0);
            $table->decimal('cash_500', 15, 2)->default(0);
            $table->decimal('cash_total', 15, 2)->default(0);
            $table->timestamp('cash_count_datetime')->nullable();

            // Petty cash denominations
            $table->decimal('petty_5',   15, 2)->default(0);
            $table->decimal('petty_10',  15, 2)->default(0);
            $table->decimal('petty_20',  15, 2)->default(0);
            $table->decimal('petty_50',  15, 2)->default(0);
            $table->decimal('petty_100', 15, 2)->default(0);
            $table->decimal('petty_200', 15, 2)->default(0);
            $table->decimal('petty_500', 15, 2)->default(0);
            $table->decimal('petty_total', 15, 2)->default(0);
            $table->boolean('petty_via_mobile_wallet')->default(false);

            // ── Step 2: Mobile Money ──────────────────────────────────
            $table->string('mobile_ussd_reference')->nullable()->comment('Transaction reference from USSD balance enquiry');
            $table->string('mobile_number')->nullable()->comment('Airtel/MTN mobile number used');
            $table->string('sim_registered_name')->nullable()->comment('Registered name of SIM card holder');
            $table->string('dm_using_sim')->nullable()->comment('Name of DM using the SIM card');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_audit_submissions');
    }
}

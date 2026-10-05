<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Stores the on/off state and target office for the cash audit wizard.
 * Only one active config row at a time (upserted by the risk manager).
 */
class CreateCashAuditWizardConfigTable extends Migration
{
    public function up()
    {
        Schema::create('cash_audit_wizard_config', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('target_office_id')->nullable()->comment('NULL = all offices with role 4');
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('target_office_id')->references('id')->on('offices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_audit_wizard_config');
    }
}

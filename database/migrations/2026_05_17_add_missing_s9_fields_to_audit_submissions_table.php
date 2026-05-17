<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_submissions', 's9_3')) {
                $table->string('s9_3')->nullable()->after('s9_2_notes');
            }
            if (!Schema::hasColumn('audit_submissions', 's9_3_notes')) {
                $table->text('s9_3_notes')->nullable()->after('s9_3');
            }
            if (!Schema::hasColumn('audit_submissions', 's9_4')) {
                $table->string('s9_4')->nullable()->after('s9_3_notes');
            }
            if (!Schema::hasColumn('audit_submissions', 's9_4_notes')) {
                $table->text('s9_4_notes')->nullable()->after('s9_4');
            }
            if (!Schema::hasColumn('audit_submissions', 's9_5')) {
                $table->string('s9_5')->nullable()->after('s9_4_notes');
            }
            if (!Schema::hasColumn('audit_submissions', 's9_5_notes')) {
                $table->text('s9_5_notes')->nullable()->after('s9_5');
            }
            if (!Schema::hasColumn('audit_submissions', 'key_findings')) {
                $table->text('key_findings')->nullable()->after('s9_notes');
            }
            if (!Schema::hasColumn('audit_submissions', 'immediate_actions')) {
                $table->text('immediate_actions')->nullable()->after('key_findings');
            }
            if (!Schema::hasColumn('audit_submissions', 'recommendations')) {
                $table->text('recommendations')->nullable()->after('immediate_actions');
            }
            if (!Schema::hasColumn('audit_submissions', 'followup_date')) {
                $table->date('followup_date')->nullable()->after('recommendations');
            }
            if (!Schema::hasColumn('audit_submissions', 'escalation_required')) {
                $table->string('escalation_required')->nullable()->after('followup_date');
            }
            if (!Schema::hasColumn('audit_submissions', 'auditor_signature')) {
                $table->string('auditor_signature')->nullable()->after('escalation_required');
            }
            if (!Schema::hasColumn('audit_submissions', 'signoff_datetime')) {
                $table->datetime('signoff_datetime')->nullable()->after('auditor_signature');
            }
            if (!Schema::hasColumn('audit_submissions', 'manager_acknowledgement')) {
                $table->string('manager_acknowledgement')->nullable()->after('signoff_datetime');
            }
            if (!Schema::hasColumn('audit_submissions', 'manager_comments')) {
                $table->text('manager_comments')->nullable()->after('manager_acknowledgement');
            }
        });
    }

    public function down()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            $table->dropColumn([
                's9_3', 's9_3_notes', 's9_4', 's9_4_notes', 's9_5', 's9_5_notes',
                'key_findings', 'immediate_actions', 'recommendations', 'followup_date',
                'escalation_required', 'auditor_signature', 'signoff_datetime',
                'manager_acknowledgement', 'manager_comments',
            ]);
        });
    }
};

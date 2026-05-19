<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            // Add missing Section 4 (Collections) items 3-6 only if they don't exist
            if (!Schema::hasColumn('audit_submissions', 's4_3')) {
                $table->string("s4_3")->nullable()->after("s4_2_notes");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_3_notes')) {
                $table->text("s4_3_notes")->nullable()->after("s4_3");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_4')) {
                $table->string("s4_4")->nullable()->after("s4_3_notes");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_4_notes')) {
                $table->text("s4_4_notes")->nullable()->after("s4_4");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_5')) {
                $table->string("s4_5")->nullable()->after("s4_4_notes");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_5_notes')) {
                $table->text("s4_5_notes")->nullable()->after("s4_5");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_6')) {
                $table->string("s4_6")->nullable()->after("s4_5_notes");
            }
            if (!Schema::hasColumn('audit_submissions', 's4_6_notes')) {
                $table->text("s4_6_notes")->nullable()->after("s4_6");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_submissions', function (Blueprint $table) {
            for ($i = 3; $i <= 6; $i++) {
                $table->dropColumn("s4_{$i}");
                $table->dropColumn("s4_{$i}_notes");
            }
        });
    }
};

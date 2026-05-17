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
            // Remove s5_7 (Section 5 only has 6 items, not 7)
            if (Schema::hasColumn('audit_submissions', 's5_7')) {
                $table->dropColumn(['s5_7', 's5_7_notes']);
            }
            
            // Remove s6_8 (Section 6 only has 7 items, not 8)
            if (Schema::hasColumn('audit_submissions', 's6_8')) {
                $table->dropColumn(['s6_8', 's6_8_notes']);
            }
            
            // Remove s7_7 and s7_8 (Section 7 only has 6 items, not 8)
            if (Schema::hasColumn('audit_submissions', 's7_7')) {
                $table->dropColumn(['s7_7', 's7_7_notes']);
            }
            if (Schema::hasColumn('audit_submissions', 's7_8')) {
                $table->dropColumn(['s7_8', 's7_8_notes']);
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
            // Re-add the columns if rolling back
            $table->string('s5_7')->nullable();
            $table->text('s5_7_notes')->nullable();
            $table->string('s6_8')->nullable();
            $table->text('s6_8_notes')->nullable();
            $table->string('s7_7')->nullable();
            $table->text('s7_7_notes')->nullable();
            $table->string('s7_8')->nullable();
            $table->text('s7_8_notes')->nullable();
        });
    }
};

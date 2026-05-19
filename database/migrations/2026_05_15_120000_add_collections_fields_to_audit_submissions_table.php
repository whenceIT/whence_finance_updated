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
            $table->string('s4_3')->nullable()->after('s4_2');
            $table->text('s4_3_notes')->nullable()->after('s4_3');
            $table->string('s4_4')->nullable()->after('s4_3_notes');
            $table->text('s4_4_notes')->nullable()->after('s4_4');
            $table->string('s4_5')->nullable()->after('s4_4_notes');
            $table->text('s4_5_notes')->nullable()->after('s4_5');
            $table->string('s4_6')->nullable()->after('s4_5_notes');
            $table->text('s4_6_notes')->nullable()->after('s4_6');
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
            $table->dropColumn([
                's4_3',
                's4_3_notes',
                's4_4',
                's4_4_notes',
                's4_5',
                's4_5_notes',
                's4_6',
                's4_6_notes',
            ]);
        });
    }
};

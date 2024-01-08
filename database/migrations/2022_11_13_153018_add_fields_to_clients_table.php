<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('working_place')->nullable();
            $table->string('working_position')->nullable();
            $table->string('salary')->nullable();
            $table->string('nrc_number')->nullable();
            $table->tinyInteger('blacklisted')->default(0);
            $table->date('date_blacklisted')->nullable();
            $table->string('key_contact_person')->nullable();
            $table->string('key_contact_person_nrc_number')->nullable();
            $table->string('number_of_shareholders')->nullable();
            $table->string('company_registration_date')->nullable();
            $table->string('type_of_business')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'working_place',
                'working_position',
                'salary',
                'nrc_number',
                'blacklisted',
                'date_blacklisted',
                'key_contact_person',
                'key_contact_person_nrc_number',
                'number_of_shareholders',
                'company_registration_date',
                'type_of_business',
            ]);
        });
    }
};

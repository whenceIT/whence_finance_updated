<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE loans MODIFY disbursement_date DATETIME NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE loans MODIFY disbursement_date DATE NULL');
    }
};

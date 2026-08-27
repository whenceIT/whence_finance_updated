<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('loans', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('loans', 'branch_name')) {
                $table->string('branch_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('loans', 'branch_code')) {
                $table->string('branch_code')->nullable()->after('branch_name');
            }
            if (!Schema::hasColumn('loans', 'sort_code')) {
                $table->string('sort_code')->nullable()->after('branch_code');
            }
            if (!Schema::hasColumn('loans', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('year');
            }
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = ['phone_number', 'sort_code', 'branch_code', 'branch_name', 'bank_name', 'bank_account_number'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('loans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

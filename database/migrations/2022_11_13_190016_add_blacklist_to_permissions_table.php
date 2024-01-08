<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            [
                'parent_id' => '36',
                'name' => 'View Blacklisted Clients',
                'slug' => 'clients.blacklisted',
            ],
            [
                'parent_id' => '36',
                'name' => 'View Client Blacklist',
                'slug' => 'clients.blacklist.view',
            ],
            [
                'parent_id' => '36',
                'name' => 'Add Client To Blacklist',
                'slug' => 'clients.blacklist.create',
            ],
            [
                'parent_id' => '36',
                'name' => 'Update Client Blacklist',
                'slug' => 'clients.blacklist.update',
            ],
            [
                'parent_id' => '36',
                'name' => 'Remove Client From Blacklist',
                'slug' => 'clients.blacklist.delete',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};

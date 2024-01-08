<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gl_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('gl_code')->nullable();
            $table->enum('account_type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('manual_entries')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gl_accounts');
    }
}

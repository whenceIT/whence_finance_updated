<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('unit_shares')) {
            Schema::create('unit_shares', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unit')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->unsignedBigInteger('loan_id')->nullable();
                $table->unsignedBigInteger('loan_txn_id')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('loan_id');
                $table->index('loan_txn_id');
                $table->index('unit');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_shares');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCollateralTableSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collateral', function (Blueprint $table) {
            if (!Schema::hasColumn('collateral', 'initial_price')) {
                $table->decimal('initial_price', 65, 4)->nullable()->after('serial');
            }
            if (!Schema::hasColumn('collateral', 'current_worth')) {
                $table->decimal('current_worth', 65, 4)->nullable()->after('initial_price');
            }
            if (!Schema::hasColumn('collateral', 'status')) {
                $table->string('status')->nullable()->after('current_worth');
            }
            if (!Schema::hasColumn('collateral', 'condition')) {
                $table->string('condition')->nullable()->after('status');
            }
            if (!Schema::hasColumn('collateral', 'date_purchased')) {
                $table->date('date_purchased')->nullable()->after('condition');
            }
            if (!Schema::hasColumn('collateral', 'date_resold')) {
                $table->date('date_resold')->nullable()->after('date_purchased');
            }
            if (!Schema::hasColumn('collateral', 'created_by_id')) {
                $table->integer('created_by_id')->nullable()->after('date_resold');
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
        Schema::table('collateral', function (Blueprint $table) {
            $table->dropColumn([
                'initial_price',
                'current_worth',
                'status',
                'condition',
                'date_purchased',
                'date_resold',
                'created_by_id'
            ]);
        });
    }
}

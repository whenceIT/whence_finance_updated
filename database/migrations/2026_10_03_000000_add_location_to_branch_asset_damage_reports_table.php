<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToBranchAssetDamageReportsTable extends Migration
{
    public function up()
    {
        // Guarded so the migration is safe to re-run after a partial failure.
        if (! Schema::hasColumn('branch_asset_damage_reports', 'location_id')) {
            Schema::table('branch_asset_damage_reports', function (Blueprint $table) {
                // Location supersedes office_id: inventories are now keyed by location
                $table->unsignedInteger('location_id')->nullable()->after('office_id');
                $table->foreign('location_id')->references('id')->on('asset_locations')->onDelete('set null');
            });
        }

        if (! Schema::hasColumn('branch_asset_damage_reports', 'inventory_id')) {
            Schema::table('branch_asset_damage_reports', function (Blueprint $table) {
                // The exact asset-register row the damage applies to.
                // unsignedInteger (not bigint) to match branch_asset_inventories.id.
                $table->unsignedInteger('inventory_id')->nullable()->after('location_id');
                $table->foreign('inventory_id')->references('id')->on('branch_asset_inventories')->onDelete('set null');
            });
        }

        // office_id becomes optional so a report can be filed against a location alone
        Schema::table('branch_asset_damage_reports', function (Blueprint $table) {
            $table->unsignedInteger('office_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('branch_asset_damage_reports', function (Blueprint $table) {
            if (Schema::hasColumn('branch_asset_damage_reports', 'inventory_id')) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            }
            if (Schema::hasColumn('branch_asset_damage_reports', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }
        });

        Schema::table('branch_asset_damage_reports', function (Blueprint $table) {
            $table->unsignedInteger('office_id')->nullable(false)->change();
        });
    }
}
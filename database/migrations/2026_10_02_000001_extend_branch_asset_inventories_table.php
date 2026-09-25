<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendBranchAssetInventoriesTable extends Migration
{
    public function up()
    {
        // 1. Drop the foreign-key + unique constraints that depend on office_id
        //    so we can alter the column to nullable.
        Schema::table('branch_asset_inventories', function (Blueprint $table) {
            // Drop FK and unique index (names follow Laravel convention)
            $table->dropForeign(['office_id']);
            $table->dropUnique(['office_id', 'category_id']);
        });

        // 2. Make office_id nullable and re-add FK
        Schema::table('branch_asset_inventories', function (Blueprint $table) {
            $table->unsignedInteger('office_id')->nullable()->change();
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('set null');
        });

        // 3. Add all the new columns from assets.md
        Schema::table('branch_asset_inventories', function (Blueprint $table) {
            // Unique WFS-xxxx asset identifier from the register
            $table->string('asset_id', 20)->nullable()->unique()->after('id');

            // Location: FK to asset_locations (separate from office branch link)
            $table->unsignedInteger('location_id')->nullable()->after('office_id');

            // Item detail columns
            $table->string('item_description')->nullable()->after('category_id');
            $table->string('serial_number')->nullable()->after('item_description');
            $table->decimal('unit_cost', 12, 2)->nullable()->after('serial_number');
            $table->decimal('total_value', 14, 2)->nullable()->after('unit_cost');

            // Free-text condition field (keeps the original text, e.g. "Needs Repair", "Mint")
            $table->string('condition_text')->nullable()->after('under_repair');

            // Allocation and notes
            $table->string('allocated_to')->nullable()->after('condition_text');
            $table->text('remarks')->nullable()->after('allocated_to');
            $table->text('action_required')->nullable()->after('remarks');

            // Financial metadata
            $table->string('valuation_basis')->nullable()->after('action_required');
            $table->string('source_ref')->nullable()->after('valuation_basis');

            $table->foreign('location_id')->references('id')->on('asset_locations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('branch_asset_inventories', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'asset_id', 'location_id', 'item_description', 'serial_number',
                'unit_cost', 'total_value', 'condition_text', 'allocated_to',
                'remarks', 'action_required', 'valuation_basis', 'source_ref',
            ]);
        });

        // Restore office_id to non-nullable + unique constraint
        Schema::table('branch_asset_inventories', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->unsignedInteger('office_id')->nullable(false)->change();
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->unique(['office_id', 'category_id']);
        });
    }
}

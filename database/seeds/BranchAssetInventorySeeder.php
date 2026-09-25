<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Self-contained seeder — all 585 WFS records are embedded as a static array.
 * assets.md is no longer required and can be deleted.
 *
 * Columns per row (index):
 *   0  asset_id        1  location (name)    2  category (name)
 *   3  item_description  4  total (qty)      5  serial_number
 *   6  unit_cost         7  total_value       8  condition_text
 *   9  allocated_to     10  remarks          11  action_required
 *  12  valuation_basis  13  source_ref
 */
class BranchAssetInventorySeeder extends Seeder
{
    public function run(): void
    {
        // Pre-load FK lookup maps (lowercase name → id)
        $locs = [];
        foreach (DB::table('asset_locations')->get() as $r) {
            $locs[strtolower(trim($r->name))] = $r->id;
        }

        $cats = [];
        foreach (DB::table('branch_asset_categories')->get() as $r) {
            $cats[strtolower(trim($r->name))] = $r->id;
        }

        $now = now();
        $inserted = 0;

        foreach ($this->records() as $row) {
            [$assetId, $locName, $catName, $description, $qty,
             $serial, $unitCost, $totalValue, $condition,
             $allocatedTo, $remarks, $actionReq, $valBasis, $sourceRef] = $row;

            $locationId = $locs[strtolower($locName ?? '')] ?? null;
            $categoryId = $cats[strtolower($catName ?? '')] ?? null;

            DB::table('branch_asset_inventories')->updateOrInsert(
                ['asset_id' => $assetId],
                [
                    'location_id'      => $locationId,
                    'category_id'      => $categoryId,
                    'item_description' => $description,
                    'total'            => (int) ($qty ?? 0),
                    'working'          => (int) ($qty ?? 0),
                    'damaged'          => 0,
                    'missing'          => 0,
                    'under_repair'     => 0,
                    'serial_number'    => $serial,
                    'unit_cost'        => $unitCost,
                    'total_value'      => $totalValue,
                    'condition_text'   => $condition,
                    'allocated_to'     => $allocatedTo,
                    'remarks'          => $remarks,
                    'action_required'  => $actionReq,
                    'valuation_basis'  => $valBasis,
                    'source_ref'       => $sourceRef,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );
            $inserted++;
        }

        $this->command->info("BranchAssetInventorySeeder: {$inserted} records upserted.");
    }

    private function records(): array
    {
        return [

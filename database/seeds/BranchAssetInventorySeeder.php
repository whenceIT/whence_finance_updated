<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Parses assets.md at runtime and upserts every WFS-xxxx row into
 * branch_asset_inventories.  Safe to re-run (updateOrInsert on asset_id).
 *
 * assets.md columns (tab-separated, first row is header):
 *   0  Asset ID
 *   1  Location
 *   2  Item Category
 *   3  Item Description
 *   4  Quantity
 *   5  Asset / Serial No.
 *   6  Unit Cost (K)
 *   7  Total Value (K)
 *   8  Condition
 *   9  Allocated To
 *  10  Remarks
 *  11  Action Required
 *  12  Valuation Basis
 *  13  Source Row
 */
class BranchAssetInventorySeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('assets.md');

        if (!file_exists($file)) {
            $this->command->error('assets.md not found at ' . $file);
            return;
        }

        // Pre-load FK lookup maps (lowercase name → id)
        $locs = [];
        foreach (DB::table('asset_locations')->get() as $r) {
            $locs[strtolower(trim($r->name))] = $r->id;
        }

        $cats = [];
        foreach (DB::table('branch_asset_categories')->get() as $r) {
            $cats[strtolower(trim($r->name))] = $r->id;
        }

        $now      = now();
        $inserted = 0;
        $skipped  = 0;
        $handle   = fopen($file, 'r');

        while (($line = fgets($handle)) !== false) {
            $line = rtrim($line, "\r\n");

            // Skip header and blank lines
            if ($line === '' || str_starts_with($line, 'Asset ID')) {
                continue;
            }

            $cols = explode("\t", $line);

            // Must have at least 8 columns and start with WFS-
            if (count($cols) < 8 || !str_starts_with(trim($cols[0]), 'WFS-')) {
                $skipped++;
                continue;
            }

            $assetId     = trim($cols[0]);
            $locationRaw = trim($cols[1] ?? '');
            $categoryRaw = trim($cols[2] ?? '');
            $description = trim($cols[3] ?? '');
            $qty         = (int) preg_replace('/\D/', '', $cols[4] ?? '0');
            $serial      = $this->nullify($cols[5] ?? '');
            $unitCost    = $this->money($cols[6] ?? '');
            $totalValue  = $this->money($cols[7] ?? '');
            $condition   = trim($cols[8] ?? '');
            $allocatedTo = trim($cols[9] ?? '');
            $remarks     = trim($cols[10] ?? '');
            $actionReq   = trim($cols[11] ?? '');
            $valBasis    = trim($cols[12] ?? '');
            $sourceRef   = trim($cols[13] ?? '');

            // Resolve FKs
            $locationId = $locs[strtolower($locationRaw)] ?? null;
            $categoryId = $cats[strtolower($categoryRaw)] ?? null;

            // If category not found by exact name try first-word match
            if (!$categoryId && $categoryRaw) {
                foreach ($cats as $catName => $catId) {
                    if (str_starts_with($catName, strtolower(explode(' ', $categoryRaw)[0]))) {
                        $categoryId = $catId;
                        break;
                    }
                }
            }

            DB::table('branch_asset_inventories')->updateOrInsert(
                ['asset_id' => $assetId],
                [
                    'location_id'      => $locationId,
                    'category_id'      => $categoryId,
                    'item_description' => $description ?: null,
                    'total'            => $qty,
                    'working'          => $qty,   // seed as all working; damage reports adjust
                    'damaged'          => 0,
                    'missing'          => 0,
                    'under_repair'     => 0,
                    'serial_number'    => $serial,
                    'unit_cost'        => $unitCost,
                    'total_value'      => $totalValue,
                    'condition_text'   => $condition ?: null,
                    'allocated_to'     => $allocatedTo ?: null,
                    'remarks'          => $remarks ?: null,
                    'action_required'  => $actionReq ?: null,
                    'valuation_basis'  => $valBasis ?: null,
                    'source_ref'       => $sourceRef ?: null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );

            $inserted++;
        }

        fclose($handle);

        $this->command->info("BranchAssetInventorySeeder: {$inserted} records upserted, {$skipped} lines skipped.");
    }

    /** Strip K, commas, spaces → float or null */
    private function money(?string $val): ?float
    {
        if ($val === null) return null;
        $clean = preg_replace('/[K,\s]/u', '', $val);
        $clean = trim($clean);
        if ($clean === '' || $clean === 'N/A') return null;
        return is_numeric($clean) ? (float) $clean : null;
    }

    /** Return null for blank / nil / N/A values */
    private function nullify(?string $val): ?string
    {
        if ($val === null) return null;
        $v = trim($val);
        if (in_array(strtolower($v), ['', 'nil', 'n/a', '0', '-'], true)) return null;
        return $v;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AssetLocation;
use App\Models\BranchAssetCategory;
use App\Models\BranchAssetDamageReport;
use App\Models\BranchAssetIndividualItem;
use App\Models\BranchAssetInventory;
use App\Models\BranchAssetRepair;
use App\Models\BranchAssetVerification;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BranchAssetController extends Controller
{
    // =========================================================================
    //  DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        // Company-wide totals from inventory
        $totals = BranchAssetInventory::selectRaw('
            SUM(total)        as total_items,
            SUM(working)      as total_working,
            SUM(damaged)      as total_damaged,
            SUM(under_repair) as total_under_repair,
            SUM(missing)      as total_missing
        ')->first();

        $totalItems      = (int) ($totals->total_items ?? 0);
        $totalWorking    = (int) ($totals->total_working ?? 0);
        $totalDamaged    = (int) ($totals->total_damaged ?? 0);
        $totalUnderRepair= (int) ($totals->total_under_repair ?? 0);
        $totalMissing    = (int) ($totals->total_missing ?? 0);
        $overallCondition = $totalItems > 0 ? round(($totalWorking / $totalItems) * 100, 1) : 0;

        // Most damaged categories (top 5)
        $mostDamagedCategories = BranchAssetInventory::with('category')
            ->select('category_id', DB::raw('SUM(damaged) as total_damaged'), DB::raw('COUNT(DISTINCT office_id) as branches_affected'))
            ->groupBy('category_id')
            ->having('total_damaged', '>', 0)
            ->orderByDesc('total_damaged')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $row->name = optional($row->category)->name;
                return $row;
            });

        // Branches with most open damage reports (top 5)
        $branchesWithMostDamage = BranchAssetDamageReport::join('offices', 'offices.id', '=', 'branch_asset_damage_reports.office_id')
            ->select('branch_asset_damage_reports.office_id', 'offices.name as office_name', DB::raw('COUNT(*) as open_reports'))
            ->whereNotIn('status', ['Repaired', 'Closed'])
            ->groupBy('branch_asset_damage_reports.office_id', 'offices.name')
            ->orderByDesc('open_reports')
            ->limit(5)
            ->get();

        // Repair costs
        $totalRepairCost    = (float) BranchAssetRepair::sum('repair_cost');
        $thisMonthRepairCost = (float) BranchAssetRepair::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()
        ])->sum('repair_cost');

        // Repair cost by branch (top 5)
        $repairCostByBranch = BranchAssetRepair::join('branch_asset_damage_reports', 'branch_asset_damage_reports.id', '=', 'branch_asset_repairs.damage_report_id')
            ->join('offices', 'offices.id', '=', 'branch_asset_damage_reports.office_id')
            ->select('offices.name as office_name', DB::raw('SUM(branch_asset_repairs.repair_cost) as cost'))
            ->groupBy('offices.name')
            ->orderByDesc('cost')
            ->limit(5)
            ->get();

        // Pending verifications
        $pendingVerifications = BranchAssetVerification::with('office', 'requester')
            ->where('status', 'Pending')
            ->orderByDesc('created_at')
            ->get();

        // Attention Required alerts
        $attentionItems = collect();

        // 🔴 Branches with 6+ damaged assets
        $highDamageBranches = BranchAssetInventory::join('offices', 'offices.id', '=', 'branch_asset_inventories.office_id')
            ->select('branch_asset_inventories.office_id', 'offices.name as office_name', DB::raw('SUM(damaged) as total_damaged'))
            ->groupBy('branch_asset_inventories.office_id', 'offices.name')
            ->having('total_damaged', '>=', 6)
            ->get();
        foreach ($highDamageBranches as $b) {
            $attentionItems->push([
                'icon' => '🔴', 'color' => '#e74c3c',
                'branch' => $b->office_name,
                'message' => $b->total_damaged . ' damaged assets awaiting action',
                'link' => route('goa.asset-manager.damage-reports', ['office_id' => $b->office_id]),
            ]);
        }

        // 🔴 Branches with missing assets
        $missingBranches = BranchAssetInventory::join('offices', 'offices.id', '=', 'branch_asset_inventories.office_id')
            ->select('branch_asset_inventories.office_id', 'offices.name as office_name', DB::raw('SUM(missing) as total_missing'))
            ->groupBy('branch_asset_inventories.office_id', 'offices.name')
            ->having('total_missing', '>', 0)
            ->get();
        foreach ($missingBranches as $b) {
            $attentionItems->push([
                'icon' => '🔴', 'color' => '#e74c3c',
                'branch' => $b->office_name,
                'message' => $b->total_missing . ' asset(s) reported missing',
                'link' => route('goa.asset-manager.inventory', ['office_id' => $b->office_id]),
            ]);
        }

        // 🟠 Branches with assets under repair
        $repairBranches = BranchAssetInventory::join('offices', 'offices.id', '=', 'branch_asset_inventories.office_id')
            ->select('branch_asset_inventories.office_id', 'offices.name as office_name', DB::raw('SUM(under_repair) as total_under_repair'))
            ->groupBy('branch_asset_inventories.office_id', 'offices.name')
            ->having('total_under_repair', '>', 0)
            ->get();
        foreach ($repairBranches as $b) {
            $attentionItems->push([
                'icon' => '🟠', 'color' => '#f39c12',
                'branch' => $b->office_name,
                'message' => $b->total_under_repair . ' asset(s) currently under repair',
                'link' => route('goa.asset-manager.repairs'),
            ]);
        }

        // 🟡 Branches with pending verifications
        foreach ($pendingVerifications->take(5) as $v) {
            $attentionItems->push([
                'icon' => '🟡', 'color' => '#f1c40f',
                'branch' => optional($v->office)->name,
                'message' => 'Asset verification pending for ' . $v->period,
                'link' => route('goa.asset-manager.verification'),
            ]);
        }

        return view('goa.asset-manager.dashboard', compact(
            'totalItems', 'totalWorking', 'totalDamaged', 'totalUnderRepair', 'totalMissing',
            'overallCondition', 'mostDamagedCategories', 'branchesWithMostDamage',
            'totalRepairCost', 'thisMonthRepairCost', 'repairCostByBranch',
            'pendingVerifications', 'attentionItems'
        ));
    }

    // =========================================================================
    //  INVENTORY
    // =========================================================================

    public function inventory(Request $request)
    {
        // ── Filter inputs ────────────────────────────────────────────────────
        $locationId  = $request->get('location_id');
        $categoryId  = $request->get('category_id');
        $condition   = $request->get('condition');
        $search      = $request->get('q');
        $activeTab   = $request->get('tab', 'register');

        // ── Main register query ──────────────────────────────────────────────
        $query = BranchAssetInventory::with('location', 'category')
            ->orderBy('asset_id');

        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($condition) {
            $query->whereRaw('LOWER(condition_text) LIKE ?', ['%' . strtolower($condition) . '%']);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_id', 'like', "%{$search}%")
                  ->orWhere('item_description', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('allocated_to', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(50)->withQueryString();

        // ── Filter dropdown data ─────────────────────────────────────────────
        $locations  = AssetLocation::orderBy('name')->get();
        $categories = BranchAssetCategory::orderBy('name')->get();

        // ── Location-grouped aggregates (for reporting tab) ──────────────────
        $locationSummary = BranchAssetInventory::join('asset_locations', 'asset_locations.id', '=', 'branch_asset_inventories.location_id')
            ->select(
                'asset_locations.id as location_id',
                'asset_locations.name as location_name',
                'asset_locations.type as location_type',
                DB::raw('COUNT(*) as item_lines'),
                DB::raw('SUM(branch_asset_inventories.total) as total_qty'),
                DB::raw('SUM(branch_asset_inventories.total_value) as total_value'),
                DB::raw('SUM(branch_asset_inventories.working) as total_working'),
                DB::raw('SUM(branch_asset_inventories.damaged) as total_damaged'),
                DB::raw('SUM(branch_asset_inventories.missing) as total_missing'),
                DB::raw('SUM(branch_asset_inventories.under_repair) as total_under_repair')
            )
            ->groupBy('asset_locations.id', 'asset_locations.name', 'asset_locations.type')
            ->orderBy('asset_locations.name')
            ->get();

        // ── Category-grouped aggregates ──────────────────────────────────────
        $categorySummary = BranchAssetInventory::join('branch_asset_categories', 'branch_asset_categories.id', '=', 'branch_asset_inventories.category_id')
            ->select(
                'branch_asset_categories.name as category_name',
                DB::raw('COUNT(*) as item_lines'),
                DB::raw('SUM(branch_asset_inventories.total) as total_qty'),
                DB::raw('SUM(branch_asset_inventories.total_value) as total_value')
            )
            ->groupBy('branch_asset_categories.name')
            ->orderByDesc('total_value')
            ->get();

        // ── Grand totals ─────────────────────────────────────────────────────
        $grandTotals = BranchAssetInventory::selectRaw('
            COUNT(*) as total_lines,
            SUM(total) as total_qty,
            SUM(total_value) as total_value,
            SUM(working) as total_working,
            SUM(damaged) as total_damaged,
            SUM(missing) as total_missing,
            SUM(under_repair) as total_under_repair
        ')->first();

        // ── Categories tab ────────────────────────────────────────────────────
        $categoriesWithCount = BranchAssetCategory::withCount('inventories as inventory_count')
            ->orderBy('name')->get();

        return view('goa.asset-manager.inventory', compact(
            'items', 'locations', 'categories', 'categoriesWithCount',
            'locationSummary', 'categorySummary', 'grandTotals',
            'locationId', 'categoryId', 'condition', 'search', 'activeTab'
        ));
    }

    public function storeInventory(Request $request)
    {
        $data = $request->validate([
            'asset_id'         => 'nullable|string|max:20|unique:branch_asset_inventories,asset_id',
            'location_id'      => 'nullable|integer|exists:asset_locations,id',
            'category_id'      => 'required|integer|exists:branch_asset_categories,id',
            'item_description' => 'required|string|max:255',
            'total'            => 'nullable|integer|min:0',
            'serial_number'    => 'nullable|string|max:255',
            'unit_cost'        => 'nullable|numeric|min:0',
            'total_value'      => 'nullable|numeric|min:0',
            'condition_text'   => 'nullable|string|max:255',
            'allocated_to'     => 'nullable|string|max:255',
            'remarks'          => 'nullable|string',
            'action_required'  => 'nullable|string',
            'valuation_basis'  => 'nullable|string|max:255',
        ]);

        $qty = (int) ($data['total'] ?? 0);
        BranchAssetInventory::create(array_merge($data, [
            'total'       => $qty,
            'working'     => $qty,
            'damaged'     => 0,
            'missing'     => 0,
            'under_repair'=> 0,
        ]));

        return redirect()->route('goa.asset-manager.inventory')
            ->with('success', 'Asset record added successfully.');
    }

    public function updateInventory(Request $request, $id)
    {
        $inv = BranchAssetInventory::findOrFail($id);

        $data = $request->validate([
            'asset_id'         => 'nullable|string|max:20|unique:branch_asset_inventories,asset_id,' . $id,
            'location_id'      => 'nullable|integer|exists:asset_locations,id',
            'category_id'      => 'required|integer|exists:branch_asset_categories,id',
            'item_description' => 'required|string|max:255',
            'total'            => 'nullable|integer|min:0',
            'serial_number'    => 'nullable|string|max:255',
            'unit_cost'        => 'nullable|numeric|min:0',
            'total_value'      => 'nullable|numeric|min:0',
            'condition_text'   => 'nullable|string|max:255',
            'allocated_to'     => 'nullable|string|max:255',
            'remarks'          => 'nullable|string',
            'action_required'  => 'nullable|string',
            'valuation_basis'  => 'nullable|string|max:255',
        ]);

        $inv->update($data);

        return redirect()->route('goa.asset-manager.inventory')
            ->with('success', 'Asset record updated.');
    }

    protected function validateInventoryTotals(array $data)
    {
        $sum = $data['working'] + $data['damaged'] + $data['missing'] + $data['under_repair'];
        if ($data['total'] > 0 && $sum !== (int) $data['total']) {
            abort(422, 'Working + Damaged + Missing + Under Repair must equal Total (' . $data['total'] . '). Got ' . $sum . '.');
        }
    }

    // =========================================================================
    //  ASSET CATEGORIES
    // =========================================================================

    public function categories()
    {
        return redirect()->route('goa.asset-manager.inventory', ['tab' => 'categories']);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:branch_asset_categories,name',
            'icon' => 'nullable|string|max:50',
            'allows_individual_tracking' => 'nullable|boolean',
        ]);

        BranchAssetCategory::create([
            'name'                     => $data['name'],
            'icon'                     => $data['icon'] ?? 'fa-tag',
            'allows_individual_tracking' => !empty($data['allows_individual_tracking']),
            'active'                   => true,
        ]);

        return redirect()->route('goa.asset-manager.inventory', ['tab' => 'categories'])
            ->with('success', 'Category "' . $data['name'] . '" created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = BranchAssetCategory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:branch_asset_categories,name,' . $id,
            'icon' => 'nullable|string|max:50',
            'allows_individual_tracking' => 'nullable|boolean',
        ]);

        $cat->update([
            'name'                     => $data['name'],
            'icon'                     => $data['icon'] ?? 'fa-tag',
            'allows_individual_tracking' => !empty($data['allows_individual_tracking']),
        ]);

        return redirect()->route('goa.asset-manager.inventory', ['tab' => 'categories'])
            ->with('success', 'Category updated.');
    }

    public function toggleCategory($id)
    {
        $cat = BranchAssetCategory::findOrFail($id);
        $cat->active = !$cat->active;
        $cat->save();

        $state = $cat->active ? 'activated' : 'deactivated';
        return redirect()->route('goa.asset-manager.inventory', ['tab' => 'categories'])
            ->with('success', 'Category "' . $cat->name . '" ' . $state . '.');
    }

    // =========================================================================
    //  INDIVIDUAL ITEMS
    // =========================================================================

    public function storeIndividualItem(Request $request, $inventoryId)
    {
        $inv = BranchAssetInventory::findOrFail($inventoryId);

        $data = $request->validate([
            'serial_number' => 'nullable|string|max:100',
            'assigned_to'   => 'nullable|integer|exists:users,id',
            'condition'     => 'required|in:Working,Damaged,Missing',
            'notes'         => 'nullable|string|max:500',
        ]);

        BranchAssetIndividualItem::create(array_merge($data, ['inventory_id' => $inv->id]));

        return redirect()->route('goa.asset-manager.inventory', ['office_id' => $inv->office_id])
            ->with('success', 'Individual item added.');
    }

    public function destroyIndividualItem($id)
    {
        $item = BranchAssetIndividualItem::findOrFail($id);
        $officeId = optional($item->inventory)->office_id;
        $item->delete();

        return redirect()->route('goa.asset-manager.inventory', ['office_id' => $officeId])
            ->with('success', 'Item removed.');
    }

    // =========================================================================
    //  DAMAGE REPORTS
    // =========================================================================

    public function damageReports(Request $request)
    {
        $query = BranchAssetDamageReport::with('office', 'category', 'reporter');

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderByDesc('reported_date')->paginate(20);

        $offices    = Office::where('active', 1)->orderBy('name')->get();
        $categories = BranchAssetCategory::where('active', true)->orderBy('name')->get();

        $openCount         = BranchAssetDamageReport::whereNotIn('status', ['Repaired', 'Closed'])->count();
        $sentForRepairCount= BranchAssetDamageReport::where('status', 'Sent for Repair')->count();
        $closedCount       = BranchAssetDamageReport::whereIn('status', ['Repaired', 'Closed'])->count();

        return view('goa.asset-manager.damage-reports', compact(
            'reports', 'offices', 'categories', 'openCount', 'sentForRepairCount', 'closedCount'
        ));
    }

    public function storeDamageReport(Request $request)
    {
        $data = $request->validate([
            'office_id'        => 'required|integer|exists:offices,id',
            'category_id'      => 'required|integer|exists:branch_asset_categories,id',
            'quantity_affected'=> 'required|integer|min:1',
            'reported_date'    => 'required|date',
            'description'      => 'required|string',
            'photo'            => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('asset-damage', 'public');
        }

        DB::transaction(function () use ($data, $photoPath) {
            // Ensure inventory record exists
            $inv = BranchAssetInventory::firstOrCreate(
                ['office_id' => $data['office_id'], 'category_id' => $data['category_id']],
                ['total' => 0, 'working' => 0, 'damaged' => 0, 'missing' => 0, 'under_repair' => 0]
            );

            // Validate that working stock is sufficient
            if ($inv->working < $data['quantity_affected']) {
                abort(422, 'Cannot report ' . $data['quantity_affected'] . ' damaged — only ' . $inv->working . ' working item(s) recorded.');
            }

            // Update inventory: working → damaged
            $inv->working -= $data['quantity_affected'];
            $inv->damaged += $data['quantity_affected'];
            $inv->save();

            // Create damage report
            BranchAssetDamageReport::create([
                'office_id'         => $data['office_id'],
                'category_id'       => $data['category_id'],
                'quantity_affected' => $data['quantity_affected'],
                'reported_date'     => $data['reported_date'],
                'description'       => $data['description'],
                'photo'             => $photoPath,
                'status'            => 'Reported',
                'reported_by'       => auth()->id(),
            ]);
        });

        return redirect()->route('goa.asset-manager.damage-reports')
            ->with('success', 'Damage report submitted. Inventory updated.');
    }

    public function updateDamageReport(Request $request, $id)
    {
        $report = BranchAssetDamageReport::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:Reported,Assessed,Sent for Repair,Repaired,Closed',
            'notes'  => 'nullable|string',
        ]);

        $oldStatus = $report->status;
        $newStatus = $data['status'];

        DB::transaction(function () use ($report, $data, $oldStatus, $newStatus) {
            $report->update($data);

            // Move damaged → under_repair when sent for repair
            if ($oldStatus !== 'Sent for Repair' && $newStatus === 'Sent for Repair') {
                $inv = BranchAssetInventory::where('office_id', $report->office_id)
                    ->where('category_id', $report->category_id)
                    ->first();
                if ($inv && $inv->damaged >= $report->quantity_affected) {
                    $inv->damaged    -= $report->quantity_affected;
                    $inv->under_repair += $report->quantity_affected;
                    $inv->save();
                }
            }
        });

        return redirect()->route('goa.asset-manager.damage-reports')
            ->with('success', 'Report status updated to "' . $newStatus . '".');
    }

    // =========================================================================
    //  REPAIRS
    // =========================================================================

    public function repairs(Request $request)
    {
        $pendingRepairs = BranchAssetDamageReport::with('office', 'category')
            ->where('status', 'Sent for Repair')
            ->whereDoesntHave('repair')
            ->orderByDesc('reported_date')
            ->get();

        $completedRepairs = BranchAssetRepair::with('damageReport.office', 'damageReport.category')
            ->orderByDesc('repair_date')
            ->get();

        $totalRepairCost = (float) BranchAssetRepair::sum('repair_cost');
        $thisMonthCost   = (float) BranchAssetRepair::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()
        ])->sum('repair_cost');

        $costByBranch = BranchAssetRepair::join('branch_asset_damage_reports as dr', 'dr.id', '=', 'branch_asset_repairs.damage_report_id')
            ->join('offices', 'offices.id', '=', 'dr.office_id')
            ->select('offices.name as office_name', DB::raw('SUM(branch_asset_repairs.repair_cost) as total_cost'), DB::raw('COUNT(*) as repair_count'))
            ->groupBy('offices.name')
            ->orderByDesc('total_cost')
            ->get();

        $costByCategory = BranchAssetRepair::join('branch_asset_damage_reports as dr', 'dr.id', '=', 'branch_asset_repairs.damage_report_id')
            ->join('branch_asset_categories as bac', 'bac.id', '=', 'dr.category_id')
            ->select('bac.name as category_name', DB::raw('SUM(branch_asset_repairs.repair_cost) as total_cost'), DB::raw('COUNT(*) as repair_count'))
            ->groupBy('bac.name')
            ->orderByDesc('total_cost')
            ->get();

        return view('goa.asset-manager.repairs', compact(
            'pendingRepairs', 'completedRepairs', 'totalRepairCost',
            'thisMonthCost', 'costByBranch', 'costByCategory'
        ));
    }

    public function storeRepair(Request $request)
    {
        $data = $request->validate([
            'damage_report_id'=> 'required|integer|exists:branch_asset_damage_reports,id',
            'repair_date'     => 'nullable|date',
            'repair_cost'     => 'nullable|numeric|min:0',
            'repair_provider' => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'invoice'         => 'nullable|file|max:5120',
            'date_returned'   => 'nullable|date',
            'condition_after' => 'required|string|max:100',
        ]);

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('asset-invoices', 'public');
        }

        DB::transaction(function () use ($data, $invoicePath) {
            $report = BranchAssetDamageReport::findOrFail($data['damage_report_id']);

            BranchAssetRepair::create([
                'damage_report_id' => $data['damage_report_id'],
                'repair_date'      => $data['repair_date'] ?? null,
                'repair_cost'      => $data['repair_cost'] ?? 0,
                'repair_provider'  => $data['repair_provider'] ?? null,
                'description'      => $data['description'] ?? null,
                'invoice_path'     => $invoicePath,
                'date_returned'    => $data['date_returned'] ?? null,
                'condition_after'  => $data['condition_after'],
            ]);

            // Update damage report status
            $report->status = 'Repaired';
            $report->save();

            // Update inventory: under_repair → working or stays damaged
            $inv = BranchAssetInventory::where('office_id', $report->office_id)
                ->where('category_id', $report->category_id)
                ->first();

            if ($inv) {
                $qty = $report->quantity_affected;
                if ($inv->under_repair >= $qty) {
                    $inv->under_repair -= $qty;
                } else {
                    // Fallback: deduct from damaged
                    $inv->damaged = max(0, $inv->damaged - $qty);
                }

                if ($data['condition_after'] === 'Working') {
                    $inv->working += $qty;
                } else {
                    // Damaged beyond repair: reduce total
                    $inv->total = max(0, $inv->total - $qty);
                }
                $inv->save();
            }
        });

        return redirect()->route('goa.asset-manager.repairs')
            ->with('success', 'Repair logged. Inventory updated.');
    }

    public function updateRepair(Request $request, $id)
    {
        $repair = BranchAssetRepair::findOrFail($id);

        $data = $request->validate([
            'repair_cost'    => 'nullable|numeric|min:0',
            'repair_provider'=> 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'condition_after'=> 'nullable|string|max:100',
        ]);

        $repair->update($data);

        return redirect()->route('goa.asset-manager.repairs')
            ->with('success', 'Repair record updated.');
    }

    // =========================================================================
    //  VERIFICATION
    // =========================================================================

    public function verification(Request $request)
    {
        $offices = Office::where('active', 1)->orderBy('name')->get();

        $pendingVerifications = BranchAssetVerification::with('office', 'requester')
            ->where('status', 'Pending')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($v) {
                // Attach inventory snapshot for the submit modal
                $snapshot = BranchAssetInventory::with('category')
                    ->where('office_id', $v->office_id)
                    ->get()
                    ->map(function ($inv) {
                        return [
                            'category_name' => optional($inv->category)->name,
                            'total'         => $inv->total,
                            'working'       => $inv->working,
                            'damaged'       => $inv->damaged,
                            'missing'       => $inv->missing,
                            'under_repair'  => $inv->under_repair,
                        ];
                    })->values()->toArray();
                $v->branchInventorySnapshot = $snapshot;
                return $v;
            });

        $submittedVerifications = BranchAssetVerification::with('office', 'requester', 'submitter')
            ->whereIn('status', ['Submitted', 'Acknowledged'])
            ->orderByDesc('submitted_at')
            ->get();

        // Offices that have never had any verification submitted
        $verifiedOfficeIds = BranchAssetVerification::whereIn('status', ['Submitted', 'Acknowledged'])
            ->pluck('office_id')
            ->unique();

        $neverVerifiedOffices = Office::withCount('inventories')
            ->where('active', 1)
            ->whereNotIn('id', $verifiedOfficeIds)
            ->orderBy('name')
            ->get();

        return view('goa.asset-manager.verification', compact(
            'offices', 'pendingVerifications', 'submittedVerifications', 'neverVerifiedOffices'
        ));
    }

    public function requestVerification(Request $request)
    {
        $data = $request->validate([
            'office_ids'   => 'required|array|min:1',
            'office_ids.*' => 'integer|exists:offices,id',
            'period'       => 'required|string|max:50',
            'notes'        => 'nullable|string',
        ]);

        foreach ($data['office_ids'] as $officeId) {
            BranchAssetVerification::create([
                'office_id'    => $officeId,
                'period'       => $data['period'],
                'requested_by' => auth()->id(),
                'status'       => 'Pending',
                'notes'        => $data['notes'] ?? null,
            ]);
        }

        $count = count($data['office_ids']);
        return redirect()->route('goa.asset-manager.verification')
            ->with('success', 'Verification request sent to ' . $count . ' branch(es) for "' . $data['period'] . '".');
    }

    public function submitVerification(Request $request, $id)
    {
        $verification = BranchAssetVerification::findOrFail($id);

        $data = $request->validate([
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($verification, $data) {
            $verification->update([
                'submitted_by' => auth()->id(),
                'submitted_at' => now(),
                'status'       => 'Submitted',
                'notes'        => $data['notes'] ?? $verification->notes,
            ]);

            // Update last_verified_at on all inventory records for this office
            BranchAssetInventory::where('office_id', $verification->office_id)
                ->update([
                    'last_verified_at' => now(),
                    'last_verified_by' => auth()->id(),
                ]);
        });

        return redirect()->route('goa.asset-manager.verification')
            ->with('success', 'Verification submitted for ' . optional($verification->office)->name . '.');
    }
}

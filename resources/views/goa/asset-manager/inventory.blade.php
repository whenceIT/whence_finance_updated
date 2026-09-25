@extends('layouts.master')
@section('title')GOA Manager - Asset Register @endsection
@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 style="font-weight:700;color:#1e293b;margin-bottom:4px;"><i class="fa fa-list-alt" style="color:#2563eb;"></i> Asset Register</h2>
            <p class="text-muted" style="margin:0;">Full asset register — {{ number_format($grandTotals->total_lines ?? 0) }} records &nbsp;|&nbsp; Total Value: <strong>K{{ number_format($grandTotals->total_value ?? 0, 2) }}</strong></p>
        </div>
        <div class="col-md-4 text-right" style="padding-top:10px;">
            <a href="{{ route('goa.asset-manager.dashboard') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Dashboard</a>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAssetModal"><i class="fa fa-plus"></i> Add Asset</button>
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#addCategoryModal"><i class="fa fa-tag"></i> Categories</button>
        </div>
    </div>

    {{-- Grand total stat bar --}}
    <div class="row mb-2">
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-blue" style="height:70px;line-height:70px;"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Total Lines</span>
                    <span class="info-box-number">{{ number_format($grandTotals->total_lines ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-aqua" style="height:70px;line-height:70px;"><i class="fa fa-hashtag"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Total Qty</span>
                    <span class="info-box-number">{{ number_format($grandTotals->total_qty ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-yellow" style="height:70px;line-height:70px;"><i class="fa fa-money"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Total Value (K)</span>
                    <span class="info-box-number" style="font-size:1rem;">{{ number_format($grandTotals->total_value ?? 0, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-green" style="height:70px;line-height:70px;"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Working</span>
                    <span class="info-box-number">{{ number_format($grandTotals->total_working ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-red" style="height:70px;line-height:70px;"><i class="fa fa-times-circle"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Damaged</span>
                    <span class="info-box-number">{{ number_format($grandTotals->total_damaged ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-purple" style="height:70px;line-height:70px;"><i class="fa fa-question-circle"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Missing</span>
                    <span class="info-box-number">{{ number_format($grandTotals->total_missing ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs" style="margin-bottom:0;">
        <li class="{{ $activeTab === 'register' ? 'active' : '' }}"><a href="#tab-register" data-toggle="tab"><i class="fa fa-table"></i> Asset Register</a></li>
        <li class="{{ $activeTab === 'by-location' ? 'active' : '' }}"><a href="#tab-location" data-toggle="tab"><i class="fa fa-map-marker"></i> By Location</a></li>
        <li class="{{ $activeTab === 'by-category' ? 'active' : '' }}"><a href="#tab-category" data-toggle="tab"><i class="fa fa-tags"></i> By Category</a></li>
        <li class="{{ $activeTab === 'categories' ? 'active' : '' }}"><a href="#tab-categories" data-toggle="tab"><i class="fa fa-cog"></i> Manage Categories</a></li>
    </ul>

    <div class="tab-content" style="border:1px solid #ddd;border-top:none;padding:15px;background:#fff;">

        {{-- ================================================================
             ASSET REGISTER TAB
        ================================================================ --}}
        <div class="tab-pane {{ $activeTab === 'register' ? 'active' : '' }}" id="tab-register">

            {{-- Filter bar --}}
            <form method="GET" action="{{ route('goa.asset-manager.inventory') }}" class="form-inline mb-3" style="gap:6px;flex-wrap:wrap;display:flex;">
                <input type="hidden" name="tab" value="register">
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search asset ID, description, serial, allocated to..." style="min-width:240px;">
                <select name="location_id" class="form-control form-control-sm">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <select name="category_id" class="form-control form-control-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="condition" class="form-control form-control-sm">
                    <option value="">All Conditions</option>
                    @foreach(['good','fair','new','bad','damaged','needs repair','not working','missing','old','excellent'] as $cond)
                        <option value="{{ $cond }}" {{ strtolower($condition) == $cond ? 'selected' : '' }}>{{ ucfirst($cond) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filter</button>
                <a href="{{ route('goa.asset-manager.inventory') }}" class="btn btn-default btn-sm"><i class="fa fa-times"></i> Clear</a>
            </form>

            {{-- Register table --}}
            <div style="overflow-x:auto;">
                <table class="table table-bordered table-hover" style="margin:0;" id="registerTable">
                    <thead style="background:#1e293b;color:#fff;">
                        <tr>
                            <th style="white-space:nowrap;width:85px;">Asset ID</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-center" style="width:45px;">Qty</th>
                            <th style="max-width:140px;">Serial / Asset No.</th>
                            <th class="text-right" style="width:90px;">Unit Cost (K)</th>
                            <th class="text-right" style="width:100px;">Total Value (K)</th>
                            <th style="width:110px;">Condition</th>
                            <th>Allocated To</th>
                            <th style="max-width:160px;">Remarks</th>
                            <th style="max-width:160px;">Action Required</th>
                            <th class="text-center" style="width:60px;">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        @php
                            $cond = strtolower($item->condition_text ?? '');
                            $condBg = match(true) {
                                in_array($cond, ['good','good condition','excellent','new','very good','mint']) => '#e8f5e9',
                                in_array($cond, ['fair','old','used']) => '#fff8e1',
                                str_contains($cond,'repair') || str_contains($cond,'needs') => '#fff3e0',
                                in_array($cond, ['bad','damaged','not working','broken','poor','missing']) => '#ffebee',
                                default => 'transparent',
                            };
                        @endphp
                        <tr style="background:{{ $condBg }};">
                            <td><code>{{ $item->asset_id }}</code></td>
                            <td style="white-space:nowrap;">{{ optional($item->location)->name ?? '—' }}</td>
                            <td style="white-space:nowrap;">{{ optional($item->category)->name ?? '—' }}</td>
                            <td>{{ $item->item_description }}</td>
                            <td class="text-center"><strong>{{ $item->total }}</strong></td>
                            <td style="word-break:break-all;">{{ $item->serial_number ?? '—' }}</td>
                            <td class="text-right">{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '—' }}</td>
                            <td class="text-right"><strong>{{ $item->total_value ? number_format($item->total_value, 2) : '—' }}</strong></td>
                            <td>
                                @if($item->condition_text)
                                    <span>{{ $item->condition_text }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $item->allocated_to ?? '—' }}</td>
                            <td style="max-width:160px;">{{ $item->remarks ? mb_strimwidth($item->remarks, 0, 63, '…') : '—' }}</td>
                            <td style="max-width:160px;">
                                @if($item->action_required)
                                    <span style="color:#c0392b;font-weight:600;">{{ mb_strimwidth($item->action_required, 0, 63, '…') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-primary edit-asset-btn"
                                    data-id="{{ $item->id }}"
                                    data-asset="{{ $item->asset_id }}"
                                    data-description="{{ $item->item_description }}"
                                    data-qty="{{ $item->total }}"
                                    data-serial="{{ $item->serial_number }}"
                                    data-unit="{{ $item->unit_cost }}"
                                    data-total="{{ $item->total_value }}"
                                    data-condition="{{ $item->condition_text }}"
                                    data-allocated="{{ $item->allocated_to }}"
                                    data-remarks="{{ $item->remarks }}"
                                    data-action="{{ $item->action_required }}"
                                    data-location="{{ $item->location_id }}"
                                    data-category="{{ $item->category_id }}"
                                    data-toggle="modal" data-target="#editAssetModal">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="13" class="text-center text-muted" style="padding:20px;">No records found. Adjust filters or seed the data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($items->hasPages())
            <div class="mt-2 text-center">
                {{ $items->links() }}
                <small class="text-muted">Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} records</small>
            </div>
            @endif
        </div>{{-- /tab-register --}}

        {{-- ================================================================
             BY LOCATION TAB
        ================================================================ --}}
        <div class="tab-pane {{ $activeTab === 'by-location' ? 'active' : '' }}" id="tab-location">
            <h4 style="margin-top:0;font-weight:700;">Asset Summary by Location</h4>
            <div style="overflow-x:auto;">
                <table class="table table-bordered table-hover table-sm" id="locationTable">
                    <thead style="background:#1e293b;color:#fff;">
                        <tr>
                            <th>Location</th>
                            <th>Type</th>
                            <th class="text-center">Item Lines</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-right">Total Value (K)</th>
                            <th class="text-center">Working</th>
                            <th class="text-center">Damaged</th>
                            <th class="text-center">Under Repair</th>
                            <th class="text-center">Missing</th>
                            <th class="text-center">Condition %</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locationSummary as $row)
                        @php
                            $pct = $row->total_qty > 0 ? round(($row->total_working / $row->total_qty) * 100, 1) : null;
                            $pctColor = $pct === null ? '#999' : ($pct >= 90 ? '#27ae60' : ($pct >= 70 ? '#f39c12' : '#e74c3c'));
                        @endphp
                        <tr>
                            <td><strong>{{ $row->location_name }}</strong></td>
                            <td><span class="label label-default">{{ ucfirst($row->location_type) }}</span></td>
                            <td class="text-center">{{ $row->item_lines }}</td>
                            <td class="text-center">{{ number_format($row->total_qty) }}</td>
                            <td class="text-right">K{{ number_format($row->total_value, 2) }}</td>
                            <td class="text-center" style="color:#27ae60;font-weight:600;">{{ number_format($row->total_working) }}</td>
                            <td class="text-center" style="color:{{ $row->total_damaged > 0 ? '#e74c3c' : '#999' }};font-weight:{{ $row->total_damaged > 0 ? 700 : 400 }};">{{ $row->total_damaged }}</td>
                            <td class="text-center" style="color:{{ $row->total_under_repair > 0 ? '#f39c12' : '#999' }};">{{ $row->total_under_repair }}</td>
                            <td class="text-center" style="color:{{ $row->total_missing > 0 ? '#8e44ad' : '#999' }};">{{ $row->total_missing }}</td>
                            <td class="text-center">
                                @if($pct !== null)
                                    <span style="font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('goa.asset-manager.inventory', ['location_id' => $row->location_id]) }}" class="btn btn-xs btn-default">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#f1f5f9;font-weight:700;">
                        <tr>
                            <td colspan="2">TOTAL</td>
                            <td class="text-center">{{ $locationSummary->sum('item_lines') }}</td>
                            <td class="text-center">{{ number_format($locationSummary->sum('total_qty')) }}</td>
                            <td class="text-right">K{{ number_format($locationSummary->sum('total_value'), 2) }}</td>
                            <td class="text-center">{{ number_format($locationSummary->sum('total_working')) }}</td>
                            <td class="text-center">{{ number_format($locationSummary->sum('total_damaged')) }}</td>
                            <td class="text-center">{{ number_format($locationSummary->sum('total_under_repair')) }}</td>
                            <td class="text-center">{{ number_format($locationSummary->sum('total_missing')) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>{{-- /tab-location --}}

        {{-- ================================================================
             BY CATEGORY TAB
        ================================================================ --}}
        <div class="tab-pane {{ $activeTab === 'by-category' ? 'active' : '' }}" id="tab-category">
            <h4 style="margin-top:0;font-weight:700;">Asset Summary by Category</h4>
            <table class="table table-bordered table-hover table-sm" id="categoryTable">
                <thead style="background:#1e293b;color:#fff;">
                    <tr>
                        <th>Category</th>
                        <th class="text-center">Item Lines</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-right">Total Value (K)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorySummary as $row)
                    <tr>
                        <td>{{ $row->category_name }}</td>
                        <td class="text-center">{{ $row->item_lines }}</td>
                        <td class="text-center">{{ number_format($row->total_qty) }}</td>
                        <td class="text-right">K{{ number_format($row->total_value, 2) }}</td>
                        <td><a href="{{ route('goa.asset-manager.inventory', ['category_id' => $categories->firstWhere('name', $row->category_name)?->id]) }}" class="btn btn-xs btn-default">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f1f5f9;font-weight:700;">
                    <tr>
                        <td>TOTAL</td>
                        <td class="text-center">{{ $categorySummary->sum('item_lines') }}</td>
                        <td class="text-center">{{ number_format($categorySummary->sum('total_qty')) }}</td>
                        <td class="text-right">K{{ number_format($categorySummary->sum('total_value'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>{{-- /tab-category --}}

        {{-- ================================================================
             MANAGE CATEGORIES TAB
        ================================================================ --}}
        <div class="tab-pane {{ $activeTab === 'categories' ? 'active' : '' }}" id="tab-categories">
            <table class="table table-bordered table-hover table-sm" id="categoriesTable">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>#</th><th>Category Name</th><th class="text-center">Individual Tracking</th>
                        <th class="text-center">Status</th><th class="text-center">In Use</th><th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoriesWithCount as $cat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><i class="fa {{ $cat->icon ?? 'fa-tag' }}"></i> {{ $cat->name }}</td>
                        <td class="text-center">@if($cat->allows_individual_tracking)<span class="label label-info">Yes</span>@else<span class="text-muted">No</span>@endif</td>
                        <td class="text-center">@if($cat->active)<span class="label label-success">Active</span>@else<span class="label label-default">Inactive</span>@endif</td>
                        <td class="text-center">{{ $cat->inventory_count ?? 0 }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-primary edit-cat-btn" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-icon="{{ $cat->icon }}" data-individual="{{ $cat->allows_individual_tracking ? 1 : 0 }}" data-toggle="modal" data-target="#editCategoryModal"><i class="fa fa-pencil"></i></button>
                            <form method="POST" action="{{ route('goa.asset-manager.categories.toggle', $cat->id) }}" style="display:inline;">@csrf @method('PATCH')
                                <button class="btn btn-xs {{ $cat->active ? 'btn-warning' : 'btn-success' }}" type="submit" onclick="return confirm('{{ $cat->active ? 'Deactivate' : 'Activate' }} this category?')"><i class="fa {{ $cat->active ? 'fa-ban' : 'fa-check' }}"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>{{-- /tab-categories --}}

    </div>{{-- /tab-content --}}
</div>

{{-- ============================================================ MODALS ============================================================ --}}

{{-- Add Asset Modal --}}
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('goa.asset-manager.inventory.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title"><i class="fa fa-plus"></i> Add Asset Record</h4></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Asset ID</label><input type="text" name="asset_id" class="form-control" placeholder="WFS-XXXX"></div></div>
                        <div class="col-md-5"><div class="form-group"><label>Location <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                            </select>
                        </div></div>
                        <div class="col-md-4"><div class="form-group"><label>Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                            </select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Item Description <span class="text-danger">*</span></label><input type="text" name="item_description" class="form-control" required></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Quantity</label><input type="number" name="total" class="form-control" value="1" min="0"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Serial / Asset No.</label><input type="text" name="serial_number" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Unit Cost (K)</label><input type="number" step="0.01" name="unit_cost" class="form-control" min="0"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Total Value (K)</label><input type="number" step="0.01" name="total_value" class="form-control" min="0"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Condition</label><input type="text" name="condition_text" class="form-control" placeholder="e.g. Good, Fair, Damaged"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Allocated To</label><input type="text" name="allocated_to" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2"></textarea></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Action Required</label><textarea name="action_required" class="form-control" rows="2"></textarea></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Valuation Basis</label><input type="text" name="valuation_basis" class="form-control"></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Asset Modal --}}
<div class="modal fade" id="editAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editAssetForm" action="">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" id="editAssetTitle"><i class="fa fa-pencil"></i> Edit Asset</h4></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Asset ID</label><input type="text" name="asset_id" id="eAssetId" class="form-control"></div></div>
                        <div class="col-md-5"><div class="form-group"><label>Location</label>
                            <select name="location_id" id="eLocationId" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                            </select>
                        </div></div>
                        <div class="col-md-4"><div class="form-group"><label>Category</label>
                            <select name="category_id" id="eCategoryId" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                            </select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Item Description</label><input type="text" name="item_description" id="eDescription" class="form-control"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Quantity</label><input type="number" name="total" id="eQty" class="form-control" min="0"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Serial / Asset No.</label><input type="text" name="serial_number" id="eSerial" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Unit Cost (K)</label><input type="number" step="0.01" name="unit_cost" id="eUnitCost" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Total Value (K)</label><input type="number" step="0.01" name="total_value" id="eTotalValue" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Condition</label><input type="text" name="condition_text" id="eCondition" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Allocated To</label><input type="text" name="allocated_to" id="eAllocated" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Remarks</label><textarea name="remarks" id="eRemarks" class="form-control" rows="2"></textarea></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Action Required</label><textarea name="action_required" id="eAction" class="form-control" rows="2"></textarea></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Valuation Basis</label><input type="text" name="valuation_basis" id="eValuation" class="form-control"></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('goa.asset-manager.categories.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title"><i class="fa fa-plus"></i> Add Asset Category</h4></div>
                <div class="modal-body">
                    <div class="form-group"><label>Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group"><label>Icon (FA class)</label><input type="text" name="icon" class="form-control" value="fa-tag"></div>
                    <div class="form-group"><label><input type="checkbox" name="allows_individual_tracking" value="1"> Allow individual tracking</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editCategoryForm" action="">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">Edit Category</h4></div>
                <div class="modal-body">
                    <div class="form-group"><label>Name <span class="text-danger">*</span></label><input type="text" name="name" id="editCatName" class="form-control" required></div>
                    <div class="form-group"><label>Icon</label><input type="text" name="icon" id="editCatIcon" class="form-control"></div>
                    <div class="form-group"><label><input type="checkbox" name="allows_individual_tracking" id="editCatIndividual" value="1"> Allow individual tracking</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    // Edit asset
    $(document).on('click', '.edit-asset-btn', function () {
        var d = $(this).data();
        $('#editAssetTitle').text('Edit Asset — ' + d.asset);
        $('#editAssetForm').attr('action', '/goa_dashboard/asset-manager/inventory/' + d.id);
        $('#eAssetId').val(d.asset);
        $('#eLocationId').val(d.location);
        $('#eCategoryId').val(d.category);
        $('#eDescription').val(d.description);
        $('#eQty').val(d.qty);
        $('#eSerial').val(d.serial);
        $('#eUnitCost').val(d.unit);
        $('#eTotalValue').val(d.total);
        $('#eCondition').val(d.condition);
        $('#eAllocated').val(d.allocated);
        $('#eRemarks').val(d.remarks);
        $('#eAction').val(d.action);
    });

    // Edit category
    $(document).on('click', '.edit-cat-btn', function () {
        var d = $(this).data();
        $('#editCatName').val(d.name);
        $('#editCatIcon').val(d.icon);
        $('#editCatIndividual').prop('checked', d.individual == 1);
        $('#editCategoryForm').attr('action', '/goa_dashboard/asset-manager/categories/' + d.id);
    });
});
</script>
@endsection

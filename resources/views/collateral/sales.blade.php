@extends('layouts.master')
@section('title', 'Collateral Sales')

@section('content')
@php
    $role = Sentinel::getUser()->roles()->first()->id;
@endphp

{{-- ══════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════ --}}
<div class="box box-primary">
    <div class="box-header with-border" style="background: linear-gradient(135deg, #1a6b3a 0%, #28a745 100%);">
        <h3 class="box-title" style="color:#fff;">
            <i class="fa fa-money" style="margin-right:6px;"></i> Collateral Sales
        </h3>
        <div style="margin-top:6px;">
            <h4 style="margin:0;font-weight:600;color:#fff;">Sold Collateral Overview</h4>
            <p style="margin:0;font-size:12px;color:#c8f7d6;">
                All collateral that has been successfully sold &mdash; proceeds, buyer information, and recovery analysis
            </p>
        </div>
        <div class="box-tools pull-right" style="margin-top:4px;">
            <a href="{{ route('collateral.index', ['key' => 'admin']) }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    {{-- ══ KPI STAT CARDS ══════════════════════════════════════════════════ --}}
    <div class="box-body" style="background:#f8fafb; padding-bottom:0;">
        <div class="row" style="margin-bottom:0;">

            {{-- Total Sold --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-tag" style="margin-right:4px;"></i>Items Sold
                    </div>
                    <div style="font-size:28px;font-weight:700;color:#1a6b3a;">{{ number_format($totalCount) }}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">collateral items</div>
                </div>
            </div>

            {{-- Total Sold Price --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-dollar" style="margin-right:4px;"></i>Total Sold Price
                    </div>
                    <div style="font-size:20px;font-weight:700;color:#1a6b3a;">{{ number_format($totalSoldPrice, 2) }}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">gross proceeds</div>
                </div>
            </div>

            {{-- Net Proceeds --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-check-circle" style="margin-right:4px;"></i>Net Proceeds
                    </div>
                    <div style="font-size:20px;font-weight:700;color:{{ $netProceeds >= 0 ? '#1a6b3a' : '#c0392b' }};">
                        {{ number_format($netProceeds, 2) }}
                    </div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">after costs &amp; penalties</div>
                </div>
            </div>

            {{-- Recovery Rate --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-percent" style="margin-right:4px;"></i>Recovery Rate
                    </div>
                    <div style="font-size:28px;font-weight:700;color:{{ $recoveryRate >= 80 ? '#1a6b3a' : ($recoveryRate >= 50 ? '#e67e22' : '#c0392b') }};">
                        {{ $recoveryRate }}%
                    </div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">sold / appraised value</div>
                </div>
            </div>

            {{-- Total Disposal Costs --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-minus-circle" style="margin-right:4px;"></i>Disposal Costs
                    </div>
                    <div style="font-size:20px;font-weight:700;color:#c0392b;">{{ number_format($totalDisposalCosts, 2) }}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">incurred costs</div>
                </div>
            </div>

            {{-- Depreciation Rate --}}
            <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                        <i class="fa fa-arrow-down" style="margin-right:4px;"></i>Depreciation
                    </div>
                    <div style="font-size:28px;font-weight:700;color:#8e44ad;">{{ $depreciationRate }}%</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">from purchase price</div>
                </div>
            </div>

        </div>{{-- /.row --}}

        {{-- secondary stats row --}}
        <div class="row" style="margin-bottom:0;">
            <div class="col-md-3 col-sm-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#6b7280;">Total Purchase Price</span>
                    <span style="font-size:14px;font-weight:600;color:#374151;">{{ number_format($totalInitial, 2) }}</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#6b7280;">Total Appraised Value</span>
                    <span style="font-size:14px;font-weight:600;color:#374151;">{{ number_format($totalWorth, 2) }}</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#6b7280;">Total Penalties</span>
                    <span style="font-size:14px;font-weight:600;color:#c0392b;">{{ number_format($totalPenalty, 2) }}</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:16px;">
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#6b7280;">Total Vetted Valuation</span>
                    <span style="font-size:14px;font-weight:600;color:#374151;">{{ number_format($totalVettedVal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>{{-- /.box-body (kpi) --}}
</div>

{{-- ══════════════════════════════════════════════════════
     ANALYSIS PANELS  (charts + breakdowns)
══════════════════════════════════════════════════════ --}}
<div class="row">

    {{-- Monthly Sales Trend --}}
    <div class="col-md-8" style="margin-bottom:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05);">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4f8;">
                <h4 style="margin:0;font-size:14px;font-weight:600;color:#2c3e50;text-transform:uppercase;letter-spacing:.03em;">
                    <i class="fa fa-line-chart" style="margin-right:6px;color:#28a745;"></i>Monthly Sales Trend — Last 12 Months
                </h4>
            </div>
            <div style="padding:20px;">
                @if($monthlySales->isEmpty())
                    <p class="text-muted text-center" style="padding:30px 0;">No sales data available.</p>
                @else
                    <canvas id="monthlySalesChart" height="90"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Offices --}}
    <div class="col-md-4" style="margin-bottom:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4f8;">
                <h4 style="margin:0;font-size:14px;font-weight:600;color:#2c3e50;text-transform:uppercase;letter-spacing:.03em;">
                    <i class="fa fa-building" style="margin-right:6px;color:#28a745;"></i>Top Offices by Sales
                </h4>
            </div>
            <div style="padding:10px 20px;">
                @forelse($topOffices as $off)
                    @php $pct = $totalCount > 0 ? round(($off['cnt'] / $totalCount) * 100) : 0; @endphp
                    <div style="padding:8px 0;border-bottom:1px solid #f8f9fa;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:13px;font-weight:500;color:#374151;">{{ $off['office'] }}</span>
                            <span style="font-size:12px;color:#6b7280;">{{ $off['cnt'] }} sold</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;background:#e9ecef;border-radius:4px;height:6px;">
                                <div style="width:{{ $pct }}%;background:#28a745;height:6px;border-radius:4px;"></div>
                            </div>
                            <span style="font-size:11px;color:#9ca3af;min-width:32px;text-align:right;">{{ $pct }}%</span>
                        </div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Revenue: {{ number_format($off['revenue'], 2) }}</div>
                    </div>
                @empty
                    <p class="text-muted text-center" style="padding:20px 0;">No data.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>{{-- /.row (charts) --}}

<div class="row">

    {{-- Category Breakdown --}}
    <div class="col-md-5" style="margin-bottom:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05);">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4f8;">
                <h4 style="margin:0;font-size:14px;font-weight:600;color:#2c3e50;text-transform:uppercase;letter-spacing:.03em;">
                    <i class="fa fa-pie-chart" style="margin-right:6px;color:#28a745;"></i>Category Breakdown
                </h4>
            </div>
            <div style="padding:16px 20px;">
                @if($categoryBreakdown->isEmpty())
                    <p class="text-muted text-center" style="padding:20px 0;">No data.</p>
                @else
                    @php $categories_map = \App\Models\Collateral::CATEGORIES; @endphp
                    <table class="table table-condensed" style="margin:0;">
                        <thead>
                            <tr style="font-size:11px;color:#6b7280;text-transform:uppercase;">
                                <th style="border-top:0;">Category</th>
                                <th style="border-top:0;text-align:right;">Count</th>
                                <th style="border-top:0;text-align:right;">Revenue</th>
                                <th style="border-top:0;text-align:right;">Appraised</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryBreakdown as $cat)
                                <tr style="font-size:13px;">
                                    <td>{{ $categories_map[$cat->category] ?? ucfirst($cat->category ?? 'Unknown') }}</td>
                                    <td style="text-align:right;">{{ $cat->cnt }}</td>
                                    <td style="text-align:right;">{{ number_format($cat->revenue, 2) }}</td>
                                    <td style="text-align:right;">{{ number_format($cat->worth, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Type Breakdown --}}
    <div class="col-md-4" style="margin-bottom:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05);">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4f8;">
                <h4 style="margin:0;font-size:14px;font-weight:600;color:#2c3e50;text-transform:uppercase;letter-spacing:.03em;">
                    <i class="fa fa-list" style="margin-right:6px;color:#28a745;"></i>Collateral Type Breakdown
                </h4>
            </div>
            <div style="padding:10px 20px;">
                @forelse($typeBreakdown as $td)
                    @php $tpct = $totalCount > 0 ? round(($td->cnt / $totalCount) * 100) : 0; @endphp
                    <div style="padding:8px 0;border-bottom:1px solid #f8f9fa;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:13px;font-weight:500;color:#374151;">{{ optional($td->type)->name ?? 'Unknown' }}</span>
                            <span style="font-size:12px;color:#6b7280;">{{ $td->cnt }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;background:#e9ecef;border-radius:4px;height:6px;">
                                <div style="width:{{ $tpct }}%;background:#17a2b8;height:6px;border-radius:4px;"></div>
                            </div>
                            <span style="font-size:11px;color:#9ca3af;min-width:32px;text-align:right;">{{ $tpct }}%</span>
                        </div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Revenue: {{ number_format($td->revenue, 2) }}</div>
                    </div>
                @empty
                    <p class="text-muted text-center" style="padding:20px 0;">No data.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Financial Summary Panel --}}
    <div class="col-md-3" style="margin-bottom:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05);height:100%;">
            <div style="padding:14px 20px;border-bottom:1px solid #f0f4f8;">
                <h4 style="margin:0;font-size:14px;font-weight:600;color:#2c3e50;text-transform:uppercase;letter-spacing:.03em;">
                    <i class="fa fa-calculator" style="margin-right:6px;color:#28a745;"></i>Financial Summary
                </h4>
            </div>
            <div style="padding:16px 20px;">
                @php
                    $summaryRows = [
                        ['label' => 'Gross Proceeds',     'value' => number_format($totalSoldPrice, 2),    'color' => '#1a6b3a'],
                        ['label' => '− Disposal Costs',   'value' => number_format($totalDisposalCosts, 2), 'color' => '#c0392b'],
                        ['label' => '− Penalties',        'value' => number_format($totalPenalty, 2),       'color' => '#c0392b'],
                        ['label' => '= Net Proceeds',     'value' => number_format($netProceeds, 2),        'color' => $netProceeds >= 0 ? '#1a6b3a' : '#c0392b'],
                        ['label' => 'Appraised Value',    'value' => number_format($totalWorth, 2),         'color' => '#374151'],
                        ['label' => 'Recovery Rate',      'value' => $recoveryRate . '%',                   'color' => $recoveryRate >= 80 ? '#1a6b3a' : ($recoveryRate >= 50 ? '#e67e22' : '#c0392b')],
                        ['label' => 'Depreciation Rate',  'value' => $depreciationRate . '%',               'color' => '#8e44ad'],
                    ];
                @endphp
                @foreach($summaryRows as $row)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f0f4f8;">
                        <span style="font-size:13px;color:#6b7280;">{{ $row['label'] }}</span>
                        <span style="font-size:13px;font-weight:600;color:{{ $row['color'] }};">{{ $row['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>{{-- /.row (breakdowns) --}}

{{-- ══════════════════════════════════════════════════════
     FILTER FORM
══════════════════════════════════════════════════════ --}}
<div class="box box-default" style="border-radius:10px;">
    <div class="box-header with-border">
        <h3 class="box-title" style="font-size:14px;"><i class="fa fa-filter" style="margin-right:6px;"></i>Filter Sold Collateral</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <form method="get" action="{{ route('collateral.sales') }}" class="form-inline" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;">

            <select name="collateral_type_id" class="form-control input-sm" style="width:140px;">
                <option value="">All Types</option>
                @foreach($collateralTypes as $type)
                    <option value="{{ $type->id }}" {{ request('collateral_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>

            <select name="category" class="form-control input-sm" style="width:150px;">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="condition" class="form-control input-sm" style="width:120px;">
                <option value="">All Conditions</option>
                <option value="new"  {{ request('condition') == 'new'  ? 'selected' : '' }}>New</option>
                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
            </select>

            @if($roleId == 1)
            <select name="province_id" class="form-control input-sm" style="width:140px;">
                <option value="">All Provinces</option>
                @foreach($provinces as $prov)
                    <option value="{{ $prov->id }}" {{ request('province_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                @endforeach
            </select>
            @endif

            @if(in_array($roleId, [1, 12]))
            <select name="office_id" class="form-control input-sm" style="width:150px;">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                @endforeach
            </select>
            @endif

            <div style="display:flex;align-items:center;gap:4px;">
                <label style="font-size:12px;color:#6b7280;margin:0;">Sold From</label>
                <input type="date" name="sold_from" class="form-control input-sm" value="{{ request('sold_from') }}" style="width:130px;">
            </div>
            <div style="display:flex;align-items:center;gap:4px;">
                <label style="font-size:12px;color:#6b7280;margin:0;">To</label>
                <input type="date" name="sold_to" class="form-control input-sm" value="{{ request('sold_to') }}" style="width:130px;">
            </div>

            <select name="sortBy" class="form-control input-sm" style="width:140px;">
                <option value="sold_at"       {{ request('sortBy','sold_at') == 'sold_at'       ? 'selected' : '' }}>Sort: Sold Date</option>
                <option value="sold_price"    {{ request('sortBy') == 'sold_price'    ? 'selected' : '' }}>Sort: Sold Price</option>
                <option value="current_worth" {{ request('sortBy') == 'current_worth' ? 'selected' : '' }}>Sort: Appraised Value</option>
                <option value="initial_price" {{ request('sortBy') == 'initial_price' ? 'selected' : '' }}>Sort: Purchase Price</option>
            </select>
            <select name="sort" class="form-control input-sm" style="width:100px;">
                <option value="desc" {{ request('sort','desc') == 'desc' ? 'selected' : '' }}>Newest</option>
                <option value="asc"  {{ request('sort') == 'asc'  ? 'selected' : '' }}>Oldest</option>
            </select>

            <input type="text" name="search" class="form-control input-sm" placeholder="Name / Buyer / NRC / Serial…" value="{{ request('search') }}" style="width:200px;">

            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filter</button>
            <a href="{{ route('collateral.sales') }}" class="btn btn-default btn-sm"><i class="fa fa-times"></i> Reset</a>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SOLD COLLATERAL TABLE
══════════════════════════════════════════════════════ --}}
<div class="box box-success" style="border-radius:10px;">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-table" style="margin-right:6px;"></i>Sold Items
            <span class="label label-success" style="margin-left:8px;">{{ $collateral->total() }}</span>
        </h3>
    </div>
    <div class="box-body no-padding">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="font-size:13px;margin:0;">
                <thead style="background:#f8fafb;">
                    <tr>
                        <th style="white-space:nowrap;">#</th>
                        <th style="white-space:nowrap;">Name</th>
                        <th style="white-space:nowrap;">Loan ID</th>
                        <th style="white-space:nowrap;">Client</th>
                        <th style="white-space:nowrap;">Type / Category</th>
                        <th style="white-space:nowrap;">Condition</th>
                        <th style="white-space:nowrap;">Purchased</th>
                        <th style="white-space:nowrap;">Sold On</th>
                        <th style="white-space:nowrap;text-align:right;">Purchase Price</th>
                        <th style="white-space:nowrap;text-align:right;">Appraised Value<br><small style="font-weight:normal;font-size:10px;">current worth</small></th>
                        <th style="white-space:nowrap;text-align:right;">Vetted Valuation</th>
                        <th style="white-space:nowrap;text-align:right;">Sold Price</th>
                        <th style="white-space:nowrap;text-align:right;">Disposal Costs</th>
                        <th style="white-space:nowrap;text-align:right;">Penalty</th>
                        <th style="white-space:nowrap;text-align:right;">Net</th>
                        <th style="white-space:nowrap;">Buyer</th>
                        <th style="white-space:nowrap;">Office</th>
                        <th style="white-space:nowrap;">Created By</th>
                        <th style="white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collateral as $i => $item)
                        @php
                            $disposalTotal = 0;
                            if ($item->disposal_costs && is_array($item->disposal_costs)) {
                                foreach ($item->disposal_costs as $cost) {
                                    $disposalTotal += (float) ($cost['amount'] ?? 0);
                                }
                            }
                            $itemNet = ($item->sold_price ?? 0) - $disposalTotal - ($item->penalty ?? 0);
                        @endphp
                        <tr>
                            <td style="color:#9ca3af;">{{ $collateral->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if($item->serial_num)
                                    <br><small style="color:#9ca3af;">S/N: {{ $item->serial_num }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->loan_id)
                                    <a href="{{ url('loan/'.$item->loan_id.'/show') }}" style="font-weight:600;">
                                        #{{ $item->loan_id }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($item->loan && $item->loan->client)
                                    {{ $item->loan->client->first_name }} {{ $item->loan->client->last_name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span>{{ optional($item->type)->name ?? '—' }}</span>
                                @if($item->category)
                                    <br><small style="color:#9ca3af;">{{ \App\Models\Collateral::CATEGORIES[$item->category] ?? ucfirst($item->category) }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $condColor = match($item->condition) {
                                        'new'  => '#1a6b3a',
                                        'good' => '#2980b9',
                                        'fair' => '#e67e22',
                                        'poor' => '#c0392b',
                                        default => '#6b7280',
                                    };
                                @endphp
                                <span style="color:{{ $condColor }};font-weight:600;">{{ ucfirst($item->condition) }}</span>
                            </td>
                            <td style="white-space:nowrap;">{{ optional($item->date_purchased)->format('d M Y') ?? '—' }}</td>
                            <td style="white-space:nowrap;">
                                {{ optional($item->sold_at)->format('d M Y') ?? (optional($item->date_resold)->format('d M Y') ?? '—') }}
                            </td>
                            <td style="text-align:right;">{{ number_format($item->initial_price, 2) }}</td>
                            <td style="text-align:right;font-weight:600;">{{ number_format($item->current_worth, 2) }}</td>
                            <td style="text-align:right;">
                                @if($item->vetted_valuation)
                                    {{ number_format($item->vetted_valuation, 2) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:700;color:#1a6b3a;">{{ number_format($item->sold_price ?? 0, 2) }}</td>
                            <td style="text-align:right;color:#c0392b;">{{ number_format($disposalTotal, 2) }}</td>
                            <td style="text-align:right;color:#c0392b;">{{ number_format($item->penalty ?? 0, 2) }}</td>
                            <td style="text-align:right;font-weight:600;color:{{ $itemNet >= 0 ? '#1a6b3a' : '#c0392b' }};">
                                {{ number_format($itemNet, 2) }}
                            </td>
                            <td>
                                @if($item->buyer_name)
                                    <strong>{{ $item->buyer_name }}</strong>
                                    @if($item->buyer_phone)
                                        <br><small style="color:#6b7280;"><i class="fa fa-phone"></i> {{ $item->buyer_phone }}</small>
                                    @endif
                                    @if($item->buyer_nrc)
                                        <br><small style="color:#9ca3af;">NRC: {{ $item->buyer_nrc }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $item->loan?->office?->name ?? '—' }}</td>
                            <td>
                                {{ optional($item->created_by)->first_name }} {{ optional($item->created_by)->last_name }}
                                @if(optional($item->created_by)->position_name)
                                    <br><small style="color:#9ca3af;">{{ $item->created_by->position_name }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('collateral.show', $item) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" class="text-center" style="padding:40px;color:#9ca3af;">
                                <i class="fa fa-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                No sold collateral found matching the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($collateral->count() > 0)
                <tfoot style="background:#f8fafb;font-weight:600;">
                    <tr>
                        <td colspan="8" style="text-align:right;font-size:12px;color:#6b7280;">Page Totals →</td>
                        <td style="text-align:right;">
                            {{ number_format($collateral->sum('initial_price'), 2) }}
                        </td>
                        <td style="text-align:right;">
                            {{ number_format($collateral->sum('current_worth'), 2) }}
                        </td>
                        <td style="text-align:right;">
                            {{ number_format($collateral->sum('vetted_valuation'), 2) }}
                        </td>
                        <td style="text-align:right;color:#1a6b3a;">
                            {{ number_format($collateral->sum('sold_price'), 2) }}
                        </td>
                        <td style="text-align:right;color:#c0392b;">
                            @php
                                $pageDisposal = $collateral->sum(function($item) {
                                    if (!$item->disposal_costs || !is_array($item->disposal_costs)) return 0;
                                    return collect($item->disposal_costs)->sum('amount');
                                });
                            @endphp
                            {{ number_format($pageDisposal, 2) }}
                        </td>
                        <td style="text-align:right;color:#c0392b;">
                            {{ number_format($collateral->sum('penalty'), 2) }}
                        </td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #e9ecef;">
            <div style="font-size:13px;color:#6b7280;">
                Showing {{ $collateral->firstItem() }}–{{ $collateral->lastItem() }} of {{ number_format($collateral->total()) }} results
            </div>
            <div>
                {{ $collateral->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>

<style>
/* tighten pagination links */
.pagination { margin: 0; }
.pagination > li > a, .pagination > li > span { padding: 4px 10px; font-size: 13px; }
/* table hover highlight */
.table-hover tbody tr:hover { background-color: #f0fdf4 !important; }
</style>

@endsection {{-- content --}}

{{-- ══════════════════════════════════════════════════════
     CHART JS
══════════════════════════════════════════════════════ --}}
@section('footer-scripts')
<script>
@if(!$monthlySales->isEmpty())
(function () {
    var labels  = @json($monthlySales->pluck('period'));
    var counts  = @json($monthlySales->pluck('cnt'));
    var revenue = @json($monthlySales->pluck('revenue'));

    var ctx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Items Sold',
                    data: counts,
                    backgroundColor: 'rgba(40,167,69,0.25)',
                    borderColor: 'rgba(40,167,69,1)',
                    borderWidth: 2,
                    yAxisID: 'yCount',
                    type: 'bar',
                    order: 2,
                },
                {
                    label: 'Revenue',
                    data: revenue,
                    backgroundColor: 'transparent',
                    borderColor: 'rgba(26,107,58,1)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(26,107,58,1)',
                    yAxisID: 'yRevenue',
                    type: 'line',
                    tension: 0.3,
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.dataset.label === 'Revenue') {
                                return ' Revenue: ' + parseFloat(ctx.raw).toLocaleString('en-US', {minimumFractionDigits:2});
                            }
                            return ' Items: ' + ctx.raw;
                        }
                    }
                }
            },
            scales: {
                yCount: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    title: { display: true, text: 'Items Sold' }
                },
                yRevenue: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Revenue' },
                    ticks: {
                        callback: function(v) {
                            return v.toLocaleString('en-US', {minimumFractionDigits:0});
                        }
                    }
                }
            }
        }
    });
})();
@endif
</script>
@endsection {{-- footer-scripts --}}

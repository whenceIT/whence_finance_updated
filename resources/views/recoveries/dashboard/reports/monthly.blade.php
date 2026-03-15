@extends('layouts.master')

@section('title')
    Monthly Recovery Report — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

@php
$catMeta = [
    'cross_branch' => ['label' => 'label-primary',  'bar' => 'progress-bar-primary',  'icon' => 'fa-random'],
    'escalated'    => ['label' => 'label-warning',  'bar' => 'progress-bar-warning',  'icon' => 'fa-arrow-up'],
    'dormant'      => ['label' => 'label-default',  'bar' => 'progress-bar-default',  'icon' => 'fa-moon-o'],
    'legal'        => ['label' => 'label-danger',   'bar' => 'progress-bar-danger',   'icon' => 'fa-gavel'],
    'skip_trace'   => ['label' => 'label-success',  'bar' => 'progress-bar-success',  'icon' => 'fa-search'],
];
$grandTotal     = $totalRecovered;
$resolvedCount  = $bySpecialist->sum('resolved_cases');
$activeCount    = $bySpecialist->sum('active_cases');
$specialistCount = $bySpecialist->count();
@endphp

{{-- ══════════════════════════════════════════════════════
     FILTER + EXPORT BAR
══════════════════════════════════════════════════════ --}}
<div class="box box-solid box-default">
    <div class="box-body" style="padding:12px 18px">
        <form method="GET" action="{{ url('recovery/report/overview') }}"
              style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">

            <i class="fa fa-calendar text-muted"></i>
            <div class="form-group" style="margin:0">
                <select name="month" class="form-control input-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <select name="year" class="form-control input-sm">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-search"></i> Run Report
            </button>

            <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                <span class="text-muted" style="font-size:12px;margin-right:4px">
                    <i class="fa fa-download"></i> Export:
                </span>
                <a href="{{ url('recovery/report/overview/pdf') }}?month={{ $month }}&year={{ $year }}"
                   class="btn btn-sm btn-danger" target="_blank" title="Open print-friendly PDF view">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
                <a href="{{ url('recovery/report/overview/excel') }}?month={{ $month }}&year={{ $year }}"
                   class="btn btn-sm btn-success" title="Download as Excel-compatible CSV">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 1 — KPI TILES
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Recovered</span>
                <span class="info-box-number">{{ number_format($grandTotal, 2) }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">
                    {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cases Resolved</span>
                <span class="info-box-number">{{ $resolvedCount }}</span>
                <div class="progress"><div class="progress-bar" style="width:{{ $activeCount + $resolvedCount > 0 ? round(($resolvedCount/($activeCount+$resolvedCount))*100) : 0 }}%"></div></div>
                <span class="progress-description">{{ $activeCount }} still active</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Specialists</span>
                <span class="info-box-number">{{ $specialistCount }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">With cases this period</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-map-marker"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Branches Contributing</span>
                <span class="info-box-number">{{ $branchBreakdown->count() }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">With recovered payments</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 2 — CATEGORY BREAKDOWN + BRANCH BARS
══════════════════════════════════════════════════════ --}}
<div class="row">

    {{-- Category table --}}
    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-th-list"></i> Recovery by Category</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped" style="margin-bottom:0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-right">Recovered</th>
                            <th class="text-right">Share</th>
                            <th>Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $key => $label)
                        @php
                            $amt  = $byCategory[$key]->total ?? 0;
                            $pct  = $grandTotal > 0 ? round(($amt / $grandTotal) * 100, 1) : 0;
                            $meta = $catMeta[$key];
                        @endphp
                        <tr>
                            <td>
                                <i class="fa {{ $meta['icon'] }} text-muted" style="width:16px"></i>
                                <span class="label {{ $meta['label'] }}">{{ $label }}</span>
                            </td>
                            <td class="text-right"><strong>{{ number_format($amt, 2) }}</strong></td>
                            <td class="text-right"><small class="text-muted">{{ $pct }}%</small></td>
                            <td style="min-width:80px">
                                <div class="progress progress-xs" style="margin:6px 0 0">
                                    <div class="progress-bar {{ $meta['bar'] }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <th colspan="2" class="text-right">
                                <strong>Total: {{ number_format($grandTotal, 2) }}</strong>
                            </th>
                            <th class="text-right"><strong>100%</strong></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Branch progress bars --}}
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-map-marker"></i> Recovery by Branch</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @php $bMax = $branchBreakdown->max('total_recovered') ?: 1; @endphp
                @forelse($branchBreakdown as $b)
                <div class="progress-group">
                    <span class="progress-text">{{ $b->name }}</span>
                    <span class="progress-number">
                        <b>{{ number_format($b->total_recovered, 2) }}</b>
                        &nbsp;<small class="text-muted">({{ $b->case_count }} {{ $b->case_count == 1 ? 'case' : 'cases' }})</small>
                    </span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-green"
                             style="width:{{ round(($b->total_recovered / $bMax) * 100) }}%">
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center" style="padding:20px 0">No branch data for this period.</p>
                @endforelse
            </div>
            @if($branchBreakdown->count())
            <div class="box-footer">
                <div style="display:flex;justify-content:space-between;font-size:12px">
                    <span class="text-muted">{{ $branchBreakdown->count() }} branches</span>
                    <span><strong>Total: {{ number_format($branchBreakdown->sum('total_recovered'), 2) }}</strong>
                    across {{ $branchBreakdown->sum('case_count') }} cases</span>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════
     ROW 3 — SPECIALIST PERFORMANCE TABLE
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-users"></i> Specialist Performance</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/report/specialist_report') }}?period={{ $period }}"
                       class="btn btn-xs btn-default">Full Specialist Report</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped" style="margin-bottom:0">
                    <thead>
                        <tr>
                            <th>Specialist</th>
                            <th>Assigned Stream</th>
                            <th class="text-right">Recovered</th>
                            <th class="text-center">Active</th>
                            <th class="text-center">Resolved</th>
                            <th style="min-width:160px">vs Target</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bySpecialist as $row)
                        @php
                            $cat  = $row['category'] ?? 'escalated';
                            $meta = $catMeta[$cat] ?? $catMeta['escalated'];
                            [$bColor, $lColor] = match($row['status']) {
                                'exceeding' => ['progress-bar-success', 'label-success'],
                                'on_track'  => ['progress-bar-info',    'label-info'],
                                'at_risk'   => ['progress-bar-warning', 'label-warning'],
                                default     => ['progress-bar-danger',  'label-danger'],
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>
                                    {{ $row['specialist']->first_name }}
                                    {{ $row['specialist']->last_name }}
                                </strong>
                            </td>
                            <td>
                                <span class="label {{ $meta['label'] }}">
                                    {{ $categories[$cat] ?? ucwords(str_replace('_', ' ', $cat)) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <strong>{{ number_format($row['total_recovered'], 2) }}</strong>
                                @if($grandTotal > 0)
                                <br><small class="text-muted">{{ round(($row['total_recovered']/$grandTotal)*100,1) }}% of total</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue">{{ $row['active_cases'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-green">{{ $row['resolved_cases'] }}</span>
                            </td>
                            <td>
                                <div class="progress progress-xs" style="margin-bottom:4px">
                                    <div class="progress-bar {{ $bColor }}"
                                         style="width:{{ min($row['target_pct'],100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $row['target_pct'] }}% of target</small>
                            </td>
                            <td>
                                <span class="label {{ $lColor }}">
                                    {{ ucwords(str_replace('_',' ',$row['status'])) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:32px">
                                No specialist data for this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($bySpecialist->count())
                    <tfoot>
                        <tr class="active">
                            <th colspan="2">Totals</th>
                            <th class="text-right">{{ number_format($bySpecialist->sum('total_recovered'), 2) }}</th>
                            <th class="text-center">{{ $bySpecialist->sum('active_cases') }}</th>
                            <th class="text-center">{{ $bySpecialist->sum('resolved_cases') }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

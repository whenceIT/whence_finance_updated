@extends('layouts.master')

@section('title')
    Specialist Performance Report
@endsection

@section('content')

@php
$catMeta = [
    'cross_branch' => ['label' => 'label-primary', 'bar' => 'progress-bar-primary', 'icon' => 'fa-random'],
    'escalated'    => ['label' => 'label-warning',  'bar' => 'progress-bar-warning', 'icon' => 'fa-arrow-up'],
    'dormant'      => ['label' => 'label-default',  'bar' => 'progress-bar-default', 'icon' => 'fa-moon-o'],
    'legal'        => ['label' => 'label-danger',   'bar' => 'progress-bar-danger',  'icon' => 'fa-gavel'],
    'skip_trace'   => ['label' => 'label-success',  'bar' => 'progress-bar-success', 'icon' => 'fa-search'],
];
$totalRecovered = $specialists->sum('total_recovered');
$totalActive    = $specialists->sum('active_cases');
$totalResolved  = $specialists->sum('resolved_cases');
@endphp

{{-- ══════════════════════════════════════════════════════
     FILTER + EXPORT BAR
══════════════════════════════════════════════════════ --}}
<div class="box box-solid box-default">
    <div class="box-body" style="padding:12px 18px">
        <form method="GET" action="{{ url('recovery/report/specialist_report') }}"
              style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <i class="fa fa-filter text-muted"></i>
            <div class="form-group" style="margin:0">
                <select name="period" class="form-control input-sm">
                    @foreach(['month'=>'This Month','quarter'=>'This Quarter','year'=>'This Year'] as $p => $lbl)
                        <option value="{{ $p }}" {{ $period===$p?'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-search"></i> Run Report
            </button>
            <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                <span class="text-muted" style="font-size:12px;margin-right:4px">
                    <i class="fa fa-download"></i> Export:
                </span>
                <a href="{{ url('recovery/report/specialist_report/pdf') }}?period={{ $period }}"
                   class="btn btn-sm btn-danger" target="_blank">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
                <a href="{{ url('recovery/report/specialist_report/excel') }}?period={{ $period }}"
                   class="btn btn-sm btn-success">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 1 — SUMMARY KPI TILES
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Recovered</span>
                <span class="info-box-number">{{ number_format($totalRecovered, 2) }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">All specialists combined</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Specialists</span>
                <span class="info-box-number">{{ $specialists->count() }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">
                    {{ $specialists->where('status','exceeding')->count() }} exceeding target
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-folder-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Cases</span>
                <span class="info-box-number">{{ $totalActive }}</span>
                <div class="progress"><div class="progress-bar" style="width:{{ $totalActive + $totalResolved > 0 ? round(($totalActive/($totalActive+$totalResolved))*100) : 0 }}%"></div></div>
                <span class="progress-description">{{ $totalResolved }} resolved this period</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box {{ $specialists->where('status','at_risk')->count() + $specialists->where('status','behind')->count() > 0 ? 'bg-red' : 'bg-blue' }}">
            <span class="info-box-icon"><i class="fa fa-exclamation-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Needing Attention</span>
                <span class="info-box-number">
                    {{ $specialists->whereIn('status',['at_risk','behind'])->count() }}
                </span>
                <div class="progress"><div class="progress-bar" style="width:{{ $specialists->count() > 0 ? round(($specialists->whereIn('status',['at_risk','behind'])->count()/$specialists->count())*100) : 0 }}%"></div></div>
                <span class="progress-description">At risk or behind target</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 2 — STATUS SUMMARY MINI-BOXES
══════════════════════════════════════════════════════ --}}
@php
$statusGroups = [
    'exceeding' => ['Exceeding', 'bg-green',  'fa-trophy'],
    'on_track'  => ['On Track',  'bg-aqua',   'fa-check'],
    'at_risk'   => ['At Risk',   'bg-yellow', 'fa-warning'],
    'behind'    => ['Behind',    'bg-red',    'fa-times'],
];
@endphp
<div class="row">
    @foreach($statusGroups as $key => [$label, $bgClass, $icon])
    @php $cnt = $specialists->where('status',$key)->count(); @endphp
    <div class="col-md-3 col-sm-6">
        <div class="info-box {{ $bgClass }}" style="min-height:60px">
            <span class="info-box-icon" style="height:60px;line-height:60px;font-size:24px">
                <i class="fa {{ $icon }}"></i>
            </span>
            <div class="info-box-content" style="padding:10px 10px">
                <span class="info-box-text" style="font-size:11px">{{ $label }}</span>
                <span class="info-box-number" style="font-size:22px">{{ $cnt }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 3 — MAIN SPECIALIST TABLE
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-table"></i> Specialist Performance Detail</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped" style="margin-bottom:0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Specialist</th>
                            <th>Stream</th>
                            <th class="text-right">Recovered</th>
                            <th class="text-right">Share</th>
                            <th class="text-center">Active</th>
                            <th class="text-center">Resolved</th>
                            <th class="text-right">Target</th>
                            <th style="min-width:140px">Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialists->sortByDesc('total_recovered')->values() as $i => $row)
                        @php
                            $cat    = $row['category'] ?? 'escalated';
                            $meta   = $catMeta[$cat] ?? $catMeta['escalated'];
                            [$bColor, $lColor] = match($row['status']) {
                                'exceeding' => ['progress-bar-success', 'label-success'],
                                'on_track'  => ['progress-bar-info',    'label-info'],
                                'at_risk'   => ['progress-bar-warning', 'label-warning'],
                                default     => ['progress-bar-danger',  'label-danger'],
                            };
                            $share = $totalRecovered > 0
                                ? round(($row['total_recovered']/$totalRecovered)*100,1)
                                : 0;
                        @endphp
                        <tr>
                            <td class="text-muted"><small>{{ $i+1 }}</small></td>
                            <td>
                                <strong>
                                    {{ $row['specialist']->first_name }}
                                    {{ $row['specialist']->last_name }}
                                </strong>
                            </td>
                            <td>
                                <i class="fa {{ $meta['icon'] }} text-muted" style="width:14px"></i>
                                <span class="label {{ $meta['label'] }}">
                                    {{ $categories[$cat] ?? ucwords(str_replace('_', ' ', $cat)) }}
                                </span>
                            </td>
                            <td class="text-right"><strong>{{ number_format($row['total_recovered'], 2) }}</strong></td>
                            <td class="text-right"><small class="text-muted">{{ $share }}%</small></td>
                            <td class="text-center"><span class="badge bg-blue">{{ $row['active_cases'] }}</span></td>
                            <td class="text-center"><span class="badge bg-green">{{ $row['resolved_cases'] }}</span></td>
                            <td class="text-right">
                                @if($row['target_amount'] > 0)
                                    <small>{{ number_format($row['target_amount'], 0) }}</small>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
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
                            <td colspan="10" class="text-center text-muted" style="padding:40px">
                                No specialist data for this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($specialists->count())
                    <tfoot>
                        <tr class="active">
                            <th colspan="3">Totals</th>
                            <th class="text-right">{{ number_format($totalRecovered, 2) }}</th>
                            <th class="text-right">100%</th>
                            <th class="text-center">{{ $totalActive }}</th>
                            <th class="text-center">{{ $totalResolved }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

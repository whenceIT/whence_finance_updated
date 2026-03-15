@extends('layouts.master')

@section('title')
    Recovery Attribution Report
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

@php
$catMeta = [
    'cross_branch' => ['label' => 'label-primary', 'bar' => 'progress-bar-primary', 'icon' => 'fa-random'],
    'escalated'    => ['label' => 'label-warning',  'bar' => 'progress-bar-warning', 'icon' => 'fa-arrow-up'],
    'dormant'      => ['label' => 'label-default',  'bar' => 'progress-bar-default', 'icon' => 'fa-moon-o'],
    'legal'        => ['label' => 'label-danger',   'bar' => 'progress-bar-danger',  'icon' => 'fa-gavel'],
    'skip_trace'   => ['label' => 'label-success',  'bar' => 'progress-bar-success', 'icon' => 'fa-search'],
];
$grandAll   = $attributionData->sum('grand_total') ?: 1;
$deptAll    = $attributionData->sum('dept_total');
$originAll  = $attributionData->sum('origin_total');
$supAll     = $attributionData->sum('supporting_total');
@endphp

{{-- ══════════════════════════════════════════════════════
     FILTER + EXPORT BAR
══════════════════════════════════════════════════════ --}}
<div class="box box-solid box-default">
    <div class="box-body" style="padding:12px 18px">
        <form method="GET" action="{{ url('recovery/report/attribution') }}"
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
                <a href="{{ url('recovery/report/attribution/pdf') }}?period={{ $period }}"
                   class="btn btn-sm btn-danger" target="_blank">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
                <a href="{{ url('recovery/report/attribution/excel') }}?period={{ $period }}"
                   class="btn btn-sm btn-success">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 1 — ATTRIBUTION SUMMARY TILES
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Grand Total Recovered</span>
                <span class="info-box-number">{{ number_format($grandAll, 2) }}</span>
                <div class="progress"><div class="progress-bar" style="width:100%"></div></div>
                <span class="progress-description">Across all categories</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-university"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recoveries Dept Share</span>
                <span class="info-box-number">{{ number_format($deptAll, 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ round(($deptAll/$grandAll)*100) }}%"></div>
                </div>
                <span class="progress-description">{{ round(($deptAll/$grandAll)*100,1) }}% of total</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-code-fork"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Origin Branch Share</span>
                <span class="info-box-number">{{ number_format($originAll, 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ round(($originAll/$grandAll)*100) }}%"></div>
                </div>
                <span class="progress-description">{{ round(($originAll/$grandAll)*100,1) }}% of total</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-sitemap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Supporting Branch Share</span>
                <span class="info-box-number">{{ number_format($supAll, 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ round(($supAll/$grandAll)*100) }}%"></div>
                </div>
                <span class="progress-description">{{ round(($supAll/$grandAll)*100,1) }}% of total</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 2 — SHARE OVERVIEW BARS
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Attribution Share Overview</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="progress-group">
                            <span class="progress-text">
                                <i class="fa fa-university"></i> Recoveries Dept
                            </span>
                            <span class="progress-number">
                                <b>{{ number_format($deptAll, 2) }}</b> &nbsp;
                                <small class="text-muted">{{ round(($deptAll/$grandAll)*100,1) }}%</small>
                            </span>
                            <div class="progress sm">
                                <div class="progress-bar progress-bar-success"
                                     style="width:{{ round(($deptAll/$grandAll)*100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="progress-group">
                            <span class="progress-text">
                                <i class="fa fa-code-fork"></i> Origin Branch
                            </span>
                            <span class="progress-number">
                                <b>{{ number_format($originAll, 2) }}</b> &nbsp;
                                <small class="text-muted">{{ round(($originAll/$grandAll)*100,1) }}%</small>
                            </span>
                            <div class="progress sm">
                                <div class="progress-bar progress-bar-warning"
                                     style="width:{{ round(($originAll/$grandAll)*100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="progress-group">
                            <span class="progress-text">
                                <i class="fa fa-sitemap"></i> Supporting Branch
                            </span>
                            <span class="progress-number">
                                <b>{{ number_format($supAll, 2) }}</b> &nbsp;
                                <small class="text-muted">{{ round(($supAll/$grandAll)*100,1) }}%</small>
                            </span>
                            <div class="progress sm">
                                <div class="progress-bar progress-bar-info"
                                     style="width:{{ round(($supAll/$grandAll)*100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 3 — FULL ATTRIBUTION TABLE
══════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-table"></i> Attribution by Category</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped table-bordered" style="margin-bottom:0">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle">Category</th>
                            <th rowspan="2" class="text-right" style="vertical-align:middle">Grand Total</th>
                            <th colspan="2" class="text-center bg-green-active">Recoveries Dept</th>
                            <th colspan="2" class="text-center bg-yellow-active">Origin Branch</th>
                            <th colspan="2" class="text-center bg-blue-active">Supporting Branch</th>
                        </tr>
                        <tr>
                            <th class="text-right bg-green-active">Amount</th>
                            <th class="text-right bg-green-active">%</th>
                            <th class="text-right bg-yellow-active">Amount</th>
                            <th class="text-right bg-yellow-active">%</th>
                            <th class="text-right bg-blue-active">Amount</th>
                            <th class="text-right bg-blue-active">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributionData as $row)
                        @php
                            $gt   = $row->grand_total ?: 1;
                            $dPct = round(($row->dept_total / $gt) * 100, 1);
                            $oPct = round(($row->origin_total / $gt) * 100, 1);
                            $sPct = round(($row->supporting_total / $gt) * 100, 1);
                            $catName = $categories[$row->category]
                                       ?? ucwords(str_replace('_',' ',$row->category));
                            $meta = $catMeta[$row->category] ?? $catMeta['escalated'];
                        @endphp
                        <tr>
                            <td>
                                <i class="fa {{ $meta['icon'] }} text-muted" style="width:16px"></i>
                                <span class="label {{ $meta['label'] }}">{{ $catName }}</span>
                            </td>
                            <td class="text-right"><strong>{{ number_format($row->grand_total, 2) }}</strong></td>
                            <td class="text-right">{{ number_format($row->dept_total, 2) }}</td>
                            <td class="text-right">
                                <div class="progress progress-xs" style="margin-bottom:2px">
                                    <div class="progress-bar progress-bar-success" style="width:{{ $dPct }}%"></div>
                                </div>
                                <small class="text-muted">{{ $dPct }}%</small>
                            </td>
                            <td class="text-right">{{ number_format($row->origin_total, 2) }}</td>
                            <td class="text-right">
                                <div class="progress progress-xs" style="margin-bottom:2px">
                                    <div class="progress-bar progress-bar-warning" style="width:{{ $oPct }}%"></div>
                                </div>
                                <small class="text-muted">{{ $oPct }}%</small>
                            </td>
                            <td class="text-right">{{ number_format($row->supporting_total, 2) }}</td>
                            <td class="text-right">
                                <div class="progress progress-xs" style="margin-bottom:2px">
                                    <div class="progress-bar progress-bar-info" style="width:{{ $sPct }}%"></div>
                                </div>
                                <small class="text-muted">{{ $sPct }}%</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding:40px">
                                No attribution data available for this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($attributionData->count())
                    <tfoot>
                        <tr class="active">
                            <th>Totals</th>
                            <th class="text-right">{{ number_format($grandAll, 2) }}</th>
                            <th class="text-right">{{ number_format($deptAll, 2) }}</th>
                            <th class="text-right">{{ round(($deptAll/$grandAll)*100,1) }}%</th>
                            <th class="text-right">{{ number_format($originAll, 2) }}</th>
                            <th class="text-right">{{ round(($originAll/$grandAll)*100,1) }}%</th>
                            <th class="text-right">{{ number_format($supAll, 2) }}</th>
                            <th class="text-right">{{ round(($supAll/$grandAll)*100,1) }}%</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

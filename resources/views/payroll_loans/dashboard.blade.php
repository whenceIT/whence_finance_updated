@extends('layouts.master')

@section('title')
    Payroll Loan Portfolio Dashboard
@endsection

@section('content')

<section class="content-header">
    <h1>Payroll Loan Portfolio Dashboard</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Payroll Loans Dashboard</li>
    </ol>
</section>

<section class="content">

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 1: STATS CARDS
    ══════════════════════════════════════════════════════════════ --}}

    <div class="row">

        {{-- Total Loans --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Loans</span>
                    <span class="info-box-number">{{ number_format($stats['total_count']) }}</span>
                </div>
            </div>
        </div>

        {{-- Loan Portfolio (disbursed) --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Loan Portfolio (Active)</span>
                    <span class="info-box-number">{{ number_format($stats['loan_portfolio'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Total Given Out --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-arrow-circle-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Given Out</span>
                    <span class="info-box-number">{{ number_format($stats['total_given_out'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Expected Interest --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fa fa-percent"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Expected Interest (40%)</span>
                    <span class="info-box-number">{{ number_format($stats['expected_interest'], 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        {{-- Expected Collections --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-calendar-check-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Expected Collections</span>
                    <span class="info-box-number">{{ number_format($stats['expected_collections'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Total Collections --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Collections</span>
                    <span class="info-box-number">{{ number_format($stats['total_collections'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Total Uncollected --}}
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Uncollected</span>
                    <span class="info-box-number">{{ number_format($stats['total_uncollected'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Collection Rate --}}
        <div class="col-md-3 col-sm-6">
            @php
                $rate = $stats['expected_collections'] > 0
                    ? round(($stats['total_collections'] / $stats['expected_collections']) * 100, 1)
                    : 0;
                $rateColor = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
            @endphp
            <div class="info-box">
                <span class="info-box-icon bg-{{ $rateColor }}"><i class="fa fa-bar-chart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Collection Rate</span>
                    <span class="info-box-number">{{ $rate }}%</span>
                    <div class="progress">
                        <div class="progress-bar progress-bar-{{ $rateColor }}" style="width: {{ $rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 2: LOAN CONSULTANT PERFORMANCE
    ══════════════════════════════════════════════════════════════ --}}

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-users"></i> Loan Consultant Performance</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="consultant-table">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Consultant</th>
                                    <th class="text-right">Loans</th>
                                    <th class="text-right">Principal</th>
                                    <th class="text-right">Exp. Interest</th>
                                    <th class="text-right">Exp. Collections</th>
                                    <th class="text-right">Collections</th>
                                    <th class="text-right">Uncollected</th>
                                    <th class="text-center">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($consultants as $i => $c)
                                @php
                                    $cRate = $c->expected_collections > 0
                                        ? round(($c->collections / $c->expected_collections) * 100, 1)
                                        : 0;
                                    $cClass = $cRate >= 80 ? 'success' : ($cRate >= 50 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $c->consultant_name }}</td>
                                    <td class="text-right">{{ number_format($c->loans) }}</td>
                                    <td class="text-right">{{ number_format($c->total_principal, 2) }}</td>
                                    <td class="text-right">{{ number_format($c->expected_interest, 2) }}</td>
                                    <td class="text-right">{{ number_format($c->expected_collections, 2) }}</td>
                                    <td class="text-right">{{ number_format($c->collections, 2) }}</td>
                                    <td class="text-right {{ $c->uncollected > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($c->uncollected, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-{{ $cClass }}">{{ $cRate }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted">No consultant data found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 3: PROVINCE → OFFICE → CONSULTANT DRILLDOWN
    ══════════════════════════════════════════════════════════════ --}}

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-sitemap"></i> Province &rsaquo; Office &rsaquo; Consultant Drilldown</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-xs btn-default" onclick="expandAll()"><i class="fa fa-plus-square"></i> Expand All</button>
                        <button class="btn btn-xs btn-default" onclick="collapseAll()"><i class="fa fa-minus-square"></i> Collapse All</button>
                    </div>
                </div>
                <div class="box-body no-padding">

                    @forelse($drilldown as $provinceName => $offices)
                        @php
                            // Province totals
                            $pLoans   = $offices->flatten()->sum('loans');
                            $pExpCol  = $offices->flatten()->sum('expected_collections');
                            $pCol     = $offices->flatten()->sum('collections');
                            $pUnc     = $offices->flatten()->sum('uncollected');
                            $pExpInt  = $offices->flatten()->sum('expected_interest');
                            $pRate    = $pExpCol > 0 ? round(($pCol / $pExpCol) * 100, 1) : 0;
                            $pClass   = $pRate >= 80 ? 'success' : ($pRate >= 50 ? 'warning' : 'danger');
                            $pId      = 'prov-' . Str::slug($provinceName);
                        @endphp

                        {{-- Province Header Row --}}
                        <div class="panel panel-default" style="margin-bottom:0; border-radius:0; border-left:4px solid #00a65a;">
                            <div class="panel-heading" style="cursor:pointer; background:#f9f9f9; padding:10px 15px;"
                                 onclick="toggleSection('{{ $pId }}')">
                                <strong style="font-size:14px;">
                                    <i class="fa fa-map-marker text-success"></i>
                                    &nbsp;{{ $provinceName }}
                                </strong>
                                <span class="pull-right">
                                    <span class="label label-default">{{ number_format($pLoans) }} loans</span>
                                    &nbsp;
                                    <span class="text-muted" style="font-size:12px;">
                                        Exp: {{ number_format($pExpCol, 2) }} |
                                        Collected: {{ number_format($pCol, 2) }} |
                                        Uncollected: {{ number_format($pUnc, 2) }}
                                    </span>
                                    &nbsp;
                                    <span class="label label-{{ $pClass }}">{{ $pRate }}%</span>
                                    &nbsp;
                                    <i class="fa fa-chevron-down toggle-icon"></i>
                                </span>
                            </div>

                            <div id="{{ $pId }}" style="display:none;">

                                @foreach($offices as $officeName => $consultantRows)
                                    @php
                                        $oLoans  = $consultantRows->sum('loans');
                                        $oExpCol = $consultantRows->sum('expected_collections');
                                        $oCol    = $consultantRows->sum('collections');
                                        $oUnc    = $consultantRows->sum('uncollected');
                                        $oExpInt = $consultantRows->sum('expected_interest');
                                        $oRate   = $oExpCol > 0 ? round(($oCol / $oExpCol) * 100, 1) : 0;
                                        $oClass  = $oRate >= 80 ? 'success' : ($oRate >= 50 ? 'warning' : 'danger');
                                        $oId     = 'off-' . Str::slug($provinceName . '-' . $officeName);
                                    @endphp

                                    {{-- Office Row --}}
                                    <div style="border-left:4px solid #3c8dbc; margin:0 0 0 20px; background:#fff;">
                                        <div style="padding:8px 15px; cursor:pointer; border-bottom:1px solid #f0f0f0;"
                                             onclick="toggleSection('{{ $oId }}')">
                                            <i class="fa fa-building-o text-info"></i>
                                            &nbsp;<strong>{{ $officeName }}</strong>
                                            <span class="pull-right">
                                                <span class="label label-default">{{ number_format($oLoans) }} loans</span>
                                                &nbsp;
                                                <span class="text-muted" style="font-size:12px;">
                                                    Exp: {{ number_format($oExpCol, 2) }} |
                                                    Collected: {{ number_format($oCol, 2) }} |
                                                    Uncollected: {{ number_format($oUnc, 2) }}
                                                </span>
                                                &nbsp;
                                                <span class="label label-{{ $oClass }}">{{ $oRate }}%</span>
                                                &nbsp;
                                                <i class="fa fa-chevron-down toggle-icon"></i>
                                            </span>
                                        </div>

                                        {{-- Consultant rows for this office --}}
                                        <div id="{{ $oId }}" style="display:none; padding:0 0 0 20px;">
                                            <table class="table table-condensed table-bordered" style="margin-bottom:0; background:#fafffe;">
                                                <thead>
                                                    <tr style="background:#e8f5e9; font-size:12px;">
                                                        <th>Consultant</th>
                                                        <th class="text-right">Loans</th>
                                                        <th class="text-right">Exp. Interest</th>
                                                        <th class="text-right">Exp. Collections</th>
                                                        <th class="text-right">Collections</th>
                                                        <th class="text-right">Uncollected</th>
                                                        <th class="text-center">Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($consultantRows as $row)
                                                    @php
                                                        $rRate  = $row->expected_collections > 0
                                                            ? round(($row->collections / $row->expected_collections) * 100, 1)
                                                            : 0;
                                                        $rClass = $rRate >= 80 ? 'success' : ($rRate >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <tr style="font-size:12px;">
                                                        <td><i class="fa fa-user text-muted"></i> {{ $row->consultant_name }}</td>
                                                        <td class="text-right">{{ number_format($row->loans) }}</td>
                                                        <td class="text-right">{{ number_format($row->expected_interest, 2) }}</td>
                                                        <td class="text-right">{{ number_format($row->expected_collections, 2) }}</td>
                                                        <td class="text-right">{{ number_format($row->collections, 2) }}</td>
                                                        <td class="text-right {{ $row->uncollected > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($row->uncollected, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="label label-{{ $rClass }}">{{ $rRate }}%</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr style="background:#e8f5e9; font-weight:bold; font-size:12px;">
                                                        <td>Office Total</td>
                                                        <td class="text-right">{{ number_format($oLoans) }}</td>
                                                        <td class="text-right">{{ number_format($oExpInt, 2) }}</td>
                                                        <td class="text-right">{{ number_format($oExpCol, 2) }}</td>
                                                        <td class="text-right">{{ number_format($oCol, 2) }}</td>
                                                        <td class="text-right {{ $oUnc > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($oUnc, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="label label-{{ $oClass }}">{{ $oRate }}%</span>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                    </div>{{-- /office --}}

                                @endforeach

                                {{-- Province total footer --}}
                                <div style="padding:8px 15px; background:#e9f7ef; border-top:2px solid #00a65a; font-weight:bold; font-size:13px;">
                                    <span>Province Total</span>
                                    <span class="pull-right">
                                        <span class="label label-default">{{ number_format($pLoans) }} loans</span>
                                        &nbsp;
                                        <span>Exp: {{ number_format($pExpCol, 2) }} |
                                        Collected: {{ number_format($pCol, 2) }} |
                                        Uncollected: <span class="{{ $pUnc > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($pUnc, 2) }}</span></span>
                                        &nbsp;
                                        <span class="label label-{{ $pClass }}">{{ $pRate }}%</span>
                                    </span>
                                </div>

                            </div>{{-- /province collapse --}}
                        </div>{{-- /province panel --}}

                    @empty
                        <p class="text-center text-muted" style="padding:20px;">No data found.</p>
                    @endforelse

                </div>{{-- /box-body --}}
            </div>{{-- /box --}}
        </div>
    </div>

</section>

@endsection

@section('scripts')
<script>
function toggleSection(id) {
    var el = document.getElementById(id);
    if (!el) return;
    var isVisible = el.style.display !== 'none';
    el.style.display = isVisible ? 'none' : 'block';

    // flip chevron on the parent heading
    var heading = el.previousElementSibling;
    if (heading) {
        var icon = heading.querySelector('.toggle-icon');
        if (icon) {
            icon.classList.toggle('fa-chevron-down', isVisible);
            icon.classList.toggle('fa-chevron-up', !isVisible);
        }
    }
}

function expandAll() {
    document.querySelectorAll('[id^="prov-"], [id^="off-"]').forEach(function(el) {
        el.style.display = 'block';
    });
    document.querySelectorAll('.toggle-icon').forEach(function(i) {
        i.classList.remove('fa-chevron-down');
        i.classList.add('fa-chevron-up');
    });
}

function collapseAll() {
    document.querySelectorAll('[id^="prov-"], [id^="off-"]').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.toggle-icon').forEach(function(i) {
        i.classList.remove('fa-chevron-up');
        i.classList.add('fa-chevron-down');
    });
}
</script>
@endsection

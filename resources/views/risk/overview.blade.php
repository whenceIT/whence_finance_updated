@extends('layouts.master')

@section('title')
    Risk Management Overview
@endsection

@push('styles')
<style>
/* ── Audit checklist pass/fail toggle icons ─────────────────── */
.audit-radio-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 22px;
    line-height: 1;
    user-select: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    transition: background .15s ease;
}
/* Hide the real radio */
.audit-radio-wrap input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
}
/* Default icon state — grey */
.audit-radio-wrap .audit-icon {
    color: #ccc;
    transition: color .15s ease, transform .15s ease;
}
/* Pass: green tick when checked */
.audit-radio-wrap.pass-wrap.is-checked .audit-icon {
    color: #27ae60;
    transform: scale(1.15);
}
/* Fail: red X icon when checked */
.audit-radio-wrap.fail-wrap.is-checked .audit-icon {
    color: #fff;
    transform: scale(1.15);
}
/* Fail cell background turns red when checked */
td.fail-cell-active {
    background-color: #c0392b !important;
    transition: background-color .2s ease;
}
/* Row highlight when failed */
tr.row-failed > td {
    background-color: #fdf2f2 !important;
}
tr.row-failed > td.fail-cell-active {
    background-color: #c0392b !important;
}
/* Hover feedback */
.audit-radio-wrap:hover .audit-icon {
    opacity: .7;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Risk Management Dashboard Overview</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#auditChecklistModal">
                        <i class="fa fa-clipboard"></i> Start Audit Checklist
                    </button>
                </div>
            </div>
            <div class="box-body">
                <p>Welcome to the Risk Management Overview. This page provides a high-level summary of all risk-related activities and metrics.</p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Critical Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">High Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-info-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Moderate Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Low Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h3 class="box-title">Risk Trends</h3>
                            </div>
                            <div class="box-body">
                                <p>Chart placeholder for risk trends over time.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     BRANCH AUDIT RESULTS OVERVIEW
     ============================================================ -->
@php
/*
 * Sample data — replace with DB query once audit submissions are persisted.
 * Shape: [ branch_name, last_audit, auditor, rating, fail_count, sections[] ]
 * Each section: [ name, short, pass, fail, na ]
 */
$sectionShorts = ['Admin','Wallet','Loans','Collections','Fraud','Staff','Systems','Reporting','Conclusion'];

$branches = [
    [
        'name'       => 'Lusaka Central',
        'code'       => 'LCA-001',
        'last_audit' => '30 Apr 2026',
        'auditor'    => 'M. Banda',
        'fail_count' => 2,
        'rating'     => 'low',
        'sections'   => [
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>9,'fail'=>1,'na'=>0],
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>5,'fail'=>1,'na'=>1],
            ['pass'=>5,'fail'=>0,'na'=>0],
        ],
    ],
    [
        'name'       => 'Kitwe Branch',
        'code'       => 'KTW-002',
        'last_audit' => '28 Apr 2026',
        'auditor'    => 'P. Mwale',
        'fail_count' => 6,
        'rating'     => 'medium',
        'sections'   => [
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>7,'fail'=>3,'na'=>0],
            ['pass'=>5,'fail'=>2,'na'=>0],
            ['pass'=>5,'fail'=>1,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>1],
            ['pass'=>5,'fail'=>0,'na'=>1],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>5,'fail'=>0,'na'=>0],
        ],
    ],
    [
        'name'       => 'Ndola Office',
        'code'       => 'NDL-003',
        'last_audit' => '25 Apr 2026',
        'auditor'    => 'C. Phiri',
        'fail_count' => 10,
        'rating'     => 'high',
        'sections'   => [
            ['pass'=>6,'fail'=>1,'na'=>0],
            ['pass'=>6,'fail'=>4,'na'=>0],
            ['pass'=>4,'fail'=>3,'na'=>0],
            ['pass'=>4,'fail'=>2,'na'=>0],
            ['pass'=>4,'fail'=>2,'na'=>0],
            ['pass'=>5,'fail'=>2,'na'=>0],
            ['pass'=>4,'fail'=>2,'na'=>0],
            ['pass'=>5,'fail'=>1,'na'=>0],
            ['pass'=>4,'fail'=>1,'na'=>0],
        ],
    ],
    [
        'name'       => 'Livingstone Branch',
        'code'       => 'LVS-004',
        'last_audit' => '22 Apr 2026',
        'auditor'    => 'R. Tembo',
        'fail_count' => 15,
        'rating'     => 'critical',
        'sections'   => [
            ['pass'=>5,'fail'=>2,'na'=>0],
            ['pass'=>4,'fail'=>6,'na'=>0],
            ['pass'=>3,'fail'=>4,'na'=>0],
            ['pass'=>2,'fail'=>4,'na'=>0],
            ['pass'=>2,'fail'=>4,'na'=>0],
            ['pass'=>3,'fail'=>4,'na'=>0],
            ['pass'=>3,'fail'=>3,'na'=>0],
            ['pass'=>3,'fail'=>3,'na'=>0],
            ['pass'=>3,'fail'=>2,'na'=>0],
        ],
    ],
    [
        'name'       => 'Chipata Office',
        'code'       => 'CHP-005',
        'last_audit' => '20 Apr 2026',
        'auditor'    => 'N. Zulu',
        'fail_count' => 0,
        'rating'     => 'low',
        'sections'   => [
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>10,'fail'=>0,'na'=>0],
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>5,'fail'=>0,'na'=>0],
        ],
    ],
    [
        'name'       => 'Kabwe Branch',
        'code'       => 'KBW-006',
        'last_audit' => '18 Apr 2026',
        'auditor'    => 'T. Mulenga',
        'fail_count' => 5,
        'rating'     => 'medium',
        'sections'   => [
            ['pass'=>7,'fail'=>0,'na'=>0],
            ['pass'=>8,'fail'=>2,'na'=>0],
            ['pass'=>6,'fail'=>1,'na'=>0],
            ['pass'=>5,'fail'=>1,'na'=>0],
            ['pass'=>5,'fail'=>1,'na'=>0],
            ['pass'=>6,'fail'=>1,'na'=>0],
            ['pass'=>5,'fail'=>0,'na'=>1],
            ['pass'=>6,'fail'=>0,'na'=>0],
            ['pass'=>5,'fail'=>0,'na'=>0],
        ],
    ],
];

$ratingConfig = [
    'low'      => ['label'=>'🟢 LOW',      'color'=>'#27ae60','bg'=>'#eafaf1','badge'=>'success'],
    'medium'   => ['label'=>'🟡 MEDIUM',   'color'=>'#f39c12','bg'=>'#fef9e7','badge'=>'warning'],
    'high'     => ['label'=>'🔴 HIGH',     'color'=>'#e74c3c','bg'=>'#fdedec','badge'=>'danger'],
    'critical' => ['label'=>'🚨 CRITICAL', 'color'=>'#7b241c','bg'=>'#f9ebea','badge'=>'danger'],
];
@endphp

<div class="row" style="margin-top:20px;">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-building-o"></i>&nbsp; Branch Audit Results — All Offices</h3>
                <div class="box-tools pull-right">
                    <span class="label label-default" style="font-size:12px;">{{ count($branches) }} branches</span>
                    &nbsp;
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body" style="padding:16px;">

                {{-- Summary strip --}}
                @php
                    $counts = ['low'=>0,'medium'=>0,'high'=>0,'critical'=>0];
                    foreach($branches as $b) $counts[$b['rating']]++;
                @endphp
                <div class="row" style="margin-bottom:20px;">
                    @foreach(['low'=>['bg-green','fa-check-circle'],'medium'=>['bg-yellow','fa-exclamation-circle'],'high'=>['bg-red','fa-times-circle'],'critical'=>['bg-red','fa-exclamation-triangle']] as $r=>$cfg)
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box" style="min-height:60px;">
                            <span class="info-box-icon {{ $cfg[0] }}" style="font-size:28px;line-height:60px;width:60px;height:60px;">
                                <i class="fa {{ $cfg[1] }}"></i>
                            </span>
                            <div class="info-box-content" style="padding:8px 10px;">
                                <span class="info-box-text" style="font-size:12px;">{{ strtoupper($r) }}</span>
                                <span class="info-box-number" style="font-size:24px;">{{ $counts[$r] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Branch cards --}}
                <div class="row">
                    @foreach($branches as $branch)
                    @php $rc = $ratingConfig[$branch['rating']]; @endphp
                    <div class="col-md-6 col-lg-4" style="margin-bottom:20px;">
                        <div class="box box-solid" style="border-top:3px solid {{ $rc['color'] }};margin-bottom:0;">

                            {{-- Card header --}}
                            <div class="box-header" style="background:{{ $rc['bg'] }};padding:10px 14px;">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                                    <div>
                                        <h4 style="margin:0;font-size:15px;color:#333;">
                                            <i class="fa fa-map-marker" style="color:{{ $rc['color'] }};"></i>
                                            &nbsp;{{ $branch['name'] }}
                                        </h4>
                                        <small class="text-muted">{{ $branch['code'] }}</small>
                                    </div>
                                    <span class="label label-{{ $rc['badge'] }}" style="font-size:11px;padding:4px 8px;background:{{ $rc['color'] }};">
                                        {{ $rc['label'] }}
                                    </span>
                                </div>
                                <div style="margin-top:8px;font-size:12px;color:#555;">
                                    <i class="fa fa-calendar-o"></i> {{ $branch['last_audit'] }}
                                    &nbsp;&nbsp;
                                    <i class="fa fa-user-o"></i> {{ $branch['auditor'] }}
                                    &nbsp;&nbsp;
                                    <strong style="color:{{ $rc['color'] }};">
                                        <i class="fa fa-times-circle"></i> {{ $branch['fail_count'] }} fail{{ $branch['fail_count'] !== 1 ? 's' : '' }}
                                    </strong>
                                </div>
                            </div>

                            {{-- Section breakdown --}}
                            <div class="box-body" style="padding:12px 14px;">
                                <table style="width:100%;font-size:11px;border-collapse:collapse;">
                                    <thead>
                                        <tr style="color:#888;border-bottom:1px solid #eee;">
                                            <th style="padding:3px 4px;text-align:left;font-weight:600;">Section</th>
                                            <th style="padding:3px 4px;text-align:center;color:#27ae60;">✓</th>
                                            <th style="padding:3px 4px;text-align:center;color:#c0392b;">✗</th>
                                            <th style="padding:3px 4px;text-align:center;color:#aaa;">N/A</th>
                                            <th style="padding:3px 4px;text-align:left;">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sectionShorts as $si => $sname)
                                        @php
                                            $sec   = $branch['sections'][$si];
                                            $total = $sec['pass'] + $sec['fail'] + $sec['na'];
                                            $pct   = $total > 0 ? round(($sec['pass'] / $total) * 100) : 0;
                                            $barColor = $sec['fail'] === 0 ? '#27ae60' : ($sec['fail'] <= 1 ? '#f39c12' : '#c0392b');
                                        @endphp
                                        <tr style="border-bottom:1px solid #f5f5f5;">
                                            <td style="padding:4px 4px;color:#444;">{{ $sname }}</td>
                                            <td style="padding:4px 4px;text-align:center;color:#27ae60;font-weight:bold;">{{ $sec['pass'] }}</td>
                                            <td style="padding:4px 4px;text-align:center;{{ $sec['fail'] > 0 ? 'color:#c0392b;font-weight:bold;' : 'color:#ccc;' }}">
                                                {{ $sec['fail'] > 0 ? $sec['fail'] : '—' }}
                                            </td>
                                            <td style="padding:4px 4px;text-align:center;color:#aaa;">{{ $sec['na'] > 0 ? $sec['na'] : '—' }}</td>
                                            <td style="padding:4px 4px;min-width:70px;">
                                                <div style="background:#eee;border-radius:3px;height:6px;overflow:hidden;">
                                                    <div style="width:{{ $pct }}%;background:{{ $barColor }};height:6px;border-radius:3px;transition:width .3s;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Card footer --}}
                            <div class="box-footer" style="padding:8px 14px;background:#fafafa;border-top:1px solid #f0f0f0;text-align:right;">
                                <a href="#" class="btn btn-xs btn-default">
                                    <i class="fa fa-eye"></i> View Full Report
                                </a>
                                <a href="#" class="btn btn-xs btn-danger" style="margin-left:4px;">
                                    <i class="fa fa-clipboard"></i> Re-Audit
                                </a>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>{{-- /.row cards --}}

            </div>{{-- /.box-body --}}
        </div>{{-- /.box --}}
    </div>
</div>

@include('risk.partials.audit-checklist-modal')

@include('risk.partials.audit-checklist-scripts')

@endsection

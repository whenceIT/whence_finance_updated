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
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('success') }}
    </div>
@endif
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
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     BRANCH AUDIT RESULTS OVERVIEW
     ============================================================ -->
@php
$sectionShorts = ['Admin','Wallet','Loans','Collections','Fraud','Staff','Systems','Reporting','Conclusion'];

$submissions = \App\Models\AuditSubmission::with('office', 'auditor')->latest()->take(20)->get();

$branches = $submissions->map(function($sub) use ($sectionShorts) {
    $sections = [];
    $failCount = 0;
    $sectionItemCounts = [
        0 => 10, // s2 has 10 items
        1 => 7,  // s3
        2 => 2,  // s4
        3 => 7,  // s5
        4 => 8,  // s6
        5 => 8,  // s7
        6 => 6,  // s8
        7 => 2,  // s9
    ];
    foreach ($sectionShorts as $i => $short) {
        $s = $i + 2;
        $pass = 0;
        $fail = 0;
        $na = 0;
        $itemCount = $sectionItemCounts[$i] ?? 0;
        for ($j = 1; $j <= $itemCount; $j++) {
            $field = "s{$s}_{$j}";
            $value = $sub->$field;
            if ($value === 'pass') $pass++;
            elseif ($value === 'fail') $fail++;
            elseif ($value === 'na') $na++;
        }
        $sections[] = ['pass' => $pass, 'fail' => $fail, 'na' => $na];
        $failCount += $fail;
    }
    return [
        'submission_id' => $sub->id,
        'name' => $sub->office->name ?? 'Unknown',
        'code' => $sub->office->external_id ?? '',
        'last_audit' => $sub->created_at->format('d M Y'),
        'auditor' => $sub->auditor_name,
        'fail_count' => $failCount,
        'rating' => $sub->risk_rating,
        'sections' => $sections,
    ];
})->toArray();

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
                                            <td style="padding:4px 4px;color:#444;cursor:pointer;" onclick="loadSectionDetails({{ $branch['submission_id'] }}, {{ $si + 2 }}, '{{ $sname }}')">{{ $sname }}</td>
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

<!-- Modal for section details -->
<div class="modal fade" id="sectionDetailsModal" tabindex="-1" role="dialog" aria-labelledby="sectionDetailsModalLabel">
    <div class="modal-dialog" role="document" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="sectionDetailsModalLabel">Section Details</h4>
            </div>
            <div class="modal-body">
                <div id="sectionDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadSectionDetails(submissionId, section, sectionName) {
    fetch('/risk/audit-section-details/' + submissionId + '/' + section)
        .then(response => response.json())
        .then(data => {
            let content = '<h4>' + sectionName + '</h4><ul class="list-group">';
            data.forEach(item => {
                let statusIcon = '';
                if (item.status === 'pass') statusIcon = '<i class="fa fa-check text-success"></i>';
                else if (item.status === 'fail') statusIcon = '<i class="fa fa-times text-danger"></i>';
                else if (item.status === 'na') statusIcon = '<i class="fa fa-minus text-muted"></i>';
                content += '<li class="list-group-item">' + statusIcon + ' ' + item.label;
                if (item.notes) content += '<br><small class="text-muted">Notes: ' + item.notes + '</small>';
                content += '</li>';
            });
            content += '</ul>';
            document.getElementById('sectionDetailsContent').innerHTML = content;
            document.getElementById('sectionDetailsModalLabel').textContent = sectionName + ' Details';
            $('#sectionDetailsModal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}
</script>

@endsection

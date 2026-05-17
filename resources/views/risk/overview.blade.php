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
        tr.row-failed>td {
            background-color: #fdf2f2 !important;
        }

        tr.row-failed>td.fail-cell-active {
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
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                            data-target="#auditChecklistModal">
                            <i class="fa fa-clipboard"></i> Start Audit Checklist
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <p>Welcome to the Risk Management Overview. This page provides a high-level summary of all risk-related
                        activities and metrics.</p>
                </div>
            </div>
        </div>
    </div>


    <!-- ============================================================
         BRANCH AUDIT RESULTS OVERVIEW
         ============================================================ -->
    @php
        $sectionShorts = ['Admin', 'Wallet', 'Loans', 'Collections', 'Fraud', 'Staff', 'Systems', 'Reporting', 'Conclusion'];

        $sectionItemCounts = [
            0 => 0,  // Admin — metadata only
            1 => 10, // Wallet (s2)
            2 => 7,  // Loans (s3)
            3 => 6,  // Collections (s4)
            4 => 6,  // Fraud (s5)
            5 => 7,  // Staff (s6)
            6 => 8,  // Systems (s7)
            7 => 6,  // Reporting (s8)
            8 => 2,  // Conclusion (s9)
        ];

        $today = \Carbon\Carbon::today();
        $submissions = \App\Models\AuditSubmission::with('office', 'auditor')->latest()->take(50)->get();

        $branches = $submissions->map(function ($sub) use ($sectionShorts, $sectionItemCounts, $today) {
            $sections = [];
            $failCount = 0;
            foreach ($sectionShorts as $i => $short) {
                // Skip Admin section (index 0) as it has no pass/fail/na items
                if ($i === 0) {
                    $sections[] = ['pass' => 0, 'fail' => 0, 'na' => 0];
                    continue;
                }
                
                // Map section index to database field prefix
                // $i=1 (Wallet) -> s2, $i=2 (Loans) -> s3, etc.
                $s = $i + 1;
                $pass = 0;
                $fail = 0;
                $na = 0;
                $itemCount = $sectionItemCounts[$i] ?? 0;
                
                for ($j = 1; $j <= $itemCount; $j++) {
                    $field = "s{$s}_{$j}";
                    $value = $sub->$field;
                    
                    // Section 5 (Fraud) uses 'present'/'not_present' instead of 'pass'/'fail'
                    if ($i === 4) {
                        if ($value === 'not_present')
                            $pass++;
                        elseif ($value === 'present')
                            $fail++;
                    } else {
                        if ($value === 'pass')
                            $pass++;
                        elseif ($value === 'fail')
                            $fail++;
                        elseif ($value === 'na')
                            $na++;
                    }
                }
                
                // Log the final counts for this section
                if ($i === 4) {
                    \Log::info('Fraud section final counts', [
                        'section_index' => $i,
                        'pass' => $pass,
                        'fail' => $fail,
                        'na' => $na,
                        'submission_id' => $sub->id
                    ]);
                }
                
                $sections[] = ['pass' => $pass, 'fail' => $fail, 'na' => $na];
                $failCount += $fail;
            }

            $ratingKey = trim((string) ($sub->risk_rating ?? '')) ?: 'pending';
            $scheduled = $sub->audit_date && $sub->audit_date->gt($today);
            $complete = !$scheduled && $ratingKey !== 'pending';

            return [
                'submission_id' => $sub->id,
                'name' => $sub->office->name ?? 'Unknown',
                'code' => $sub->office->external_id ?? '',
                'audit_date' => $sub->audit_date ? $sub->audit_date->format('d M Y') : 'Unknown',
                'audit_date_human' => $sub->audit_date ? $sub->audit_date->diffForHumans() : 'Unknown',
                'last_audit' => $sub->created_at->format('d M Y'),
                'created_at_human' => $sub->created_at->diffForHumans(),
                'auditor' => $sub->auditor_name,
                'audit_type' => $sub->audit_type,
                'opening_remarks' => $sub->opening_remarks,
                'unannounced' => $sub->unannounced,
                'fail_count' => $failCount,
                'rating' => $ratingKey,
                'is_complete' => $complete,
                'is_scheduled' => $scheduled,
                'sections' => $sections,
            ];
        })->toArray();

        $completeAudits = array_values(array_filter($branches, fn($b) => $b['is_complete'] && !$b['is_scheduled']));
        $scheduledAudits = array_values(array_filter($branches, fn($b) => $b['is_scheduled']));
        $incompleteAudits = array_values(array_filter($branches, fn($b) => !$b['is_complete'] && !$b['is_scheduled']));

        // Get offices that have never been audited
        $auditedOfficeIds = $submissions->pluck('office_id')->unique()->toArray();
        $unauditedOffices = \App\Models\Office::with(['province', 'district', 'manager'])
            ->whereNotIn('id', $auditedOfficeIds)
            ->where('active', 1)
            ->orderBy('name')
            ->get();

        $ratingConfig = [
            'low' => ['label' => '🟢 LOW', 'color' => '#27ae60', 'bg' => '#eafaf1', 'badge' => 'success'],
            'medium' => ['label' => '🟡 MEDIUM', 'color' => '#f39c12', 'bg' => '#fef9e7', 'badge' => 'warning'],
            'high' => ['label' => '🔴 HIGH', 'color' => '#e74c3c', 'bg' => '#fdedec', 'badge' => 'danger'],
            'critical' => ['label' => '🚨 CRITICAL', 'color' => '#7b241c', 'bg' => '#f9ebea', 'badge' => 'danger'],
            'pending' => ['label' => '⚪ PENDING', 'color' => '#7f8c8d', 'bg' => '#f3f3f3', 'badge' => 'default'],
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
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="padding:16px;">

                    {{-- Summary strip --}}
                    @php
                        $counts = ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0, 'pending' => 0];
                        foreach ($branches as $b) {
                            $counts[$b['rating']] = ($counts[$b['rating']] ?? 0) + 1;
                        }
                    @endphp
                    <div class="row" style="margin-bottom:20px;">
                        @foreach(['low' => ['bg-green', 'fa-check-circle'], 'medium' => ['bg-yellow', 'fa-exclamation-circle'], 'high' => ['bg-red', 'fa-times-circle'], 'critical' => ['bg-red', 'fa-exclamation-triangle']] as $r => $cfg)
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box" style="min-height:60px;">
                                    <span class="info-box-icon {{ $cfg[0] }}"
                                        style="font-size:28px;line-height:60px;width:60px;height:60px;">
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
                    <div class="nav-tabs-custom" style="margin-bottom:20px;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab-complete" data-toggle="tab">Complete Audits
                                    ({{ count($completeAudits) }})</a></li>
                            <li><a href="#tab-incomplete" data-toggle="tab">Incomplete Audits
                                    ({{ count($incompleteAudits) }})</a></li>
                            <li><a href="#tab-scheduled" data-toggle="tab">Scheduled Audits
                                    ({{ count($scheduledAudits) }})</a></li>
                            <li><a href="#tab-unaudited" data-toggle="tab">Un-audited Offices
                                    ({{ count($unauditedOffices) }})</a></li>
                        </ul>
                        <div class="tab-content" style="padding:15px;border:1px solid #ddd;border-top:none;">
                            <div class="tab-pane active" id="tab-complete">
                                @include('risk.partials.tab-complete-audits')
                            </div>
                            <div class="tab-pane" id="tab-incomplete">
                                @include('risk.partials.tab-incomplete-audits')
                            </div>
                            <div class="tab-pane" id="tab-scheduled">
                                @include('risk.partials.tab-scheduled-audits')
                            </div>
                            <div class="tab-pane" id="tab-unaudited">
                                @include('risk.partials.tab-unaudited-offices')
                            </div>
                        </div>
                    </div>
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
        function preSelectOffice(officeId) {
            // Wait for modal to be shown, then set the office dropdown
            $('#auditChecklistModal').on('shown.bs.modal', function () {
                $('#s1_office_id').val(officeId).trigger('change');
            });
        }

        function confirmDeleteAudit(submissionId, branchName) {
            if (confirm('Are you sure you want to delete the incomplete audit for ' + branchName + '? This action cannot be undone.')) {
                deleteAudit(submissionId);
            }
        }

        function deleteAudit(submissionId) {
            fetch('/risk/audit-submission/' + submissionId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Audit deleted successfully.');
                    location.reload();
                } else {
                    alert('Error deleting audit: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting audit. Please try again.');
            });
        }

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
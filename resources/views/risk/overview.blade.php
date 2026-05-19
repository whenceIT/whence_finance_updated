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

    <!-- Date filter bar -->
    <div class="row" style="margin-bottom:16px;">
        <div class="col-md-12">
            <form method="GET" action="{{ route('risk.overview') }}" class="form-inline" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                @csrf
                <label style="font-size:13px;font-weight:600;color:#555;">Filter by date:</label>
                <div style="display:flex;align-items:center;gap:4px;">
                    <label for="filter_start" style="font-size:12px;color:#888;">From</label>
                    <input type="date" name="filter_start" id="filter_start" class="form-control input-sm"
                           value="{{ $filterStart ? $filterStart->format('Y-m-d') : '' }}" style="width:150px;">
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <label for="filter_end" style="font-size:12px;color:#888;">To</label>
                    <input type="date" name="filter_end" id="filter_end" class="form-control input-sm"
                           value="{{ $filterEnd ? $filterEnd->format('Y-m-d') : '' }}" style="width:150px;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Apply</button>
                <a href="{{ route('risk.overview') }}" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</a>
                @if($filterStart || $filterEnd)
                <span class="text-info" style="font-size:12px;">
                    <i class="fa fa-info-circle"></i>
                    @if($filterStart)
                    From {{ $filterStart->format('d M Y') }}
                    @endif
                    @if($filterEnd)
                    To {{ $filterEnd->format('d M Y') }}
                    @endif
                </span>
                @endif
            </form>
        </div>
    </div>

    <!-- ============================================================
         BRANCH AUDIT RESULTS OVERVIEW
         ============================================================ -->
    <div class="row" style="margin-top:20px;">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-building-o"></i>&nbsp; Branch <span class="badge bage-xl badge-primary">Completed</span> Audit Results — All Offices</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-default" style="font-size:12px;">{{ count($branches) }} branches</span>
                        &nbsp;
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="padding:16px;">

                    {{-- Summary strip --}}
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
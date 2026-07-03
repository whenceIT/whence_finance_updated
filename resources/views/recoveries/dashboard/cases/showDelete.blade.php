@extends('layouts.master')

@section('title')
    {{ $case->case_number ?? 'Case Detail' }}

@push('scripts')
<script>
    alert('here');
document.querySelectorAll('.nudge-ch').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.nudge-ch').forEach(b => {
            b.classList.remove('btn-warning', 'active');
            b.classList.add('btn-default');
        });
        this.classList.remove('btn-default');
        this.classList.add('btn-warning', 'active');
        document.getElementById('nudge-channel-input').value = this.dataset.channel;
    });
});
</script>
@endpush
@endsection

@section('content')
@php
    $categories = \App\Models\RecoveryCase::CATEGORIES;

    $clientName = ($case->client->client_type ?? '') === 'business'
        ? ($case->client->full_name ?? '—')
        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—');

    $loanRef = $case->loan->loan_id ?? ('Loan #' . $case->loan_id);

    $catLabels = [
        'cross_branch' => ['Cross-Branch',  'label-primary'],
        'escalated'    => ['Escalated',     'label-warning'],
        'dormant'      => ['Dormant',        'label-default'],
        'legal'        => ['Legal',          'label-danger'],
        'skip_trace'   => ['Skip Trace',     'label-success'],
    ];
    [$catLabel, $catClass] = $catLabels[$case->category] ?? [$case->category, 'label-default'];

    $statusLabels = [
        'runaway_pending_confirmation'       => ['Pending Confirmation',  'label-warning'],
        'runaway_active_recovery'            => ['Active Recovery',       'label-primary'],
        'recovered_runaway'                  => ['Recovered',             'label-success'],
        'escalated_handover'                 => ['Handover',              'label-default'],
        'escalated_in_review'                => ['In Review',             'label-info'],
        'escalated_active_recovery'          => ['Active Recovery',       'label-primary'],
        'recovered_post_escalation'          => ['Recovered',             'label-success'],
        'dormant_for_revival'                => ['For Revival',           'label-warning'],
        'recovery_revived'                   => ['Revived',               'label-success'],
        'pre_litigation_review'              => ['Pre-Litigation',        'label-warning'],
        'legal_filed'                        => ['Legal Filed',           'label-danger'],
        'legal_active'                       => ['Legal Active',          'label-danger'],
        'legal_judgment_won'                 => ['Judgment Won',          'label-info'],
        'recovered_legal'                    => ['Recovered',             'label-success'],
        'skip_trace_required'                => ['Trace Required',        'label-warning'],
        'skip_trace_digital_review'          => ['Digital Review',        'label-info'],
        'skip_trace_contact_reengagement'    => ['Re-engagement',         'label-primary'],
        'skip_trace_field_intel_active'      => ['Field Intel',           'label-primary'],
        'located_for_recovery'               => ['Located',               'label-info'],
        'closed'                             => ['Closed',                'label-default'],
        'written_off'                        => ['Written Off',           'label-default'],
    ];
    [$statusLabel, $statusClass] = $statusLabels[$case->status] ?? [ucwords(str_replace('_',' ',$case->status)), 'label-default'];

    $specialistName = $case->assignedSpecialist
        ? trim(($case->assignedSpecialist->first_name ?? '') . ' ' . ($case->assignedSpecialist->last_name ?? ''))
        : 'Unassigned';

    $caseExpenses = \App\Models\Expense::with(['type','createdBy'])
        ->where('recovery_case_id', $case->id)
        ->orderBy('date','desc')
        ->get();
@endphp

{{-- Page Header --}}
<div class="row">
    <div class="col-sm-12">
        <h4 style="margin:0 0 4px">
            {{ $case->case_number }}
            <span class="label {{ $catClass }}" style="vertical-align:middle;margin-left:6px">{{ $catLabel }}</span>
            <span class="label {{ $statusClass }}" style="vertical-align:middle;margin-left:4px">{{ $statusLabel }}</span>
        </h4>
        <small class="text-muted">
            {{ $clientName }} &nbsp;·&nbsp; {{ $loanRef }}
            &nbsp;·&nbsp; Opened {{ $case->created_at->format('d M Y') }}
        </small>
    </div>
</div>
<br>

{{-- KPI Info Boxes --}}
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-bank"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Outstanding</span>
                <span class="info-box-number">K {{ number_format($case->loan_outstanding_amount, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recovered</span>
                <span class="info-box-number">K {{ number_format($case->amount_recovered, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon {{ $case->net_recovery >= 0 ? 'bg-green' : 'bg-red' }}">
                <i class="fa fa-line-chart"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Net Recovery</span>
                <span class="info-box-number">K {{ number_format($case->net_recovery, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recovery Rate</span>
                <span class="info-box-number">{{ $case->recovery_rate }}%</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ min($case->recovery_rate,100) }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

{{-- LEFT COLUMN --}}
<div class="col-md-8">

    {{-- Attribution --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Attribution Split</h3>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach([
                    ['Recoveries Dept',  $case->recoveries_dept_attribution_pct,  'bg-aqua'],
                    ['Origin Branch',    $case->origin_branch_attribution_pct,    'bg-blue'],
                    ['Supporting Branch',$case->supporting_branch_attribution_pct,'bg-purple'],
                ] as [$lbl, $pct, $bg])
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon {{ $bg }}"><i class="fa fa-building"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ $lbl }}</span>
                            <span class="info-box-number">{{ $pct }}%</span>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="progress-description">
                                K {{ number_format($case->amount_recovered * ($pct / 100), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    

    {{-- Actions: Payment + Cost --}}
    @if(!$case->is_resolved)
    <div class="row">
        <div class="col-md-5">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-dollar"></i> Record Repayment</h3>
                </div>
                @else
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-dollar"></i> Pending</h3>
                </div>
                @endif
                <div class="box-body">
                    <p class="text-muted" style="font-size:13px;margin-bottom:14px">
                        Payments post through the loan repayment system for proper GL,
                        receipt numbering and SMS notification.
                    </p>
                    <a href="{{ url('loan/' . $case->loan_id . '/repayment/create') }}"
                       class="btn btn-success btn-block">
                        <i class="fa fa-dollar"></i> Open Loan Repayment Form
                    </a>
                    @if($case->payments->count())
                    <p class="text-center text-muted" style="margin-top:10px;font-size:12px;margin-bottom:0">
                        {{ $case->payments->count() }} payment(s) &mdash;
                        K {{ number_format($case->payments->sum('amount'), 2) }} total
                    </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-minus-circle"></i> Log a Cost</h3>
                </div>
                <div class="box-body">
                    <p class="text-muted" style="font-size:13px;margin-bottom:14px">
                        Costs post as expenses to GL accounts and appear in financial reports.
                    </p>
                    <a href="{{ url('expense/create?recovery_case_id=' . $case->id
                                   . '&name=Recovery+Case+' . $case->case_number
                                   . '&office_id=' . $case->origin_branch_id) }}"
                       class="btn btn-warning btn-block">
                        <i class="fa fa-minus-circle"></i> Open Expense Form
                    </a>
                    @if($caseExpenses->count())
                    <p class="text-center text-muted" style="margin-top:10px;font-size:12px;margin-bottom:0">
                        {{ $caseExpenses->count() }} expense(s) &mdash;
                        K {{ number_format($caseExpenses->sum('amount'), 2) }} total
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Payment History --}}
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-history"></i> Payment History</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            <table class="table table-hover table-striped" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Receipt</th>
                        <th>Dept Share</th>
                        <th>Received By</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($case->payments as $pay)
                    <tr>
                        <td>{{ $pay->payment_date->format('d M Y') }}</td>
                        <td><strong>K {{ number_format($pay->amount, 2) }}</strong></td>
                        <td>{{ ucwords(str_replace('_', ' ', $pay->payment_method ?? '')) }}</td>
                        <td><small>{{ $pay->receipt_number ?? '—' }}</small></td>
                        <td>K {{ number_format($pay->recoveries_dept_amount, 2) }}</td>
                        <td>
                            @if($pay->recordedBy)
                                {{ trim(($pay->recordedBy->first_name ?? '') . ' ' . ($pay->recordedBy->last_name ?? '')) ?: '—' }}
                            @else —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding:24px">
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
                @if($case->payments->count())
                <tfoot>
                    <tr class="active">
                        <th>Total</th>
                        <th>K {{ number_format($case->payments->sum('amount'), 2) }}</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Cost History --}}
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-minus-circle"></i> Cost History</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            @if($caseExpenses->count())
            <table class="table table-hover table-striped" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                        <th>Logged By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($caseExpenses as $exp)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($exp->date)->format('d M Y') }}</td>
                        <td>
                            <span class="label label-warning">
                                {{ optional($exp->type)->name ?? '—' }}
                            </span>
                        </td>
                        <td>{{ $exp->name }}</td>
                        <td class="text-right">
                            <strong>K {{ number_format($exp->amount, 2) }}</strong>
                        </td>
                        <td>
                            @if($exp->createdBy)
                                {{ trim(($exp->createdBy->first_name ?? '') . ' ' . ($exp->createdBy->last_name ?? '')) ?: '—' }}
                            @else —
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('expense/' . $exp->id . '/show') }}"
                               class="btn btn-xs btn-default" title="View Expense">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="active">
                        <th colspan="3">Total Costs</th>
                        <th class="text-right">K {{ number_format($caseExpenses->sum('amount'), 2) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="text-center text-muted" style="padding:24px">
                No costs recorded yet.
                @if(!$case->is_resolved)
                    Use the <strong>Open Expense Form</strong> button above.
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Documents --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-paperclip"></i> Documents</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            @if(!$case->is_resolved)
            <form method="POST" action="{{ url('recovery/case/' . $case->id . '/document/store') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control input-sm" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="document_type" class="form-control input-sm">
                                <option value="demand_letter">Demand Letter</option>
                                <option value="court_order">Court Order</option>
                                <option value="summons">Summons</option>
                                <option value="payment_receipt">Payment Receipt</option>
                                <option value="correspondence">Correspondence</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>File</label>
                            <input type="file" name="document" class="form-control input-sm" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endif

            @forelse($case->documents as $doc)
            <div class="well well-sm" style="margin-bottom:8px">
                <div class="row">
                    <div class="col-md-7">
                        @php
                            $iconMap = [
                                'application/pdf'  => 'fa-file-pdf-o text-danger',
                                'image/jpeg'       => 'fa-file-image-o text-info',
                                'image/png'        => 'fa-file-image-o text-info',
                                'application/msword'         => 'fa-file-word-o text-primary',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o text-primary',
                            ];
                            $icon = $iconMap[$doc->mime_type] ?? 'fa-file-o';
                        @endphp
                        <i class="fa {{ $icon }}"></i>
                        <strong>{{ $doc->title }}</strong>
                        <small class="text-muted">
                            &nbsp;·&nbsp;
                            <span class="label label-default">{{ ucwords(str_replace('_',' ',$doc->document_type)) }}</span>
                            &nbsp;· {{ $doc->file_size_formatted }}
                            &nbsp;· {{ $doc->created_at->format('d M Y') }}
                            @if($doc->uploadedBy)
                                &nbsp;· {{ trim(($doc->uploadedBy->first_name ?? '') . ' ' . ($doc->uploadedBy->last_name ?? '')) }}
                            @endif
                            @if($doc->notes)
                                <br><em>{{ $doc->notes }}</em>
                            @endif
                        </small>
                    </div>
                    <div class="col-md-5 text-right">
                        <a href="{{ url('recovery/case/' . $case->id . '/document/' . $doc->id . '/download') }}"
                           class="btn btn-xs btn-primary">
                            <i class="fa fa-download"></i> Download
                        </a>
                        <a href="{{ url('recovery/case/' . $case->id . '/document/' . $doc->id . '/delete') }}"
                           class="btn btn-xs btn-danger"
                           onclick="return confirm('Delete this document?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted text-center">No documents uploaded yet.</p>
            @endforelse
        </div>
    </div>


    {{-- Nudge History --}}
    @php $nudges = \App\Models\RecoveryNudge::where('recovery_case_id', $case->id)->with('sentBy')->latest()->limit(20)->get(); @endphp
    @if($nudges->isNotEmpty())
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-bell"></i> Nudge History
                <span class="badge bg-blue">{{ $nudges->count() }}</span>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            <table class="table table-condensed table-hover" style="margin:0">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Channel</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Sent By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nudges as $nudge)
                    <tr>
                        <td>{{ $nudge->created_at->format('d M Y H:i') }}</td>
                        <td>
                            @if($nudge->channel === 'whatsapp')
                                <span class="label label-success"><i class="fa fa-whatsapp"></i> WhatsApp</span>
                            @else
                                <span class="label label-info"><i class="fa fa-comment"></i> SMS</span>
                            @endif
                        </td>
                        <td><small>{{ $nudge->phone_number }}</small></td>
                        <td>
                            @if($nudge->status === 'sent')
                                <span class="label label-success">Sent</span>
                            @else
                                <span class="label label-danger">Failed</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ trim(($nudge->sentBy->first_name ?? '') . ' ' . ($nudge->sentBy->last_name ?? '')) ?: '—' }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Activity Log --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list-ul"></i> Activity Log</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            @forelse($case->activities->sortByDesc('created_at') as $act)
            <div class="callout callout-info" style="margin:0;border-radius:0;border-left:3px solid #00c0ef">
                <p style="margin:0;font-size:13px">{{ $act->description }}</p>
                <small class="text-muted">
                    {{ $act->created_at->diffForHumans() }}
                    @if($act->performedBy)
                        &nbsp;·&nbsp;
                        {{ trim(($act->performedBy->first_name ?? '') . ' ' . ($act->performedBy->last_name ?? '')) ?: '—' }}
                    @endif
                </small>
            </div>
            @empty
            <div class="text-center text-muted" style="padding:24px">No activity logged yet.</div>
            @endforelse
        </div>
    </div>

</div>{{-- /.col-md-8 --}}

{{-- RIGHT COLUMN --}}
<div class="col-md-4">

    {{-- Actions --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cog"></i> Actions</h3>
        </div>
        <div class="box-body">
            <a href="{{ url('recovery/case/' . $case->id . '/edit') }}"
               class="btn btn-primary btn-block" style="margin-bottom:6px">
                <i class="fa fa-pencil"></i> Edit Case
            </a>
            <a href="{{ url('recovery/case/data') }}"
               class="btn btn-default btn-block">
                <i class="fa fa-arrow-left"></i> Back to Cases
            </a>
        </div>
    </div>

    {{-- Quick Assign Specialist --}}
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user-plus"></i> Assign Specialist</h3>
        </div>
        <div class="box-body">
            @php
                $allSpecialists = \App\Models\User::orderBy('first_name')->get();
            @endphp
            <form method="POST" action="{{ url('recovery/case/' . $case->id . '/assign') }}">
                @csrf
                <div class="form-group" style="margin-bottom:8px">
                    <select name="specialist_id" class="form-control input-sm" required>
                        <option value="">— Select Specialist —</option>
                        @foreach($allSpecialists as $u)
                        <option value="{{ $u->id }}"
                            {{ $case->assigned_specialist_id == $u->id ? 'selected' : '' }}>
                            {{ trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->email }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-info btn-sm btn-block">
                    <i class="fa fa-save"></i>
                    {{ $case->assigned_specialist_id ? 'Reassign' : 'Assign' }}
                </button>
            </form>
            @if($case->assignedSpecialist)
            <p class="text-muted text-center" style="margin-top:8px;margin-bottom:0;font-size:12px">
                Currently: <strong>{{ $specialistName }}</strong>
            </p>
            @endif
        </div>
    </div>


    {{-- Send Nudge --}}
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bell"></i> Send Nudge</h3>
        </div>
        <div class="box-body">
            @if($case->client->phone ?? null)
            <form method="POST" action="{{ url('recovery/case/' . $case->id . '/nudge/send') }}">
                @csrf
                <div class="form-group" style="margin-bottom:8px">
                    <div class="btn-group btn-group-justified" id="nudge-channel-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default nudge-ch active"
                                    data-channel="sms">
                                <i class="fa fa-comment"></i> SMS
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default nudge-ch"
                                    data-channel="whatsapp">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="channel" id="nudge-channel-input" value="sms">
                </div>
                @php
                    $nudgeName = ($case->client->client_type ?? '') === 'business'
                        ? ($case->client->full_name ?? 'Valued Client')
                        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: 'Valued Client');
                    $nudgeBal  = 'K' . number_format(($case->loan_outstanding_amount ?? 0) - ($case->amount_recovered ?? 0), 2);
                @endphp
                <div class="form-group" style="margin-bottom:8px">
                    <textarea name="message" class="form-control input-sm" rows="4"
                              maxlength="1000" required
                              id="nudge-message">Dear {{ $nudgeName }}, you have an outstanding loan balance of {{ $nudgeBal }}. Please make payment arrangements at your earliest convenience. Ref: {{ $case->case_number }}.</textarea>
                </div>
                <button type="submit" class="btn btn-warning btn-sm btn-block">
                    <i class="fa fa-paper-plane"></i> Send Nudge
                </button>
            </form>
            <p class="text-muted" style="margin-top:6px;margin-bottom:0;font-size:11px;text-align:center">
                <i class="fa fa-phone"></i> {{ $case->client->phone }}
            </p>
            @else
            <p class="text-muted text-center" style="margin-bottom:0">
                <i class="fa fa-exclamation-triangle text-warning"></i>
                No phone number on record for this client.
            </p>
            @endif
        </div>
    </div>

    {{-- Case Details --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Case Details</h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-condensed" style="margin-bottom:0">
                <tbody>
                    <tr><td class="text-muted">Case Number</td><td><strong>{{ $case->case_number }}</strong></td></tr>
                    <tr><td class="text-muted">Category</td><td><span class="label {{ $catClass }}">{{ $catLabel }}</span></td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="label {{ $statusClass }}">{{ $statusLabel }}</span></td></tr>
                    <tr><td class="text-muted">Opened</td><td>{{ $case->created_at->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Target Resolution</td><td>{{ $case->target_resolution_date?->format('d M Y') ?? 'Not set' }}</td></tr>
                    <tr><td class="text-muted">Origin Branch</td><td>{{ $case->originBranch->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Supporting Branch</td><td>{{ $case->supportingBranch->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Specialist</td><td>{{ $specialistName }}</td></tr>
                    @if($case->days_past_due_at_escalation)
                    <tr><td class="text-muted">DPD at Escalation</td><td>{{ $case->days_past_due_at_escalation }}</td></tr>
                    @endif
                    @if($case->lc_contact_attempts)
                    <tr><td class="text-muted">LC Contact Attempts</td><td>{{ $case->lc_contact_attempts }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Client Info --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user"></i> Client</h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-condensed" style="margin-bottom:0">
                <tbody>
                    <tr><td class="text-muted">Name</td><td><strong>{{ $clientName }}</strong></td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $case->client->phone ?? '—' }}</td></tr>
                    <tr><td class="text-muted">ID Number</td><td>{{ $case->client->national_id ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Loan Ref</td><td>{{ $loanRef }}</td></tr>
                    @if($case->client_last_known_location)
                    <tr><td class="text-muted">Last Known Location</td><td>{{ $case->client_last_known_location }}</td></tr>
                    @endif
                    @if($case->client_new_location)
                    <tr><td class="text-muted">New Location</td><td>{{ $case->client_new_location }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legal Details --}}
    @if($case->category === 'legal' && $case->legal_reference_number)
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-gavel"></i> Legal Details</h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-condensed" style="margin-bottom:0">
                <tbody>
                    <tr><td class="text-muted">Reference</td><td>{{ $case->legal_reference_number }}</td></tr>
                    <tr><td class="text-muted">Lawyer / Firm</td><td>{{ $case->lawyer_firm ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Filed Date</td><td>{{ $case->legal_filed_date?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Court Date</td><td>{{ $case->court_date?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Enforcement</td><td>{{ ucwords(str_replace('_',' ',$case->enforcement_type ?? 'none')) }}</td></tr>
                    <tr><td class="text-muted">Legal Costs</td><td>K {{ number_format($case->legal_costs_incurred, 2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($case->notes)
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-sticky-note-o"></i> Notes</h3>
        </div>
        <div class="box-body">
            <p style="font-size:13px;line-height:1.6;margin:0">{{ $case->notes }}</p>
        </div>
    </div>
    @endif

</div>{{-- /.col-md-4 --}}
</div>{{-- /.row --}}

@endsection

@extends('layouts.master')

@section('title')
    {{ $case->case_number ?? 'Case Detail' }}
@endsection

@push('scripts')
<script>
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

@section('content')
@php
    $categories = \App\Models\RecoveryCase::CATEGORIES;

    $clientName = ($case->client->client_type ?? '') === 'business'
        ? ($case->client->full_name ?? '—')
        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—');

    $loanRef = $case->loan->loan_id ?? ('Loan #' . $case->loan_id);
@endphp

{{-- Back Button --}}
<div class="row">
    <div class="col-12">
        <a href="{{ url('recovery/case/data') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Cases
        </a>
    </div>
</div>

{{-- KPI Boxes --}}
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
</div>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $case->case_number }}</h3>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#basic" aria-controls="basic" role="tab" data-toggle="tab">Basic Info</a></li>
                    <li role="presentation"><a href="#financial" aria-controls="financial" role="tab" data-toggle="tab">Financial</a></li>
                    <li role="presentation"><a href="#legal" aria-controls="legal" role="tab" data-toggle="tab">Legal</a></li>
                    <li role="presentation"><a href="#skiptrace" aria-controls="skiptrace" role="tab" data-toggle="tab">Skip Trace</a></li>
                    <li role="presentation"><a href="#dormant" aria-controls="dormant" role="tab" data-toggle="tab">Dormant</a></li>
                    <li role="presentation"><a href="#resolution" aria-controls="resolution" role="tab" data-toggle="tab">Resolution</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="basic">
                        <dl class="dl-horizontal">
                            <dt>Client:</dt><dd>{{ $clientName }}</dd>
                            <dt>Loan:</dt><dd>{{ $loanRef }}</dd>
                            <dt>Status:</dt><dd><span class="label label-{{ $case->status_color }}">{{ $case->status }}</span></dd>
                            <dt>Category:</dt><dd>{{ $case->category_label }}</dd>
                            <dt>Assigned Specialist:</dt><dd>{{ $case->assignedSpecialist?->first_name ?? '' }} {{ $case->assignedSpecialist?->last_name ?? '—' }}</dd>
                            <dt>Origin Branch:</dt><dd>{{ $case->originBranch?->name ?? '—' }}</dd>
                            <dt>Supporting Branch:</dt><dd>{{ $case->supportingBranch?->name ?? '—' }}</dd>
                            <dt>Escalated By:</dt><dd>{{ $case->escalatedBy?->first_name ?? '' }} {{ $case->escalatedBy?->last_name ?? '—' }}</dd>
                            <dt>Escalation Date:</dt><dd>{{ $case->escalation_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Days Past Due at Escalation:</dt><dd>{{ $case->days_past_due_at_escalation ?? '—' }}</dd>
                            <dt>LC Contact Attempts:</dt><dd>{{ $case->lc_contact_attempts ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="financial">
                        <dl class="dl-horizontal">
                            <dt>Loan Outstanding Amount:</dt><dd>K {{ number_format($case->loan_outstanding_amount, 2) }}</dd>
                            <dt>Amount Recovered:</dt><dd>K {{ number_format($case->amount_recovered, 2) }}</dd>
                            <dt>Recovery Costs:</dt><dd>K {{ number_format($case->recovery_costs, 2) }}</dd>
                            <dt>Settlement Amount:</dt><dd>K {{ number_format($case->settlement_amount, 2) }}</dd>
                            <dt>Net Recovery:</dt><dd>K {{ number_format($case->net_recovery, 2) }}</dd>
                            <dt>Recovery Rate:</dt><dd>{{ $case->recovery_rate }}%</dd>
                            <dt>Recoveries Dept Amount:</dt><dd>K {{ number_format($case->recoveries_dept_amount, 2) }}</dd>
                            <dt>Recoveries Dept Attribution %:</dt><dd>{{ $case->recoveries_dept_attribution_pct }}%</dd>
                            <dt>Origin Branch Attribution %:</dt><dd>{{ $case->origin_branch_attribution_pct }}%</dd>
                            <dt>Supporting Branch Attribution %:</dt><dd>{{ $case->supporting_branch_attribution_pct }}%</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="legal">
                        <dl class="dl-horizontal">
                            <dt>Legal Reference Number:</dt><dd>{{ $case->legal_reference_number ?? '—' }}</dd>
                            <dt>Lawyer Firm:</dt><dd>{{ $case->lawyer_firm ?? '—' }}</dd>
                            <dt>Legal Filed Date:</dt><dd>{{ $case->legal_filed_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Court Date:</dt><dd>{{ $case->court_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Legal Costs Incurred:</dt><dd>K {{ number_format($case->legal_costs_incurred, 2) }}</dd>
                            <dt>Enforcement Type:</dt><dd>{{ $case->enforcement_type ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="skiptrace">
                        <dl class="dl-horizontal">
                            <dt>Skip Trace Tracking Code:</dt><dd>{{ $case->skip_trace_tracking_code ?? '—' }}</dd>
                            <dt>Client Located:</dt><dd>{{ $case->client_located ? 'Yes' : 'No' }}</dd>
                            <dt>Located Date:</dt><dd>{{ $case->located_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Skip Trace Costs:</dt><dd>K {{ number_format($case->skip_trace_costs, 2) }}</dd>
                            <dt>Client Last Known Location:</dt><dd>{{ $case->client_last_known_location ?? '—' }}</dd>
                            <dt>Client New Location:</dt><dd>{{ $case->client_new_location ?? '—' }}</dd>
                            <dt>Joint Field Visit Done:</dt><dd>{{ $case->joint_field_visit_done ? 'Yes' : 'No' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="dormant">
                        <dl class="dl-horizontal">
                            <dt>Last Payment Date:</dt><dd>{{ $case->last_payment_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Dormant Days:</dt><dd>{{ $case->dormant_days ?? '—' }}</dd>
                            <dt>Revival Method:</dt><dd>{{ $case->revival_method ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="resolution">
                        <dl class="dl-horizontal">
                            <dt>Notes:</dt><dd>{{ $case->notes ?? '—' }}</dd>
                            <dt>Target Resolution Date:</dt><dd>{{ $case->target_resolution_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Resolved Date:</dt><dd>{{ $case->resolved_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Approved Date:</dt><dd>{{ $case->approved_date?->format('Y-m-d') ?? '—' }}</dd>
                            <dt>Is Resolved:</dt><dd>{{ $case->is_resolved ? 'Yes' : 'No' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loan Details --}}
        @if($case->loan)
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Loan Details</h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Loan ID:</dt><dd>{{ $case->loan->loan_id ?? $case->loan->id }}</dd>
                    <dt>Status:</dt><dd>{{ $case->loan->status }}</dd>
                    <dt>Principal Amount:</dt><dd>K {{ number_format($case->loan->principal_amount ?? 0, 2) }}</dd>
                    <dt>Interest Rate:</dt><dd>{{ $case->loan->interest_rate ?? '—' }}%</dd>
                    <dt>Term:</dt><dd>{{ $case->loan->term ?? '—' }} months</dd>
                    <dt>Disbursed Date:</dt><dd>{{ $case->loan->disbursed_date?->format('Y-m-d') ?? '—' }}</dd>
                    <dt>Maturity Date:</dt><dd>{{ $case->loan->maturity_date?->format('Y-m-d') ?? '—' }}</dd>
                    <dt>Loan Officer:</dt><dd>{{ $case->loan->loan_officer?->first_name ?? '' }} {{ $case->loan->loan_officer?->last_name ?? '—' }}</dd>
                    <dt>Office:</dt><dd>{{ $case->loan->office?->name ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        @endif

        {{-- Client Details --}}
        @if($case->client)
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Client Details</h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Client Type:</dt><dd>{{ $case->client->client_type ?? '—' }}</dd>
                    <dt>Full Name:</dt><dd>{{ $case->client->full_name ?? ($case->client->first_name . ' ' . $case->client->last_name) }}</dd>
                    <dt>Email:</dt><dd>{{ $case->client->email ?? '—' }}</dd>
                    <dt>Phone:</dt><dd>{{ $case->client->phone ?? '—' }}</dd>
                    <dt>Address:</dt><dd>{{ $case->client->address ?? '—' }}</dd>
                    <dt>NRC ID:</dt><dd>{{ $case->client->nrc_id ?? '—' }}</dd>
                    <dt>Staff:</dt><dd>{{ $case->client->staff?->first_name ?? '' }} {{ $case->client->staff?->last_name ?? '—' }}</dd>
                    <dt>Office:</dt><dd>{{ $case->client->office?->name ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        @endif

        {{-- Recent Transactions --}}
        @if($case->loan && $case->loan->transactions->count() > 0)
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Recent Loan Transactions</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Recovery</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($case->loan->transactions->take(10) as $transaction)
                        <tr>
                            <td>{{ $transaction->date?->format('Y-m-d') ?? $transaction->created_at->format('Y-m-d') }}</td>
                            <td>{{ $transaction->type ?? '—' }}</td>
                            <td>K {{ number_format($transaction->amount ?? 0, 2) }}</td>
                            <td>{{ $transaction->recovery ? 'Yes' : 'No' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

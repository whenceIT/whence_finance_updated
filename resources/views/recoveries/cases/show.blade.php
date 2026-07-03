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

    $loanRef = $case->loan->loan_id ?? $case->loan_id;
@endphp

{{-- Back Button & Toolbar --}}
<div class="row" style="margin-bottom: 15px;">
    <div class="col-xs-6">
        <a href="{{ url('recovery/case/data') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Cases
        </a>
    </div>
    <div class="col-xs-6 text-right">
        @if($case->loan)
            @if($case->approved_date != null)
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#recovery_modal">
                    <i class="fa fa-money"></i> Record Repayment
                </button>
            @else
                <span class="label label-warning">
                    <i class="fa fa-money"></i> Pending Approval
                </span>
            @endif
        @else
            <i class="fa fa-money">No loan associated with this case</i> 
        @endif
    </div>
</div>

{{-- KPI Boxes --}}
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-bank"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Outstanding</span>
                <span class="info-box-number">K {{ number_format(($case->loan_outstanding_amount ?? 0) - ($case->payments->where('status', 1)->sum('amount')), 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recovered</span>
                <span class="info-box-number">K {{ number_format($case->payments->where('status', 1)->sum('amount'), 2) }}</span>
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
                            <dt>Escalation Date:</dt><dd>{{ $case->escalation_date ?? '—' }}</dd>
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
                            <dt>Legal Filed Date:</dt><dd>{{ $case->legal_filed_date ?? '—' }}</dd>
                            <dt>Court Date:</dt><dd>{{ $case->court_date ?? '—' }}</dd>
                            <dt>Legal Costs Incurred:</dt><dd>K {{ number_format($case->legal_costs_incurred, 2) }}</dd>
                            <dt>Enforcement Type:</dt><dd>{{ $case->enforcement_type ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="skiptrace">
                        <dl class="dl-horizontal">
                            <dt>Skip Trace Tracking Code:</dt><dd>{{ $case->skip_trace_tracking_code ?? '—' }}</dd>
                            <dt>Client Located:</dt><dd>{{ $case->client_located ? 'Yes' : 'No' }}</dd>
                            <dt>Located Date:</dt><dd>{{ $case->located_date ?? '—' }}</dd>
                            <dt>Skip Trace Costs:</dt><dd>K {{ number_format($case->skip_trace_costs, 2) }}</dd>
                            <dt>Client Last Known Location:</dt><dd>{{ $case->client_last_known_location ?? '—' }}</dd>
                            <dt>Client New Location:</dt><dd>{{ $case->client_new_location ?? '—' }}</dd>
                            <dt>Joint Field Visit Done:</dt><dd>{{ $case->joint_field_visit_done ? 'Yes' : 'No' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="dormant">
                        <dl class="dl-horizontal">
                            <dt>Last Payment Date:</dt><dd>{{ $case->last_payment_date ?? '—' }}</dd>
                            <dt>Dormant Days:</dt><dd>{{ $case->dormant_days ?? '—' }}</dd>
                            <dt>Revival Method:</dt><dd>{{ $case->revival_method ?? '—' }}</dd>
                        </dl>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="resolution">
                        <dl class="dl-horizontal">
                            <dt>Notes:</dt><dd>{{ $case->notes ?? '—' }}</dd>
                            <dt>Target Resolution Date:</dt><dd>{{ $case->target_resolution_date ?? '—' }}</dd>
                            <dt>Resolved Date:</dt><dd>{{ $case->resolved_date ?? '—' }}</dd>
                            <dt>Approved Date:</dt><dd>{{ $case->approved_date ?? '—' }}</dd>
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
                    <dt>Disbursed Date:</dt><dd>{{ $case->loan->disbursed_date ?? '—' }}</dd>
                    <dt>Maturity Date:</dt><dd>{{ $case->loan->maturity_date ?? '—' }}</dd>
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
                            <td>{{ $transaction->date ?? $transaction->created_at->format('Y-m-d') }}</td>
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

{{-- Include Recovery Payment Modal --}}

    @php
        // Prepare data for the recovery modal
        $loan = \App\Models\Loan::where('id',$loanRef)->first();
        $recoveryCases = collect([$case]); // Pass current case as collection
    @endphp
    @include('loan.repayment.recovery_modal')


@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Recovery Case Auto-fill Logic
    $('#recovery_case_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var outstanding = parseFloat(selectedOption.data('outstanding')) || 0;
        var amountRecovered = parseFloat(selectedOption.data('amount-recovered')) || 0;
        var clientName = selectedOption.data('client-name') || '-';
        var recoveriesDeptPct = parseFloat(selectedOption.data('recoveries-dept-pct')) || 0;
        var originBranchPct = parseFloat(selectedOption.data('origin-branch-pct')) || 0;
        var supportingBranchPct = parseFloat(selectedOption.data('supporting-branch-pct')) || 0;
        
        // Show/hide panels
        if ($(this).val()) {
            $('#caseInfoPanel').show();
            $('#attributionPanel').show();
        } else {
            $('#caseInfoPanel').hide();
            $('#attributionPanel').hide();
        }
        
        // Update case info display
        $('#displayClientName').text(clientName);
        $('#displayOutstanding').text(outstanding.toFixed(2));
        $('#displayAmountRecovered').text(amountRecovered.toFixed(2));
        $('#displayOutstandingBefore').text(outstanding.toFixed(2));
        
        // Update percentage labels
        $('#recoveriesDeptPctLabel').text('(' + recoveriesDeptPct + '%)');
        $('#originBranchPctLabel').text('(' + originBranchPct + '%)');
        $('#supportingBranchPctLabel').text('(' + supportingBranchPct + '%)');
        
        // Set outstanding before
        $('#outstanding_before').val(outstanding);
        
        // Reset amount and calculate outstanding after when amount changes
        $('#recovery_amount').val('');
        $('#outstanding_after').val(outstanding);
        $('#displayOutstandingAfter').text(outstanding.toFixed(2));
        
        // Reset attribution amounts
        $('#recoveries_dept_amount').val(0);
        $('#origin_branch_amount').val(0);
        $('#supporting_branch_amount').val(0);
        $('#displayRecoveriesDeptAmount').text('0.00');
        $('#displayOriginBranchAmount').text('0.00');
        $('#displaySupportingBranchAmount').text('0.00');
        
        // Reset settlement dropdown
        $('#is_settlement').val('0');
        $('#settlementHint').text('Select "Yes" if this payment fully settles the debt');
    });

    // Calculate attribution amounts when amount changes
    $('#recovery_amount').on('input', function() {
        var amount = parseFloat($(this).val()) || 0;
        var outstanding = parseFloat($('#outstanding_before').val()) || 0;
        var recoveriesDeptPct = parseFloat($('#recovery_case_id option:selected').data('recoveries-dept-pct')) || 0;
        var originBranchPct = parseFloat($('#recovery_case_id option:selected').data('origin-branch-pct')) || 0;
        var supportingBranchPct = parseFloat($('#recovery_case_id option:selected').data('supporting-branch-pct')) || 0;
        
        // Calculate outstanding after
        var outstandingAfter = Math.max(0, outstanding - amount);
        $('#outstanding_after').val(outstandingAfter);
        $('#displayOutstandingAfter').text(outstandingAfter.toFixed(2));
        
        // Auto-detect settlement: if outstanding after is 0 or amount >= outstanding, set settlement to Yes
        if (outstandingAfter <= 0 && amount > 0) {
            $('#is_settlement').val('1');
            $('#settlementHint').html('<span class="text-success">✓ Auto-detected: This payment will fully settle the debt</span>');
        } else if (amount >= outstanding && outstanding > 0) {
            $('#is_settlement').val('1');
            $('#settlementHint').html('<span class="text-success">✓ Auto-detected: This payment will fully settle the debt</span>');
        } else {
            $('#is_settlement').val('0');
            $('#settlementHint').text('Select "Yes" if this payment fully settles the debt');
        }
        
        // Calculate attribution amounts
        var recoveriesDeptAmount = (amount * recoveriesDeptPct / 100);
        var originBranchAmount = (amount * originBranchPct / 100);
        var supportingBranchAmount = (amount * supportingBranchPct / 100);
        
        $('#recoveries_dept_amount').val(recoveriesDeptAmount);
        $('#origin_branch_amount').val(originBranchAmount);
        $('#supporting_branch_amount').val(supportingBranchAmount);
        
        $('#displayRecoveriesDeptAmount').text(recoveriesDeptAmount.toFixed(2));
        $('#displayOriginBranchAmount').text(originBranchAmount.toFixed(2));
        $('#displaySupportingBranchAmount').text(supportingBranchAmount.toFixed(2));
    });

    // Handle Payment Method change - update reference label and show/hide bank field
    $('#payment_method').on('change', function() {
        var selectedValue = $(this).val().toLowerCase();
        
        // Reset bank field
        $('#bankRow').hide();
        $('#bank_name').val('');
        
        // Update reference label based on payment method
        if (selectedValue.indexOf('cash') !== -1) {
            $('#paymentReferenceLabel').text('Receiver Name');
            $('#payment_reference').attr('placeholder', 'Enter name of person receiving the payment');
        } else if (selectedValue.indexOf('bank') !== -1 || selectedValue.indexOf('transfer') !== -1 || selectedValue.indexOf('cheque') !== -1) {
            $('#paymentReferenceLabel').text('Bank Payment Reference');
            $('#payment_reference').attr('placeholder', 'Enter cheque number, bank transaction ID, etc.');
            $('#bankRow').show();
        } else if (selectedValue.indexOf('mobile') !== -1 || selectedValue.indexOf('money') !== -1) {
            $('#paymentReferenceLabel').text('Mobile Money Transaction (Txn#) Reference');
            $('#payment_reference').attr('placeholder', 'Enter mobile money transaction number');
        } else {
            $('#paymentReferenceLabel').text('Payment Reference');
            $('#payment_reference').attr('placeholder', 'Cheque number, transaction ID, etc.');
        }
    });

    // Handle Is Settlement change (manual override)
    $('#is_settlement').on('change', function() {
        if ($(this).val() == '1') {
            // If settlement, set outstanding after to 0
            var outstanding = parseFloat($('#outstanding_before').val()) || 0;
            var amount = parseFloat($('#recovery_amount').val()) || 0;
            if (amount >= outstanding) {
                $('#outstanding_after').val(0);
                $('#displayOutstandingAfter').text('0.00');
            }
        }
    });

    // Recovery form submission
    $('#recoverySubmitBtn').click(function(event){
        event.preventDefault();
        swal({
            title: "Are you sure you want to record this recovery payment?",
            text: "This will update the recovery case and loan balance.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            buttons: ["Cancel","Yes!"],
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes I'm sure!"
        }).then((willDelete) => {
            if (willDelete) {
                $('#recoveryForm').submit();
            }
        });
    });
});
</script>
@endpush
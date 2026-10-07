@php
    $tenure = $loan->loan_term;

    if ($loan->schedule_type === 'old') {
        // Old schedule: lookup from payroll_loan_old_schedule
        $oldRow = \App\Models\PayrollLoanOldSchedule::where('disbursement_amount', $loan->principal)->first();
        $colMap = [9 => 'repayment_9_months', 12 => 'repayment_12_months', 18 => 'repayment_18_months', 24 => 'repayment_24_months'];
        $col = $colMap[$tenure] ?? null;
        $monthlyAmount = ($oldRow && $col) ? $oldRow->$col : null;
    } else {
        // New schedule: lookup from payroll_loan_schedules
        $schedule = DB::table('payroll_loan_schedules')
            ->where('loan_amount', $loan->principal)
            ->first();
        $monthlyAmount = $schedule ? ($schedule->{"months_$tenure"} ?? null) : null;
    }

    $totalRepayment = $monthlyAmount ? ($monthlyAmount * $tenure) : null;
    $totalPaid = DB::table('loan_transactions')
        ->where('loan_id', $loan->id)
        ->where('transaction_type', 'repayment')
        ->sum('credit');
    $paidPercentage = $totalRepayment ? min(100, round(($totalPaid / $totalRepayment) * 100)) : 0;
    $scheduleLabel  = ($loan->schedule_type === 'old') ? 'Old Schedule' : 'New Schedule';
@endphp

@if($monthlyAmount)
    <p class="text-muted" style="margin-bottom:8px;">
        <i class="fa fa-table"></i>&nbsp;
        Using <strong>{{ $scheduleLabel }}</strong>
    </p>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.amount',1)}}</th>
                <th>Tenure</th>
                <th>{{trans_choice('general.monthly',1)}} {{trans_choice('general.amount',1)}}</th>
                <th>{{trans_choice('general.total',1)}} {{trans_choice('general.repayment',1)}}</th>
                <th>{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>K{{ number_format($loan->principal, 2) }}</td>
                <td>{{ $tenure }} Months</td>
                <td>K{{ number_format($monthlyAmount, 2) }}</td>
                <td>K{{ number_format($totalRepayment, 2) }}</td>
                <td>K{{ number_format($totalPaid, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="progress" style="height: 20px; margin-top: 15px;">
        <div class="progress-bar" role="progressbar" style="width: {{ $paidPercentage }}%;" aria-valuenow="{{ $paidPercentage }}" aria-valuemin="0" aria-valuemax="100">
            {{ $paidPercentage }}%
        </div>
    </div>
    <p style="margin-top: 10px;">
        {{ trans_choice('general.paid',1) }}: K{{ number_format($totalPaid, 2) }} / {{ trans_choice('general.total',1) }}: K{{ number_format($totalRepayment, 2) }}
    </p>
@else
    <p>No schedule found for this loan amount and tenure.</p>
@endif

{{-- ══════════════════════════════════════════════════════
     BULK REPAYMENTS SECTION
══════════════════════════════════════════════════════ --}}
@if($monthlyAmount)
<div style="margin-top:20px; border-top:1px solid #eee; padding-top:15px;">
    <button type="button"
            class="btn btn-warning btn-sm"
            data-toggle="modal"
            data-target="#bulkRepaymentModal">
        <i class="fa fa-plus-circle"></i> Update Payments
    </button>
    <small class="text-muted" style="margin-left:8px;">
        Record bulk monthly repayments (K{{ number_format($monthlyAmount, 2) }}/month)
    </small>
</div>

<!-- Bulk Repayment Modal -->
<div class="modal fade" id="bulkRepaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f39c12; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Bulk Repayments
                </h4>
            </div>

            <form method="POST"
                  action="{{ route('payrollloans.bulk-repayments', $loan->id) }}">
                @csrf
                <div class="modal-body">

                    <div class="alert alert-info" style="font-size:13px;">
                        <strong>Monthly Amount:</strong> K{{ number_format($monthlyAmount, 2) }}<br>
                        <strong>Starting from:</strong>
                        {{ $loan->disbursement_date
                            ? \Carbon\Carbon::parse($loan->disbursement_date)->addMonth()->format('M Y')
                            : 'Next month' }}
                    </div>

                    <div class="form-group">
                        <label>Number of Monthly Payments to Record</label>
                        <input type="number"
                               name="num_payments"
                               class="form-control"
                               min="1"
                               max="{{ $tenure }}"
                               placeholder="e.g. 3"
                               required
                               id="numPaymentsInput">
                        <small class="text-muted">Max: {{ $tenure }} (full tenure)</small>
                    </div>

                    <div id="bulkPreview" style="display:none; margin-top:10px;">
                        <strong>Preview:</strong>
                        <div id="bulkPreviewList" style="font-size:12px; margin-top:5px;"></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save"></i> Create Repayments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var monthly     = {{ $monthlyAmount }};
    var startStr    = '{{ $loan->disbursement_date ?? now()->toDateString() }}';
    var input       = document.getElementById('numPaymentsInput');
    var preview     = document.getElementById('bulkPreview');
    var previewList = document.getElementById('bulkPreviewList');
    var months      = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    if (input) {
        input.addEventListener('input', function () {
            var n = parseInt(this.value);
            if (!n || n < 1) { preview.style.display = 'none'; return; }

            var html = '<table class="table table-condensed" style="margin:0;">';
            html += '<thead><tr><th>#</th><th>Date</th><th>Amount</th></tr></thead><tbody>';
            for (var i = 1; i <= n; i++) {
                var d = new Date(startStr);
                d.setMonth(d.getMonth() + i);
                html += '<tr><td>' + i + '</td>' +
                        '<td>' + months[d.getMonth()] + ' ' + d.getFullYear() + '</td>' +
                        '<td>K' + monthly.toLocaleString('en', {minimumFractionDigits:2}) + '</td></tr>';
            }
            html += '<tr style="font-weight:bold;"><td colspan="2">Total</td>' +
                    '<td>K' + (monthly * n).toLocaleString('en', {minimumFractionDigits:2}) + '</td></tr>';
            html += '</tbody></table>';

            previewList.innerHTML = html;
            preview.style.display = 'block';
        });
    }
})();
</script>
@endif
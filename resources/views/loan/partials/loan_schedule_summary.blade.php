@php
    $tenure = $loan->loan_term;

    if ($loan->schedule_type === 'old') {
        $oldRow = \App\Models\PayrollLoanOldSchedule::where('disbursement_amount', $loan->principal)->first();
        $col = 'repayment_' . $tenure . '_months';
        $monthlyAmount = $oldRow ? ($oldRow->$col ?? null) : null;
    } else {
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
    $restructuredAt = $loan->restructured_at ? \Carbon\Carbon::parse($loan->restructured_at)->format('d M Y H:i') : null;
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

    @if($restructuredAt)
        <span class="badge badge-warning" style="font-size:12px;">
            <i class="fa fa-random"></i> Restructured on {{ $restructuredAt }}
        </span>
    @endif

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

    <button type="button"
            class="btn btn-info btn-sm"
            style="margin-left: 10px;"
            data-toggle="modal"
            data-target="#restructureModal">
        <i class="fa fa-random"></i> Restructure Tenure
    </button>
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

                    <div class="form-group">
                        <label>Start Date for First Repayment</label>
                        <input type="date"
                               name="start_date"
                               class="form-control"
                               id="bulkStartDateInput"
                               value="{{ $loan->disbursement_date ?? now()->toDateString() }}">
                        <small class="text-muted">Leave as-is to start from the disbursement date.</small>
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

<!-- Restructure Loan Tenure Modal -->
<div class="modal fade" id="restructureModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#17a2b8; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-random"></i> Restructure Loan Tenure
                </h4>
            </div>

            <form method="POST" action="{{ route('payrollloans.restructure', $loan->id) }}">
                @csrf
                <input type="hidden" name="monthly_amount" id="monthlyAmountHidden" value="{{ $monthlyAmount }}">
                <input type="hidden" name="tenure" id="tenureHidden" value="{{ $tenure }}">
                <input type="hidden" name="schedule_type" value="{{ $loan->schedule_type }}">
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:13px;">
                        <strong>Current:</strong>
                        <ul style="font-size:14px; margin:5px 0;">
                            <li>Tenure: <span id="currentTenureDisplay">{{ $tenure }}</span> months (<span id="currentScheduleLabel">{{$scheduleLabel}}</span>)</li>
                            <li>
                                Monthly Amount: K
                                <input type="number"
                                       id="currentMonthlyInput"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:120px; display:inline-block; vertical-align:middle;"
                                       value="{{ $monthlyAmount }}"
                                       step="0.01"
                                       min="0.01"
                                       required>
                            </li>
                            <li>
                                New Tenure (months):
                                <input type="number"
                                       name="new_long_term"
                                       id="newLongTermInput"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:80px; display:inline-block; vertical-align:middle;"
                                       value="{{ $tenure }}"
                                       min="1"
                                       max="60"
                                       step="1">
                            </li>
                            <li>Total Repayment: K{{ number_format($totalRepayment, 2) }}</li>
                        </ul>
                        <small class="text-muted" style="display:block; margin-top:5px;">
                            Adjust the monthly amount or enter a new tenure below.
                            A schedule entry will be added if none exists for the new tenure.
                        </small>
                    </div>

                    <div id="preview" style="display:none; margin-top:10px; padding:10px; background:#f8f9fa; border-radius:4px;">
                        <strong>Preview (Before Save):</strong>
                        <ul style="font-size:14px; margin:5px 0;">
                            <li>Tenure: <span id="previewTenure">-</span> months</li>
                            <li>Monthly Amount: <span id="previewMonthly">-</span></li>
                            <li>Total Repayment: <span id="previewTotal">-</span></li>
                        </ul>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-check"></i> Restructure Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var monthly     = {{ $monthlyAmount }};
    var startInput  = document.getElementById('bulkStartDateInput');
    var startStr    = startInput ? startInput.value : '{{ $loan->disbursement_date ?? now()->toDateString() }}';
    var input       = document.getElementById('numPaymentsInput');
    var preview     = document.getElementById('bulkPreview');
    var previewList = document.getElementById('bulkPreviewList');
    var months      = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    if (startInput) {
        startInput.addEventListener('change', function () {
            startStr = this.value;
        });
    }

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

(function () {
    var totalRepayment = {{ $totalRepayment ?? 0 }};
    var monthlyInput   = document.getElementById('currentMonthlyInput');
    var tenureDisplay  = document.getElementById('currentTenureDisplay');
    var newLongTermInput = document.getElementById('newLongTermInput');

    var previewTenure   = document.getElementById('previewTenure');
    var previewMonthly  = document.getElementById('previewMonthly');
    var previewTotal    = document.getElementById('previewTotal');
    var previewDiv      = document.getElementById('preview');

    var tenureHidden      = document.getElementById('tenureHidden');
    var monthlyAmountHidden = document.getElementById('monthlyAmountHidden');

    function formatMoney(v) {
        return 'K' + v.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function apply(monthly, tenure) {
        tenure = Math.max(1, Math.min(60, Math.floor(tenure) || 1));
        monthly = Math.max(0.01, parseFloat(monthly) || 0);

        if (monthlyInput) monthlyInput.value = monthly.toFixed(2);
        if (newLongTermInput) newLongTermInput.value = tenure;
        if (tenureDisplay) tenureDisplay.textContent = tenure;

        if (previewTenure) previewTenure.textContent = tenure;
        if (previewMonthly) previewMonthly.textContent = formatMoney(monthly);
        if (previewTotal) previewTotal.textContent = formatMoney(totalRepayment);
        if (previewDiv) previewDiv.style.display = 'block';

        if (tenureHidden) tenureHidden.value = tenure;
        if (monthlyAmountHidden) monthlyAmountHidden.value = monthly;
    }

    function recalculateTenure() {
        if (!monthlyInput || !totalRepayment || totalRepayment <= 0) return;

        var monthly = parseFloat(monthlyInput.value);
        if (!monthly || monthly <= 0) return;

        var tenure = Math.ceil(totalRepayment / monthly);
        monthly = totalRepayment / tenure;

        apply(monthly, tenure);
    }

    if (monthlyInput) {
        monthlyInput.addEventListener('input', recalculateTenure);
        recalculateTenure();
    }

    if (newLongTermInput) {
        newLongTermInput.addEventListener('input', function () {
            var newTenure = parseInt(this.value);
            if (!newTenure || isNaN(newTenure) || !totalRepayment || totalRepayment <= 0) return;

            var monthly = totalRepayment / newTenure;

            apply(monthly, newTenure);
        });
    }
})();
</script>

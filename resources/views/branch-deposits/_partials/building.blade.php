@php
    $officeId = Sentinel::getUser()->office_id;
    $selectedMonth = $selectedMonth ?? date('Y-m');
    
    // Fetch deposits using join between Deposit and BankDepositLog
    // deposits: id, deposit_type, office, amount, debt, date, status
    // bank_deposit_log: id, deposit_type, office_id, user_id, amount, deposit_method, deposit_id, reference_number, created_date
    $deposits = \App\Models\Deposit::join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
        ->where('bank_deposit_log.office_id', $officeId)
        ->where('bank_deposit_log.deposit_type', 3)
        ->whereRaw('DATE_FORMAT(bank_deposit_log.created_date, "%Y-%m") = ?', [$selectedMonth])
        ->select(
            'deposits.*',
            'bank_deposit_log.id as log_id',
            'bank_deposit_log.amount as log_amount',
            'bank_deposit_log.reference_number',
            'bank_deposit_log.deposit_method',
            'bank_deposit_log.created_date as deposit_date'
        )
        ->orderBy('bank_deposit_log.created_date', 'desc')
        ->get();
        
    // Get deposit type info
    $depositType = \App\Models\DepositType::find(3);
    $depositName = $depositType->name ?? 'Administration Department fee deposit';
    $monthlyRequired = $depositType->monthly_amount ?? 10000;
    
    // Calculate totals using log_amount from bank_deposit_log
    $totalAmount = $deposits->sum('log_amount');
    $balance = $monthlyRequired - $totalAmount;
    
    // Determine status
    $statusText = 'Not Paid';
    $statusColor = '#e74c3c';
    if ($totalAmount > 0 && $monthlyRequired > $totalAmount) {
        $statusText = 'Partially Paid';
        $statusColor = '#f39c12';
    } elseif ($totalAmount > 0 && $monthlyRequired <= $totalAmount) {
        $statusText = 'Fully Paid';
        $statusColor = '#27ae60';
    }
@endphp

<div class="deposit-item deposit-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
        <h4 class="deposit-title" style="margin:0;">{{ $depositName }}</h4>
        <span style="background:{{ $statusColor }};color:#fff;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;">{{ $statusText }}</span>
    </div>

    <div style="display: flex; flex-direction: row; gap: 10px; margin: 15px 0;">
        <div style="flex: 1; background: #e8f4fc; border-radius: 6px; padding: 12px 15px;">
            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Monthly Fee</small>
            <div style="color: #003366; font-weight: 700; font-size: 16px; margin-top: 4px;">K{{ number_format($monthlyRequired, 2) }}</div>
        </div>
        <div style="flex: 1; background: #f0f7f0; border-radius: 6px; padding: 12px 15px;">
            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Current Paid</small>
            <div style="color: #006600; font-weight: 700; font-size: 16px; margin-top: 4px;">K{{ number_format($totalAmount, 2) }}</div>
        </div>
        <div style="flex: 1; background: #fff3cd; border-radius: 6px; padding: 12px 15px;">
            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Balance</small>
            <div style="color: #856404; font-weight: 700; font-size: 16px; margin-top: 4px;">K{{ number_format(abs($balance), 2) }}</div>
        </div>
    </div>
    <!-- <div class="deposit-btns">
        <button class="this-month-btn btn btn-success btn-sm">This Month Deposit</button>
        <button class="deposit-history-btn btn btn-info btn-sm">Check Deposit History</button>
    </div> -->
    <label class="deposit-label">Payment Method</label>
    <select class="form-control payment-method">
        <option value="">Select Method</option>
        <option value="airtel">Airtel Money</option>
        <option value="zanaco_express">Zanaco Express</option>
        <option value="mtn">MTN MoMo</option>
        <option value="zanaco_cash">Zanaco Cash Deposit</option>
        <option value="access">Access</option>
        <option value="absa">Absa</option>
        <option value="withinhere">WithinHere</option>
        <option value="zanaco_online_transfer">Zanaco Online Transfer</option>
    </select>
    <br>
    <small class="text-muted format-hint">Enter Payment Reference Number</small>
    <input type="text" class="form-control reference" placeholder="Enter reference number" required>
    <br>
    <input type="number" class="form-control amount" placeholder="Enter amount to add" min="5000" step="0.01" required>
    <br>
    <button class="btn btn-primary complete-btn" style="min-width: 100px;">
        <span class="btn-text">Save Deposit</span>
        <span class="btn-loader" style="display: none; margin-left: 8px;">
            <i class="fa fa-spinner fa-spin"></i>
        </span>
    </button>
</div>




<style>
    .deposit-section {
        margin-bottom: 30px;
    }
    
    .info-box {
        min-height: 90px;
        background: #fff;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
    }
    
    .info-box-number {
        font-size: 22px;
        font-weight: bold;
    }
    
    .table > thead > tr > th {
        background-color: #3c8dbc;
        color: white;
    }
    
    .bg-light-blue {
        background-color: #d2e3f3 !important;
    }
</style>


<script>
$(document).ready(function() {
    var depositApiUrl = 'https://lms2backend.whencefinancesystem.com';
    var userId = {{ Sentinel::getUser()->id }};
    var branchId = {{ Sentinel::getUser()->office_id }};
    var currentDepositType = 3; // Building & Infrastructure
    var currentDepositTypeName = '{{ $depositName }}';

    /* ---------- PAYMENT METHOD FORMAT HINT ---------- */
    $(document).on('change', '.payment-method', function () {
        let box = $(this).closest('.deposit-item');
        let hint = box.find('.format-hint');
        let referenceInput = box.find('.reference');
        referenceInput.val('');

        switch ($(this).val()) {
            case 'airtel':
                hint.text('Format: MP260223.0953.J76581');
                referenceInput.attr('placeholder', 'MP260223.0953.J76581');
                break;
            case 'airtel_app':
                hint.text('Format: APCZM194947529952000');
                referenceInput.attr('placeholder', 'APCZM194947529952000');
                break;
            case 'zanaco_express':
                hint.text('Format: 12 digit number (002504072516)');
                referenceInput.attr('placeholder', '002504072516');
                break;
            case 'mtn':
                hint.text('Format: 10 digit number (8704564481)');
                referenceInput.attr('placeholder', '8704564481');
                break;
            case 'zanaco_cash':
                hint.text('Format: 16 digit number (0502605703255600)');
                referenceInput.attr('placeholder', '0502605703255600');
                break;
            case 'access':
                hint.text('Format: FJB2606341708208');
                referenceInput.attr('placeholder', 'FJB2606341708208');
                break;
            case 'absa':
                hint.text('Format: FJB2606341708208');
                referenceInput.attr('placeholder', 'FJB2606341708208');
                break;
            case 'withinhere':
                hint.text('Format: 1777356230718931');
                referenceInput.attr('placeholder', '1777356230718931');
                break;
            case 'zanaco_online_transfer':
                hint.text('Format: 002STAJ1234567MN');
                referenceInput.attr('placeholder', '002STAJ1234567MN');
                break;
            default:
                hint.text('Enter Payment Reference Number');
        }
    });

    /* ---------- VALIDATE REFERENCE FORMAT ---------- */
    function validateReferenceFormat(paymentMethod, reference) {
        let valid = false;
        switch (paymentMethod) {
            case 'airtel':
                valid = /^[A-Za-z]{2}\d{6}\.\d{4}\.[A-Za-z]\d{5}$/.test(reference);
                break;
            case 'airtel_app':
                valid = /^[A-Za-z]{5}\d{15}$/.test(reference);
                break;
            case 'zanaco_express':
                valid = /^\d{12}$/.test(reference);
                break;
            case 'mtn':
                valid = /^\d{10}$/.test(reference);
                break;
            case 'zanaco_cash':
                valid = /^\d{16}$/.test(reference);
                break;
            case 'access':
                valid = /^[A-Za-z]{3}\d{13}$/.test(reference);
                break;
            case 'absa':
                valid = /^[A-Za-z]{3}\d{10}$/.test(reference);
                break;
            case 'withinhere':
                valid = /^\d+$/.test(reference);
                break;
            case 'zanaco_online_transfer':
                valid = /^\d{3}[A-Za-z]{4}\d{7}[A-Za-z]{2}$/.test(reference);
                break;
        }
        return valid;
    }

    /* ---------- SAVE DEPOSIT BUTTON ---------- */
    $(document).on('click', '.complete-btn', function() {
        let $btn = $(this);
        let box = $btn.closest('.deposit-item');
        
        let raw = box.find('.amount').val();
        let currentDepositAmount = parseFloat(raw);
        let currentReferenceNumber = box.find('.reference').val().trim();
        let paymentMethod = box.find('.payment-method').val();

        if (!paymentMethod) {
            KiloAlert.warning('Please select a payment method.');
            return;
        }
        if (!currentReferenceNumber) {
            KiloAlert.warning('Please enter a payment reference number.');
            return;
        }
        if (isNaN(currentDepositAmount) || currentDepositAmount <= 0) {
            KiloAlert.warning('Enter a valid amount to add');
            return;
        }
        if (!validateReferenceFormat(paymentMethod, currentReferenceNumber)) {
            KiloAlert.warning('Invalid reference format for selected payment method.');
            return;
        }

        $btn.prop('disabled', true);
        $btn.find('.btn-text').hide();
        $btn.find('.btn-loader').show();

        function today() {
            let now = new Date();
            return now.toISOString().split('T')[0];
        }

        $.ajax({
            url: `${depositApiUrl}/create-deposit`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                deposit_type: currentDepositType,
                office: branchId,
                amount: currentDepositAmount,
                reference_number: currentReferenceNumber,
                deposit_method: paymentMethod,
                user_id: userId,
                date: today()
            }),
            success: function (res) {
                KiloAlert.success(res.message || 'Deposit saved successfully');
                box.find('.amount').val('');
                box.find('.reference').val('');
                box.find('.payment-method').val('');
                $btn.prop('disabled', false);
                $btn.find('.btn-text').show();
                $btn.find('.btn-loader').hide();

                fetch('https://notifications.whencefinancesystem.com/emit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        event: 'deposit.created',
                        data: {
                            created_by: '{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}',
                            office_id: '{{ Sentinel::getUser()?->office?->name }}',
                            amount: currentDepositAmount,
                            type: 'New Deposit requesting approval',
                            deposit: { type: currentDepositTypeName, reference: currentReferenceNumber, method: paymentMethod, date: today() }
                        }
                    })
                }).catch(error => console.log('Error sending notification:', error));

                setTimeout(function() { window.location.reload(); }, 2000);
            },
            error: function(res) {
                KiloAlert.error('Failed to save deposit. Please try again. ' + (res.responseJSON?.error || ''));
                $btn.prop('disabled', false);
                $btn.find('.btn-text').show();
                $btn.find('.btn-loader').hide();
            }
        });
    });
});
</script>

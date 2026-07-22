@php

$officeId = Sentinel::getUser()->office_id;
$depositTypeId = 6; // or whatever deposit type you need
$selectedMonth = $selectedMonth ?? date('Y-m');
// Validate format
if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
    // If it's in MM-YYYY format, convert it
    if (preg_match('/^(\d{2})-(\d{4})$/', $selectedMonth, $matches)) {
        $selectedMonth = $matches[2] . '-' . $matches[1]; // Convert to YYYY-MM
    } else {
        $selectedMonth = date('Y-m'); // Fallback to current month
    }
}
$deposits = \DB::table('deposits as d')
    ->leftJoin('deposit_types as dt', 'd.deposit_type', '=', 'dt.id')
    ->leftJoin('bank_deposit_log as bdl', function($join) {
        $join->on('bdl.deposit_id', '=', 'd.id')
             ->on('bdl.deposit_type', '=', 'd.deposit_type')
             ->on('bdl.office_id', '=', 'd.office')
             ->whereRaw('DATE_FORMAT(bdl.created_date, "%Y-%m") = DATE_FORMAT(d.date, "%Y-%m")');
    })
    ->where('d.deposit_type', $depositTypeId)
    ->where('d.office', $officeId)
    ->whereRaw('DATE_FORMAT(d.date, "%Y-%m") = ?', [$selectedMonth])
    ->select(
        'd.*',
        'dt.name as deposit_type_name',
        'dt.monthly_amount',
        'dt.bank',
        'dt.gl_account',
        'bdl.id as bank_deposit_log_id',
        'bdl.user_id as bank_deposit_log_user_id',
        'bdl.amount as bank_deposit_log_amount',
        'bdl.deposit_method as bank_deposit_log_method',
        'bdl.reference_number as bank_deposit_log_reference_number',
        'bdl.created_date as bank_deposit_log_created_date'
    )
    ->orderBy('dt.sort_order', 'asc')
    ->get();
    
    // Get deposit type info
    $depositType = \App\Models\DepositType::find(6);

    
    $depositName = $depositType->name ?? 'Administration Department fee deposit';
    $monthlyRequired = $depositType->monthly_amount ?? 0;
    
    // Calculate totals using log_amount from bank_deposit_log
    $totalAmount = $deposits->sum('amount');
    $approvalStatus = $deposits->isNotEmpty() && $deposits[0]->status ? 'Verified & Approved':'Pending verification '; 
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
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;background:linear-gradient(90deg, #e3f2fd 0%,#bbdefb 100%);padding:16px 20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);border:1px solid rgba(255,255,255,0.3);backdrop-filter:blur(10px);">
        <h4 class="deposit-title" style="margin:0;font-size:18px;font-weight:700;color:#1a237e;line-height:1.3;">{{ $depositName }}</h4>
        <span style="background:{{ $statusColor }};color:#fff;padding:8px 16px;border-radius:24px;font-size:12px;font-weight:600;min-width:90px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.15);">{{$approvalStatus ?? ''}} {{ $statusText }}</span>
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
            <div style="color: #856404; font-weight: 700; font-size: 16px; margin-top: 4px;">K{{ number_format(max(0, $balance), 2) }}</div>
        </div>
    </div>
    <!-- <div class="deposit-btns">
        <button class="this-month-btn btn btn-success btn-sm">This Month Deposit</button>
        <button class="deposit-history-btn btn btn-info btn-sm">Check Deposit History</button>
    </div> -->
    <label class="deposit-label">Payment Method</label>
    <select class="form-control payment-method" {{ $disabled ?? false ? 'disabled' : '' }}>
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
    <input type="text" class="form-control reference" placeholder="Enter reference number" required {{ $disabled ?? false ? 'readonly' : '' }}>
    <br>
    <input type="number" class="form-control amount" placeholder="Enter amount to add" min="100" step="0.01" required {{ $disabled ?? false ? 'readonly' : '' }}>
    <br>
    <button class="btn btn-primary complete-btn-5" style="min-width: 100px;" {{ $disabled ?? false ? 'disabled' : '' }}>
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
    var currentDepositType = 6;
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
    $(document).on('click', '.complete-btn-5', function() {
        let $btn = $(this);
        let box = $btn.closest('.deposit-item');
        
        // Get form values
        let raw = box.find('.amount').val();
        let currentDepositAmount = parseFloat(raw);
        let currentReferenceNumber = box.find('.reference').val().trim();
        let paymentMethod = box.find('.payment-method').val();

        // Validation
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

        // Validate reference format
        if (!validateReferenceFormat(paymentMethod, currentReferenceNumber)) {
            KiloAlert.warning('Invalid reference format for selected payment method.');
            return;
        }

        // Disable button and show loader
        $btn.prop('disabled', true);
        $btn.find('.btn-text').hide();
        $btn.find('.btn-loader').show();

        // Helper function for today's date
        function today() {
            let now = new Date();
            return now.toISOString().split('T')[0];
        }

        function getSelectedMonthDate(selectedMonth) {
            let parts = selectedMonth.split('-');
            let year = parseInt(parts[0]);
            let month = parseInt(parts[1]);
            let lastDay = new Date(year, month, 0).getDate();
            return year + '-' + String(month).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');
        }

        // Submit deposit
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
                date: getSelectedMonthDate('{{ $selectedMonth }}')
            }),
            success: function (res) {
                KiloAlert.success(res.message || 'Deposit saved successfully');

                // Clear form
                box.find('.amount').val('');
                box.find('.reference').val('');
                box.find('.payment-method').val('');

                // Reset button
                $btn.prop('disabled', false);
                $btn.find('.btn-text').show();
                $btn.find('.btn-loader').hide();

                // Send notification to WebSocket server
                fetch('https://notifications.whencefinancesystem.com/emit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        event: 'deposit.created',
                        data: {
                            created_by: '{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}',
                            office_id: '{{ Sentinel::getUser()?->office?->name }}',
                            amount: currentDepositAmount,
                            type: 'New Deposit requesting approval',
                            deposit: {
                                type: currentDepositTypeName,
                                reference: currentReferenceNumber,
                                method: paymentMethod,
                                date: today()
                            }
                        }
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Notification sent successfully:', data);
                })
                .catch(error => {
                    console.log('Error sending notification:', error);
                });

                // Reload page after 2 seconds
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
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

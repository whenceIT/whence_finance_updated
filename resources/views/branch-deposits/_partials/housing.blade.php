@php
    $officeId = Sentinel::getUser()->office_id;
    $selectedMonth = $selectedMonth ?? date('Y-m');
    
    // Fetch deposits using join between Deposit and BankDepositLog
    // deposits: id, deposit_type, office, amount, debt, date, status
    // bank_deposit_log: id, deposit_type, office_id, user_id, amount, deposit_method, deposit_id, reference_number, created_date
    $deposits = \App\Models\Deposit::join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
        ->where('bank_deposit_log.office_id', $officeId)
        ->where('bank_deposit_log.deposit_type', 2)
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
    $depositType = \App\Models\DepositType::find(2);

    
    $depositName = $depositType->name ?? 'Administration Department fee deposit';
    $monthlyRequired = $depositType->monthly_amount ?? 0;
    
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

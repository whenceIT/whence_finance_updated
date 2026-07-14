@extends('layouts.master')

@section('title')
    Branch Deposit Audit
@endsection
@php
    $officeIdParam = request('office_id');
@endphp
@section('content')
@include('components.kilo-alert')
<style>
    .da-type-card {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .da-type-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.14); }
    .da-type-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px;
        cursor: pointer;
        user-select: none;
        border-bottom: 1px solid transparent;
        transition: background .15s;
    }
    .da-type-header:hover { background: #f8f9ff; }
    .da-type-header .left { display: flex; align-items: center; gap: 12px; }
    .da-type-header .toggle-icon { font-size: 14px; color: #667eea; width: 18px; text-align: center; transition: transform .2s; }
    .da-type-card.open .da-type-header .toggle-icon { transform: rotate(90deg); }
    .da-type-header .type-name { font-weight: 700; font-size: 15px; color: #333; }
    .da-type-header .type-meta  { font-size: 13px; color: #888; margin-left: 8px; }
    .da-type-summary  { display: flex; gap: 20px; font-size: 13px; }
    .da-summary-item  { color: #555; }
    .da-summary-item strong { color: #667eea; }
    .right-group { display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
    .da-filter-bar {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding: 10px 16px; margin-bottom: 18px;
        background: #f4f6fb; border-radius: 7px;
        border: 1px solid #dde3ef;
    }
    .da-filter-bar label { font-size: 13px; font-weight: 600; color: #555; white-space: nowrap; }
    .da-filter-bar select {
        font-size: 13px; padding: 5px 8px; border: 1px solid #c7cfdf;
        border-radius: 4px; background: #fff; color: #444; outline: none;
    }
    .da-filter-bar select:focus { border-color: #667eea; }
    .da-custom-row { display: flex; align-items: center; gap: 6px; }
     .page-chrome { background: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #667eea; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
     .page-chrome h1  { margin: 0; font-size: 20px; font-weight: 700; }
     .page-chrome p   { margin: 4px 0 0; font-size: 13px; color: #666; }
     .page-chrome #openOfficeDebtModal { margin-top: 10px; }
     .sc-stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; margin-bottom: 24px; }
     .sc-card { border-radius: 10px; padding: 18px 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.15); }
     .sc-card-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; opacity: .7; margin-bottom: 10px; }
     .sc-row { font-size: 13px; margin-bottom: 3px; }
     .sc-row strong { font-weight: 600; }
     .sc-balance { font-size: 14px; font-weight: 700; margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.25); }
     .sc-card-debt { background: linear-gradient(135deg,#c0392b,#8e44ad); color: #fff; }
     .sc-card-dep  { background: linear-gradient(135deg,#667eea,#2c3e50); color: #fff; }
     .sc-card-debt .sc-row { opacity: .9; }
     .sc-card-debt .sc-card-title { opacity: .85; }

     /* Require-months info hint */
     .req-hint {
         display: inline-flex; align-items: center; justify-content: center;
         width: 18px; height: 18px; border-radius: 50%;
         background: #e8eaf6; color: #3949ab;
         font-size: 12px; font-weight: 700; font-style: normal;
         cursor: help; line-height: 1;
         font-family: serif;
     }

     /* Modal-fullscreen override */
    .od-dialog {
        width: 96vw;
        height: 92vh;
        margin: 3vh auto;
        padding: 0;
    }
    .od-dialog .modal-content {
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    .od-dialog .modal-header { background: #2c3e50; color: #fff; padding: 16px 24px; }
    .od-dialog .modal-header h4 { margin: 0; font-size: 16px; font-weight: 700; }
    .od-dialog .modal-header .close { color: #fff; opacity: .8; font-size: 26px; margin-top: -4px; }

    /* Debt table */
    #odTable { font-size: 13px; }
    #odTable thead th { background: #667eea; color: #fff; }
    #odTable tbody td { vertical-align: middle; }
    .od-debt-row td { border-top: 1px solid #eee; }

    /* Make the debt table vertically scrollable with sticky header */
    #odModal .table-responsive {
        max-height: 480px;
        overflow-y: auto;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }

    #odModal #odTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #667eea;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    /* Status pill badge styling */
    .od-status-pill {
        display: inline-block; padding: 2px 10px; border-radius: 12px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    }
    .od-status-pill.owing   { background: #fdecea; color: #c0392b; }
    .od-status-pill.partial { background: #fff8e1; color: #f39c12; }
    .od-status-pill.paid    { background: #eafaf1; color: #27ae60; }

    /* Form inside modal.body */
    .od-form-row { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; padding: 14px 16px; background: #f7f9fc; border: 1px solid #dde3ef; border-radius: 6px; }
    .od-form-group { display: flex; flex-direction: column; gap: 4px; flex: 1 1 180px; }
    .od-form-group label { font-size: 12px; font-weight: 600; color: #555; }
    .od-form-group input, .od-form-group select, .od-form-group textarea { padding: 7px 10px; border: 1px solid #c7cfdf; border-radius: 4px; font-size: 13px; outline: none; }
    .od-form-group input:focus, .od-form-group select:focus, .od-form-group textarea:focus { border-color: #667eea; }
    .od-editable-highlight { border: 3px solid #2196f3 !important; box-shadow: 0 0 0 3px rgba(33,150,243,0.25); }

    /* Drill-down office/deposit table */
    .da-office-row {
        border: 1px solid #e0e0e0;
        border-radius: 0px;
        margin-bottom: 12px;
        background: #fafafa;
        overflow: hidden;
    }
    .da-office-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #f0f4f8;
        border-bottom: 1px solid #e0e0e0;
    }
    .da-office-name {
        font-weight: 600;
        color: #333;
    }
    .da-office-months {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .da-month-box {
        width: 36px;
        height: 40px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
        position: relative;
        border: 1px solid #ddd;
    }
    .da-month-box.da-month-has-deposits {
        background: #27ae60;
        color: #fff;
        border-color: #27ae60;
    }
    .da-month-box.da-month-has-deposits .da-month-amount {
        font-size: 9px;
        color: #fff;
    }
    .da-month-box.da-month-past-no-deposits,
    .da-month-box.da-month-current-no-deposits {
        background: #ffebee;
        color: #c62828;
        border-color: #ffcdd2;
    }
    .da-month-box.da-month-future-no-deposits {
        background: transparent;
        color: #999;
        border-color: #ddd;
    }
    .da-month-amount {
        position: absolute;
        bottom: 3px;
        font-size: 8px;
    }
    .da-office-no-deposits {
        padding: 12px 14px;
        text-align: center;
        color: #888;
        font-style: italic;
    }

    /* Deposit drill-down shimmer loading */
    .da-body.da-loading {
        min-height: 80px;
        position: relative;
    }
    .da-shimmer-row {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 12px;
        background: #fafafa;
        padding: 10px 14px;
    }
    .da-shimmer-line {
        height: 12px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 400px 12px;
        animation: shimmer 1.2s ease-in-out infinite;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .da-shimmer-line.short { width: 40%; }
    .da-shimmer-line.medium { width: 60%; }
    .da-shimmer-line.long { width: 100%; }

    /* Action cell */
    .od-actions { display: flex; gap: 4px; }
    .od-btn { padding: 3px 8px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; }
    .od-btn-edit  { background: #fff3cd; color: #856404; }
    .od-btn-del   { background: #fdecea; color: #c0392b; }
    .od-btn-save  { background: #667eea; color: #fff; }
    .od-btn-cancel{ background: #eee; color: #555; }

    /* Shimmer */
    #odShimmer { display: none; padding: 20px 24px; }
    @keyframes shimmer-anim {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }
@keyframes shimmer {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .card-header {
        background: #f8f9ff;
        padding: 12px 16px;
        border-bottom: 1px solid #ddd;
        font-weight: 600;
    }
    .card-body {
        padding: 16px;
    }
    .label.label-success {
        background: #27ae60;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    .label.label-danger {
        background: #e74c3c;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

</style>

<div class="content-wrapper" style="margin: 20px;">

    <div class="page-chrome">
        <h1><i class="fa fa-history"></i> Branch Deposit Audit</h1>
        <p>Click a deposit type to expand and view all offices, including those with no deposits, for that type.</p>
        <!-- <a href="#odModal" id="openOfficeDebtModal" class="btn btn-primary btn-sm" style="border-radius:6px;text-decoration:none;color:#fff;">
            <i class="fa fa-balance-scale"></i>Edit Office Debt
        </a> -->
        <button type="button" id="openDepositQueryModal" class="btn btn-secondary btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-search"></i> Deposits Statements
        </button>
        <!-- <button type="button" id="openFailedDepositsModal" class="btn btn-danger btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-exclamation-triangle"></i> Failed Deposits
        </button> -->
        <!-- <button type="button" id="openSettingsModal" class="btn btn-outline-secondary btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-stop"></i> Exempt Offices
        </button> -->
        <button type="button" id="btnOpenDepositExemptModal" class="btn btn-outline-primary btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-calendar-times-o"></i> Exempt Months
        </button>
        <!-- <button type="button" id="activateAllOfficesBtn" class="btn btn-success btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-check-circle"></i> Activate Blocking for All Offices
        </button>
        <button type="button" id="deactivateAllOfficesBtn" class="btn btn-warning btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-ban"></i> Remove Blocking All Offices
        </button> -->
        <!-- <a href="{{ route('platform.block-skip-settings') }}" class="btn btn-outline-info btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-unlock"></i> Block Skip Settings
        </a> -->
        <!-- <button type="button" id="openDebtBalancesModal" class="btn btn-info btn-sm" style="border-radius:6px; margin-top:4px;">
            <i class="fa fa-plus-circle"></i> Record Debt Balance
        </button> -->
    </div>



    
    

    @include('risk.partials.deposit-query-modal', ['offices' => $offices ?? []])
    @include('risk.partials.failed-deposits-modal')


    <div class="da-filter-bar">
        <label><i class="fa fa-building"></i> Office</label>
        <select id="da-office-select">
            <option value="">All Offices</option>
            <?php if (isset($offices) && $offices): foreach ($offices as $office): ?>
                <option value="<?= $office->id ?>"><?= e($office->name) ?></option>
            <?php endforeach; endif; ?>
        </select>

        <label><i class="fa fa-filter"></i> Period</label>

        <?php
            $currentPeriod = $period ?? 'month';
            $sel = fn($v) => $currentPeriod === $v ? ' selected' : '';
        ?>
        <select id="da-period">
            <option value="overall"       {{ $sel('overall') }}>Overall</option>
            <option value="month"         {{ $sel('month') }}>This Month</option>
            <option value="quarter"       {{ $sel('quarter') }}>This Quarter</option>
            <option value="year"          {{ $sel('year') }}>This Year</option>
            <option value="this_circle"   {{ $sel('this_circle') }}>This Circle</option>
            <option value="last_circle"   {{ $sel('last_circle') }}>Last Circle</option>
            <option value="last_quarter"  {{ $sel('last_quarter') }}>Last Quarter</option>
            <option value="last_month"    {{ $sel('last_month') }}>Last Month</option>
            <option value="last_year"     {{ $sel('last_year') }}>Last Year</option>
            <option value="custom"        {{ $sel('custom') }}>Custom…</option>
        </select>

        <div id="da-custom-row" class="da-custom-row" style="display:<?= $currentPeriod === 'custom' ? 'flex' : 'none' ?>">
            <label style="font-size:11px;white-space:nowrap;margin-right:2px;">From</label>
            <input type="date" id="da-start-date" value="<?= e($startDate ?? '') ?>" style="font-size:12px;padding:2px 6px;border:1px solid #c7cfdf;border-radius:3px;">

            <label style="font-size:11px;white-space:nowrap;margin:0 2px 0 8px;">To</label>
            <input type="date" id="da-end-date" value="<?= e($endDate ?? '') ?>" style="font-size:12px;padding:2px 6px;border:1px solid #c7cfdf;border-radius:3px;">
        </div>
    </div>

    @php
        $periodLabels = [
            'overall'      => 'Overall (Jan → 28th of current month)',
            'month'        => 'This Month',
            'quarter'      => 'This Quarter',
            'year'         => 'This Year',
            'last_month'   => 'Last Month',
            'last_quarter' => 'Last Quarter',
            'last_year'    => 'Last Year',
            'this_circle'  => 'This Circle',
            'last_circle'  => 'Last Circle',
            'custom'       => 'Custom',
        ];
        $p = $period ?? 'month';
        $periodText = $periodLabels[$p] ?? ucfirst(str_replace('_', ' ', $p));
        if ($p === 'custom') {
            $mNames = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $periodText = 'Custom: ' . ($mNames[$customMonth ?? 0] ?? '') . ' ' . ($customYear ?? '');
        }
    @endphp

    <div style="margin: 0 0 14px 0; font-size:12px; color:#334155; background:#e0e7ff; padding:6px 10px; border-radius:4px; display:flex; gap:16px; flex-wrap:wrap;">
        <span><strong>Office:</strong> {{ $selectedOfficeName ?? 'All Offices' }}</span>
        <span><strong>Period:</strong> {{ $periodText }}</span>
    </div>

     <!-- Display stats cards here -->

      <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
        <span style="font-size:13px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;">Deposit Compliance</span>
        <span class="req-hint" title="
            Required = monthly_amount × offices × months_span

            Period → months counted in Required:
            overall    Jan 1 → last day of last month
            month          1 month
            quarter        3 months
            year          12 months
            last_month     1 month
            last_quarter   3 months
            last_year     12 months
            this_circle    ~1 month
            last_circle    ~1 month

            Received = actual deposits matching the period filter
            ">&#9432;
        </span>
      </div>

      <div class="sc-stats-grid">
          <!-- Outstanding Branch Debt -->
          <div class="sc-card sc-card-debt">
                
              <div class="sc-card-title">Outstanding Branch Debt</div>
              <i>This will display balances in Building, Administration and Statutory</i>
               <!--<div class="sc-row">Debt Accumulated&nbsp;&nbsp;<strong>K{{ number_format((int)$debtCards['accumulated'], 2) }}</strong></div>
              <div class="sc-row">Debt Amount Repaid&nbsp;&nbsp;<strong>K{{ number_format((int)$debtCards['paid'], 2) }}</strong></div>
              <div class="sc-balance">Balance&nbsp;&nbsp;<strong>K{{ number_format((int)$debtCards['balance'], 2) }}</strong></div> -->
          </div>

           <?php
               // Ensure cards appear in the order defined by DepositType.sort_order
               usort($depositCardStats, function ($a, $b) {
                   return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
               });

               $statsCount = count($depositCardStats);
           ?>
           <?php $idx = 0; ?>
           <?php foreach ($depositCardStats as $s): ?>
                
                <?php
                    $isSpecial = ($statsCount > 3) && ($idx >= ($statsCount - 3));
                    $req = (int) $s['required'];
                    $rec = (int) $s['received'];
                    $other = (int) $s['other'];
                    $bal = (int) $s['balance'];
                    $displayBal = max(0, $bal); // Show 0 or positive only
                    $excess = $bal < 0 ? abs($bal) : 0; // Calculate excess if negative
                ?>

                <div class="sc-card sc-card-dep">
                    <div class="sc-card-title">{{ $s['label'] }}</div>

                    <?php if (!$isSpecial): ?>
                        <div class="sc-row">Required&nbsp;&nbsp;<strong>K{{ number_format($req, 2) }}</strong></div>
                    <?php endif; ?>

                    <div class="sc-row">Received&nbsp;&nbsp;<strong>K{{ $rec == 0 ? number_format($other, 2) : number_format($rec, 2) }}</strong></div>

                    <?php if (!$isSpecial): ?>
                        <div class="sc-balance">
                            Balance&nbsp;&nbsp;<strong{{ $displayBal > 0 ? ' style="color:#e61700"' : '' }}>K{{ number_format($displayBal, 2) }}</strong>
                            <?php if ($excess > 0): ?>
                                <div style="font-size:11px; color:#27ae60; margin-top:4px;">
                                    <i class="fa fa-arrow-up" style="margin-right:2px;"></i>
                                    Excess: K{{ number_format($excess, 2) }}
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php $idx++; ?>
            <?php endforeach; ?>
<!-- Totals card -->
            <?php
                $tR = (int) $depositCardTotals['required'];
                $tC = (int) $depositCardTotals['received'];
                $tOther = (int) $depositCardTotals['other'];
                $tGT = (int) $depositCardTotals['grand_total'];
                $tB = (int) $depositCardTotals['balance'];
            ?>
            <div class="sc-card sc-card-dep" style="border: 2px solid rgba(255,255,255,0.45);">
                <div class="sc-card-title">{{ $depositCardTotals['label'] }}</div>
                <div class="sc-row">Required&nbsp;&nbsp;<strong>K{{ number_format($tR, 2) }}</strong></div>
                <div class="sc-row">Received&nbsp;&nbsp;<strong>K{{ number_format($tC, 2) }}</strong></div>
                <div class="sc-row">Other Received&nbsp;&nbsp;<strong>K{{ number_format($tOther, 2) }}</strong></div>
                <div class="sc-balance">
                    Balance&nbsp;&nbsp;<strong{{ $tB > 0 ? ' style="color:#e61700"' : '' }}>K{{ number_format($tR - $tC, 2) }}</strong>
                    <br>
                    <small> <i>(Required - Received)</i> </small>
                </div>
                <div class="sc-balance">
                    Grand Total&nbsp;&nbsp;<strong{{ $tGT > 0 ? ' style="color:#e61700"' : '' }}>K{{ number_format($tGT, 2) }}</strong>
                    <br>
                    <small> <i>(Received + Other Received)</i> </small>
            </div>
            </div>
            </div>



    <div id="daContainer">
        <div class="da-type-card bg-danger" data-type-id="debt">
             <div class="da-type-header">
                 <div class="left">
                     <span class="toggle-icon"><i class="fa fa-caret-right"></i></span>
                     <span class="type-name">Debt Repayment (For Branches in Debt)</span>
                     <span class="type-meta">All</span>
                 </div>
                 <div class="right-group">
                     <div class="da-stats">
                         <span class="da-stat" title="Total debt records">
                         <!-- <span class="da-stat" title="Total debt records">
                             <i class="fa fa-building"></i> <strong>{{ \App\Models\OfficeDebt::count() }}</strong> records
                         </span>
                         <span class="da-stat" title="Offices with outstanding debt">
                             <i class="fa fa-exclamation-circle" style="color:#c0392b"></i> <strong>{{ \App\Models\OfficeDebt::where('outstanding_amount', '>', 0)->count() }}</strong> with outstanding
                         </span>
                         </span> -->
                         <!-- <span class="da-stat" title="Total outstanding debt across all branches">
                             <i class="fa fa-line-chart" style="color:#c0392b"></i> <strong>K{{ number_format((int)\App\Models\OfficeDebt::sum('outstanding_amount'), 0) }}</strong> outstanding
                         </span> -->
                         <i class="fa fa-exclamation-circle" style="color:#c0392b"></i>
                    </div>
              </div>
              </div>
         </div>

@foreach($types as $t)
            @php
                $tid = $t['id'];
                $tname = $t['name'];
                $tbank = $t['bank'] ?? '–';
                $tgl = $t['gl_account'] ?? '–';
                $tcount = $t['office_count'];
                $withDep = $t['offices_with_deposits'];
                $ttotal = $t['total_amount'];
            @endphp
            <div class="da-type-card" data-type-id="{{ $tid }}">
                <div class="da-type-header">
                    <div class="left">
                        <span class="toggle-icon"><i class="fa fa-caret-right"></i></span>
                        <span class="type-name">{{ $tname }}</span>
                    </div>
                    <div class="right-group">
                        <div class="da-stats">
                            
                        </div>
                    </div>
                </div>
                <div class="da-body" id="da-body-{{ $tid }}">
                </div>
            </div>
        @endforeach
    </div>

    <!-- Debt Balances Modal -->
    <div class="modal fade" id="debtBalancesModal" tabindex="-1" role="dialog" aria-labelledby="debtBalancesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #667eea; color: #fff;">
                    <h5 class="modal-title" id="debtBalancesModalLabel">
                        <i class="fa fa-plus-circle"></i> Record Debt Balance
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <form id="debtBalancesForm">
                        @csrf
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="debtBalanceOffice" style="font-weight: 600; margin-bottom: 6px;">
                                <i class="fa fa-building"></i> Office <span style="color: #c0392b;">*</span>
                            </label>
                            <select id="debtBalanceOffice" name="office_id" class="form-control" required style="border-radius: 4px;">
                                <option value="">-- Select an Office --</option>
                                <?php if (isset($offices) && $offices): ?>
                                    <?php foreach ($offices as $office): ?>
                                        <option value="<?= $office->id ?>"><?= e($office->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="debtBalanceDepositType" style="font-weight: 600; margin-bottom: 6px;">
                                <i class="fa fa-money"></i> Deposit Type <span style="color: #c0392b;">*</span>
                            </label>
                            <select id="debtBalanceDepositType" name="deposit_type_id" class="form-control" required style="border-radius: 4px;">
                                <option value="">-- Select a Deposit Type --</option>
                                <?php if (isset($depositTypes) && $depositTypes): ?>
                                    <?php foreach ($depositTypes as $dt): ?>
                                        <option value="<?= $dt->id ?>"><?= e($dt->name) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="debtBalanceAmount" style="font-weight: 600; margin-bottom: 6px;">
                                <i class="fa fa-calculator"></i> Balance Amount <span style="color: #c0392b;">*</span>
                            </label>
                            <input type="number" id="debtBalanceAmount" name="balance" class="form-control" required 
                                   min="0" step="0.01" placeholder="Enter balance amount" style="border-radius: 4px;">
                            <small style="color: #888; margin-top: 4px;">Amount in currency</small>
                        </div>

                        <div style="display: flex; gap: 8px; margin-top: 20px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1; border-radius: 4px;">
                                <i class="fa fa-save"></i> Save Debt Balance
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="flex: 1; border-radius: 4px;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('risk.partials.exemption-list-table')

<script>(function(){

var csrf = (function(){
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    })();

    // Period filter — wired via addEventListener so the handler is in the same scope
    (function() {
        var period  = document.getElementById('da-period');
        if (!period) return;

        period.addEventListener('change', function() {
            var value    = period.value;
            var customR  = document.getElementById('da-custom-row');
            var officeEl = document.getElementById('da-office-select');
            var params   = new URLSearchParams({'period': value});

            // Carry office_id across period changes
            if (officeEl && officeEl.value) {
                params.set('office_id', officeEl.value);
            }

            if (value === 'custom') {
                var startEl = document.getElementById('da-start-date');
                var endEl   = document.getElementById('da-end-date');
                if (startEl && startEl.value) params.set('start_date', startEl.value);
                if (endEl && endEl.value)     params.set('end_date', endEl.value);
                customR.style.display = 'flex';
            } else {
                customR.style.display = 'none';
            }

            window.location.href = '?' + params.toString();
        });

        // Init on page load — show/hide custom row based on current selection
        var customR = document.getElementById('da-custom-row');
        if (customR) {
            customR.style.display = period.value === 'custom' ? 'flex' : 'none';
        }
    })();

    // ── Custom date range (start/end) — live reload when changed while on "custom" ──
    (function() {
        var start = document.getElementById('da-start-date');
        var end   = document.getElementById('da-end-date');
        if (!start || !end) return;

        function applyCustomRange() {
            var params = new URLSearchParams(window.location.search);
            params.set('period', 'custom');

            if (start.value) params.set('start_date', start.value);
            else             params.delete('start_date');

            if (end.value) params.set('end_date', end.value);
            else           params.delete('end_date');

            // Carry current office filter
            var officeEl = document.getElementById('da-office-select');
            if (officeEl && officeEl.value) {
                params.set('office_id', officeEl.value);
            }

            window.location.href = '?' + params.toString();
        }

        start.addEventListener('change', applyCustomRange);
        end.addEventListener('change', applyCustomRange);
    })();

    // ── Office filter ───────────────────────────────────────────────────────────
    (function() {
        var officeSel = document.getElementById('da-office-select');
        if (!officeSel) return;

        // Pre-select from URL params
        var params  = new URLSearchParams(window.location.search);
        var urlOffice = params.get('office_id');
        if (urlOffice) {
            officeSel.value = urlOffice;
        }

        officeSel.addEventListener('change', function() {
            var params = new URLSearchParams(window.location.search);
            var office = officeSel.value;

            if (office) {
                params.set('office_id', office);
            } else {
                params.delete('office_id');
            }

            // Always reload — debts always need fresh API data for any filter change
            window.location.href = window.location.pathname + '?' + params.toString();
        });
    })();

    // ── OfficeDebt Management ──────────────────────────────────────────────────
    (function() {
        // Markers (dynamically resolved at call time, not at IIFE init, to avoid stale empty jQuery sets)
        var editId    = function() { return $('#odEditId').val(); };

        // In-memory data for search + export
        var odAllRows = [];

        // Open modal → load data (delegated: survives second jQuery load in master layout)
        $(document).on('click', '#openOfficeDebtModal', function(e) {
            e.preventDefault();
            try {
                // Re-resolve $modal at click time so the element is in the DOM
                var $liveModal = $('#odModal');
                if (!$liveModal.length) { console.error('odModal not found in DOM'); return; }
                odResetForm();
                odLoadTable();
                $liveModal.modal('show');
                console.log('odModal shown, length:', $liveModal.length);
            } catch(e) { console.error('Modal open error:', e.message, e.stack); }
        });

        function odResetForm() {
            $('#odInputOffice').val('');
            $('#odInputDepositType').val('');
            $('#odInputMonth').val('');
            $('#odInputYear').val('');
            $('#odInputStatus').val('owing');
            $('#odInputOriginal').val('');
            $('#odInputOutstanding').val('');
            $('#odInputNotes').val('');
            $('#odEditId').val('');
            $('#odFormBar').hide();

            // Re-enable all fields for new records
            $('#odInputOffice, #odInputDepositType, #odInputMonth, #odInputYear, #odInputStatus, #odInputOriginal, #odInputOutstanding, #odInputNotes')
                .prop('disabled', false);
            $('#odInputOutstanding').removeClass('od-editable-highlight');

            odShowList();
            // empty visibility managed by odLoadTable when needed
        }

        function odShowList() {
            $('#odFormBar').hide();
            $('#odListHeader').show();
            $('.table-responsive').show();
        }

        function odShowForm(isEdit) {
            $('#odListHeader').hide();
            $('.table-responsive').hide();
            $('#odEmpty').hide();

            var $fb = $('#odFormBar');
            $('#odFormTitle').text(isEdit ? 'Edit Debt Record' : 'Add New Debt Record');

            if (!isEdit) {
                // Prefill with current debt period values for better UX
                var now = new Date();
                var currentMonth = now.getMonth() + 1;
                var currentYear = now.getFullYear();
                $('#odInputMonth').val(currentMonth);
                $('#odInputYear').val(currentYear);
                if (!$('#odInputStatus').val()) {
                    $('#odInputStatus').val('owing');
                }
            }

            // In EDIT mode: Outstanding (thick blue highlight) + Notes are editable.
            // All other fields (incl. Original Debt) are locked.
            // Server-side: if outstanding > original then original is bumped to match;
            // if lower then the delta is recorded as a deposit.
            const editing = !!isEdit;
            $('#odInputOffice, #odInputDepositType, #odInputMonth, #odInputYear, #odInputStatus, #odInputOriginal')
                .prop('disabled', editing);
            $('#odInputOutstanding')
                .prop('disabled', false)
                .toggleClass('od-editable-highlight', editing);

            $fb.show();
            $fb[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // ── Load table ──
        function odLoadTable() {
            var $tableBody = $('#odTableBody');
            var $empty     = $('#odEmpty');
            var $shimmer   = $('#odShimmer');

            $tableBody.empty();
            $empty.hide();
            $shimmer.show();

            $.get('{{ route("risk.office-debts.list") }}', function(resp) {
                $shimmer.hide();
                odAllRows = resp || [];

                // Clear any previous search
                $('#odSearchInput').val('');

                if (odAllRows.length === 0) {
                    $empty.show();
                    return;
                }

                odRenderDebtTable(odAllRows);
            }).fail(function() {
                $shimmer.hide();
                $tableBody.html('<tr><td colspan="8" style="padding:20px;text-align:center;color:#c0392b;">Error loading debt records. Try again.</td></tr>');
            });
        }

        // ── Row HTML ──
        function odRowHtml(row) {
            var amountClass = function() {
                if (row.outstanding_amount <= 0) return 'paid';
                if (row.outstanding_amount < row.original_amount) return 'partial';
                return 'owing';
            };
            var cls = amountClass();
            var balance = row.outstanding_amount <= 0
                ? '—'
                : (function() {
                    try { return parseFloat(row.outstanding_amount).toLocaleString('en-US', { style: 'currency', currency: 'ZMW' }); }
                    catch(e) { return row.outstanding_amount; }
                  })();
            var original = (function() {
                try { return parseFloat(row.original_amount).toLocaleString('en-US', { style: 'currency', currency: 'ZMW' }); }
                catch(e) { return row.original_amount; }
            })();

            var monthLabel = row.debt_month && row.debt_year
                ? (function() {
                      var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                      return (m[row.debt_month - 1] || row.debt_month) + ' ' + row.debt_year;
                  })()
                : '—';

            return '<tr class="od-debt-row" data-id="' + row.id + '">'
                 + '<td>' + (row.office_name || '—') + '</td>'
                 + '<td>' + (row.deposit_type_name || '—') + '</td>'
                 + '<td>' + monthLabel + '</td>'
                 + '<td><span class="od-status-pill ' + cls + '">' + row.debt_status + '</span></td>'
                 + '<td>' + original + '</td>'
                 + '<td style="font-weight:700;color:' + (cls === 'owing' ? '#c0392b' : (cls === 'partial' ? '#f39c12' : '#27ae60')) + ';">' + balance + '</td>'
                 + '<td style="color:#777;font-size:12px;">' + (row.notes || '') + '</td>'
                 + '<td class="od-actions">'
                 + '<button class="od-btn od-btn-edit"  title="Edit"   onclick="odEdit(' + row.id + ')"><i class="fa fa-pencil"></i></button> '
                //  + '<button class="od-btn od-btn-del"   title="Delete" onclick="odDel(' + row.id + ')"><i class="fa fa-trash"></i></button>'
                 + '</td>'
                  + '</tr>';
         }

         // ── Render (with optional filter) ──
         function odRenderDebtTable(rows) {
             var $tableBody = $('#odTableBody');
             $tableBody.empty();

             if (!rows || rows.length === 0) {
                 $tableBody.html('<tr><td colspan="8" style="padding:20px;text-align:center;color:#888;">No matching records</td></tr>');
                 return;
             }

             rows.forEach(function(row) {
                 $tableBody.append(odRowHtml(row));
             });
         }

         // ── Export Helper (CSV, opens as Excel) ──
         function odExportToCSV(rows) {
             if (!rows || rows.length === 0) return;

             var headers = ['Branch', 'Deposit Type', 'Month/Year', 'Status', 'Original', 'Outstanding', 'Notes'];
             var csvRows = [];

             // Header row
             csvRows.push(headers.join(','));

             rows.forEach(function(row) {
                 var monthLabel = row.debt_month && row.debt_year
                     ? (['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][row.debt_month - 1] || row.debt_month) + ' ' + row.debt_year
                     : '';

                 var line = [
                     '"' + (row.office_name || '').replace(/"/g, '""') + '"',
                     '"' + (row.deposit_type_name || '').replace(/"/g, '""') + '"',
                     '"' + monthLabel + '"',
                     '"' + (row.debt_status || '') + '"',
                     row.original_amount || 0,
                     row.outstanding_amount || 0,
                     '"' + (row.notes || '').replace(/"/g, '""') + '"'
                 ];
                 csvRows.push(line.join(','));
             });

             var csvContent = csvRows.join('\n');
             var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
             var link = document.createElement('a');
             var url = URL.createObjectURL(blob);
             link.href = url;
             link.download = 'office_debts_' + new Date().toISOString().slice(0,10) + '.csv';
             link.style.visibility = 'hidden';
             document.body.appendChild(link);
             link.click();
             document.body.removeChild(link);
         }

         // ── New Record ──
          $(document).on('click', '#odBtnNewRow', function() {
              odResetForm();
              odShowForm(false);
           });

           // ── Live Search Filter ──
         $(document).on('input', '#odSearchInput', function() {
             var term = $(this).val().toLowerCase().trim();

             if (!odAllRows || odAllRows.length === 0) return;

             var filtered = odAllRows.filter(function(row) {
                 return (row.office_name || '').toLowerCase().includes(term) ||
                        (row.deposit_type_name || '').toLowerCase().includes(term) ||
                        (row.debt_status || '').toLowerCase().includes(term) ||
                        (row.notes || '').toLowerCase().includes(term);
             });

             odRenderDebtTable(filtered);
         });

        // ── Export to Excel (CSV) ──
          $(document).on('click', '#odBtnExport', function() {
              if (!odAllRows || odAllRows.length === 0) {
                  KiloAlert.info('No data to export.');
                  return;
              }

             // Use currently visible rows if search is active, otherwise all
             var searchTerm = ($('#odSearchInput').val() || '').toLowerCase().trim();
             var dataToExport = odAllRows;

             if (searchTerm) {
                 dataToExport = odAllRows.filter(function(row) {
                     return (row.office_name || '').toLowerCase().includes(searchTerm) ||
                            (row.deposit_type_name || '').toLowerCase().includes(searchTerm) ||
                            (row.debt_status || '').toLowerCase().includes(searchTerm) ||
                            (row.notes || '').toLowerCase().includes(searchTerm);
                 });
             }

             odExportToCSV(dataToExport);
         });

         // ── Cancel new/edit ──
        $(document).on('click', '#odBtnCancelForm', function() {
            odResetForm();
            odShowList();
        });

        // ── Back to list link in form bar ──
        $(document).on('click', '#odBackToList', function(e) {
            e.preventDefault();
            odResetForm();
            odShowList();
        });

        // ── Edit row ──
        window.odEdit = function(id) {
            $.get('{{ route("risk.office-debts.list") }}', function(resp) {
                var row = resp.find(function(r) { return r.id === id; });
                if (!row) return;

                $('#odEditId').val(id);
                $('#odInputOffice').val(row.office_id);
                $('#odInputDepositType').val(row.deposit_type_id || '');

                // Prefill Month and Year from the OfficeDebt model's debt_month / debt_year
                if (row.debt_month) {
                    $('#odInputMonth').val(row.debt_month);
                } else if (row.created_at) {
                    var created = new Date(row.created_at);
                    $('#odInputMonth').val(created.getMonth() + 1);
                } else {
                    $('#odInputMonth').val('');
                }

                if (row.debt_year) {
                    $('#odInputYear').val(row.debt_year);
                } else if (row.created_at) {
                    var created = new Date(row.created_at);
                    $('#odInputYear').val(created.getFullYear());
                } else {
                    $('#odInputYear').val('');
                }

                $('#odInputStatus').val(row.debt_status);
                $('#odInputOriginal').val(row.original_amount);
                $('#odInputOutstanding').val(row.outstanding_amount);
                $('#odInputNotes').val(row.notes);
                window.currentOdIsSetupDebt = row.is_setup_debt || 'false';
                odShowForm(true);

                // Scroll form into view
                var $tBody = $('#odTableBody');
                $tBody.find('tr[data-id="' + id + '"]')[0] && $tBody.find('tr[data-id="' + id + '"]')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        };

        // ── Delete row (soft-clear → hard-delete after double-confirm) ──
        window.odDel = function(id) {
            if (!confirm('Remove this debt record? The branch will no longer appear as carrying debt.')) return;
            $.ajax({
                url: '{{ route("risk.office-debts.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
            }).done(function(r) {
                if (r.success) odLoadTable();
                else KiloAlert.error(r.message || 'Unable to delete record.');
            }).fail(function() {
                KiloAlert.error('Network error. Try again.');
            });
        };

        // ── Save (create / update) ──
        $(document).on('click', '#odBtnSaveForm', function() {
            var officeId      = $('#odInputOffice').val();
            var depositTypeId = $('#odInputDepositType').val();
            var month         = $('#odInputMonth').val();
            var year          = $('#odInputYear').val();
            var status        = $('#odInputStatus').val();
            var original      = $('#odInputOriginal').val();
            var outstanding   = $('#odInputOutstanding').val();
            var notes         = $('#odInputNotes').val();
            var isSetupDebt   = window.currentOdIsSetupDebt || 'false';

            if (!officeId || !original || outstanding === '') {
                KiloAlert.warning('Please fill in Branch, Original Amount and Outstanding Amount.');
                return;
            }

            var id      = editId();
            var url     = id
                ? '{{ route("risk.office-debts.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
                : '{{ route("risk.office-debts.store") }}';
            var type    = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: type,
                data: {
                    _token:              '{{ csrf_token() }}',
                    office_id:           officeId,
                    deposit_type_id:     depositTypeId || null,
                    debt_month:          month ? parseInt(month) : null,
                    debt_year:           year  ? parseInt(year)  : null,
                    debt_status:         status,
                    original_amount:     original,
                    outstanding_amount:  outstanding,
                    notes:               notes,
                    is_setup_debt:       isSetupDebt,
                },
            }).done(function(r) {
                if (r.success) {
                    odResetForm();
                    odShowList();
                    odLoadTable();
                } else {
                    KiloAlert.error(r.message || 'Save failed.');
                }
            }).fail(function(xhr) {
                var msg = 'Save failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 409) {
                    msg = 'This monthly debt record already exists for the selected office and deposit type.';
                }
                KiloAlert.error(msg);
            });
        });

    })();

})();
</script>




<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="settingsModalLabel">
                    <i class="fa fa-cog"></i> Platform Settings
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="settingsForm">
                    <input type="hidden" id="settingsId" value="">
                    <div class="form-group">
                        <label>Office</label>
                        <select id="settingsOffice" class="form-control">
                            <option value="">Select an office…</option>
                            <?php
                                $offices = \App\Models\Office::orderBy('name')->get();
                                foreach ($offices as $o) {
                                    echo '<option value="' . $o->id . '">' . htmlspecialchars($o->name) . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="admin" value="1" id="admin_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="admin" value="0" id="admin_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Building</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="building" value="1" id="building_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="building" value="0" id="building_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Statutory</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="statutory" value="1" id="statutory_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="statutory" value="0" id="statutory_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Set up debt</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="set_up_debt" value="1" id="set_up_debt_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="set_up_debt" value="0" id="set_up_debt_0"> Enable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '#openSettingsModal', function() {
    $('#settingsModal').modal('show');
    loadSettings();
});

function loadSettings(officeId) {
    var url = '/settings/platform/get';
    if (officeId) url += '?office_id=' + officeId;
    $.get(url, function(data) {
        $('#settingsId').val(data.id || '');
        $('#settingsOffice').val(data.office_id || '');
        $('input[name="admin"][value="' + (data.admin ? '0' : '1') + '"]').prop('checked', true);
        $('input[name="building"][value="' + (data.building ? '0' : '1') + '"]').prop('checked', true);
        $('input[name="statutory"][value="' + (data.statutory ? '0' : '1') + '"]').prop('checked', true);
        $('input[name="set_up_debt"][value="' + (data.set_up_debt ? '0' : '1') + '"]').prop('checked', true);
    });
}

$('#settingsOffice').on('change', function() {
    loadSettings($(this).val());
});

$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    var data = {
        _token: '{{ csrf_token() }}',
        id: $('#settingsId').val(),
        office_id: $('#settingsOffice').val(),
        admin: $('input[name="admin"]:checked').val() || 0,
        building: $('input[name="building"]:checked').val() || 0,
        statutory: $('input[name="statutory"]:checked').val() || 0,
        set_up_debt: $('input[name="set_up_debt"]:checked').val() || 0,
    };
    $.post('/settings/platform/save', data, function(res) {
        KiloAlert.success(res.message || 'Saved');
        $('#settingsModal').modal('hide');
    }).fail(function() {
        KiloAlert.error('Save failed.');
    });
});

$('#activateAllOfficesBtn').on('click', function() {
    if (!confirm('Initialize branch deposit settings for all offices with default values (enabled)?')) return;
    $.post('/settings/platform/initialize-all', {
        _token: '{{ csrf_token() }}'
    }, function(res) {
        KiloAlert.success(res.message || 'Initialized');
    }).fail(function() {
        KiloAlert.error('Failed to initialize.');
    });
});

$('#deactivateAllOfficesBtn').on('click', function() {
    if (!confirm('Deactivate branch deposit settings for all offices? This will remove all custom settings.')) return;
    $.post('/settings/platform/deactivate-all', {
        _token: '{{ csrf_token() }}'
    }, function(res) {
        KiloAlert.success(res.message || 'Deactivated');
    }).fail(function() {
        KiloAlert.error('Failed to deactivate.');
    });
});

function loadOfficesSettings() {
    var officeId = new URLSearchParams(window.location.search).get('office_id');
    var url = '/settings/platform/offices-settings';
    if (officeId) url += '?office_id=' + officeId;
    
    $.get(url, function(data) {
        if (officeId && typeof renderExemptions === 'function') {
            renderExemptions(data);
            return;
        }
        
        var tableHtml = '';
        data.forEach(function(o) {
            tableHtml += '<tr>' +
                '<td>' + o.name + '</td>' +
                '<td>' + o.code + '</td>' +
                '<td>' + (o.admin ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.building ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.statutory ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.set_up_debt ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '</tr>';
        });
        $('#offices-settings-table').html(tableHtml);
    }).fail(function() {
        if (officeId) {
            $('#office-detail-card').html('<div class="col-md-12" style="text-align:center;color:#c0392b;">Failed to load settings.</div>');
        } else {
            $('#offices-settings-table').html('<tr><td colspan="6" style="text-align:center;color:#c0392b;">Failed to load settings.</td></tr>');
        }
    });
}

$(document).on('click', '#openExemptionListModal', function() {
    $('#exemptionListModal').modal('show');
    loadOfficesSettings();
});

document.getElementById('openDepositQueryModal').addEventListener('click', function() {
    document.getElementById('depositQueryModal').style.display = 'block';
});

document.getElementById('closeDepositQueryModal').addEventListener('click', function() {
    document.getElementById('depositQueryModal').style.display = 'none';
});

// document.getElementById('openFailedDepositsModal').addEventListener('click', function() {
//     document.getElementById('failedDepositsModal').style.display = 'block';
// });

// document.getElementById('closeFailedDepositsModal').addEventListener('click', function() {
//     document.getElementById('failedDepositsModal').style.display = 'none';
// });

document.getElementById('btnOpenDepositExemptModal').addEventListener('click', function() {
    if (typeof window.openEditExemptModal === 'function') {
        window.openEditExemptModal();
    }
});

var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}

// ── Debt Balances Modal ────────────────────────────────────────────────────────
// document.getElementById('openDebtBalancesModal').addEventListener('click', function() {
//     $('#debtBalancesModal').modal('show');
//     // Reset form
//     document.getElementById('debtBalancesForm').reset();
// });

// ── Handle Debt Balances Form Submission ───────────────────────────────────────
document.getElementById('debtBalancesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var officeId = document.getElementById('debtBalanceOffice').value;
    var depositTypeId = document.getElementById('debtBalanceDepositType').value;
    var balance = document.getElementById('debtBalanceAmount').value;
    
    if (!officeId || !depositTypeId || !balance) {
        KiloAlert.warning('Please fill in all required fields.');
        return;
    }
    
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    $.ajax({
        url: '/risk/debt-balances',
        type: 'POST',
        data: {
            _token: csrfToken,
            office_id: officeId,
            deposit_type_id: depositTypeId,
            balance: balance
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                KiloAlert.success(response.message || 'Debt balance recorded successfully!');
                $('#debtBalancesModal').modal('hide');
                document.getElementById('debtBalancesForm').reset();
            } else {
                KiloAlert.error(response.message || 'Failed to record debt balance.');
            }
        },
        error: function(xhr) {
            var errorMsg = 'Network error. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                errorMsg = 'Validation error. Please check your inputs.';
            }
            KiloAlert.error(errorMsg);
        }
    });
});

// ── Drill-down: Click type header to show office deposits ──
(function() {
    var monthNames = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
    var currentMonth = new Date().getMonth();
    var currentYear = new Date().getFullYear();

    function formatCurrency(amount) {
        return 'K' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function groupDepositsByMonth(deposits) {
        var months = {};
        deposits.forEach(function(d) {
            var dateStr = d.date;
            var month = dateStr ? parseInt(dateStr.split('-')[1], 10) : 1;
            if (!months[month]) {
                months[month] = { total: 0, count: 0 };
            }
            months[month].total += parseFloat(d.amount) || 0;
            months[month].count++;
        });
        return months;
    }

    function getMonthStatus(monthIdx, hasDeposits) {
        if (hasDeposits) return 'has-deposits';
        if (monthIdx < currentMonth) return 'past-no-deposits';
        if (monthIdx === currentMonth) return 'current-no-deposits';
        return 'future-no-deposits';
    }

    function renderOfficeDeposits(officeData, depositTypeId) {
        var container = document.getElementById('da-body-' + depositTypeId);
        if (!container) return;

        var html = '';
        officeData.forEach(function(office) {
            var total = office.total || 0;
            var months = groupDepositsByMonth(office.deposits || []);

            var monthBoxes = monthNames.map(function(m, idx) {
                var hasDeposits = months[idx + 1] && months[idx + 1].count > 0;
                var status = getMonthStatus(idx, hasDeposits);
                var amount = hasDeposits ? formatCurrency(months[idx + 1].total) : '';
                return '<div class="da-month-box da-month-' + status + '" title="' + (amount || 'No deposits') + '">' + m + (amount ? '<span class="da-month-amount">' + amount + '</span>' : '') + '</div>';
            }).join('');

            html += '<div class="da-office-row" data-office-id="' + office.office_id + '"><div class="da-office-header"><span class="da-office-name">' + office.office_name + '</span><div class="da-office-months">' + monthBoxes + '</div></div></div>';
        });

        container.innerHTML = html;
    }

    function fetchAndRenderDeposits(typeId, period) {
        var card = document.querySelector('.da-type-card[data-type-id="' + typeId + '"]');
        var body = document.getElementById('da-body-' + typeId);
        if (!card || !body) return;

        if (card.classList.contains('open')) {
            card.classList.remove('open');
            body.innerHTML = '';
            return;
        }

        var allCards = document.querySelectorAll('.da-type-card');
        allCards.forEach(function(c) {
            if (c !== card) {
                c.classList.remove('open');
                var b = document.getElementById('da-body-' + c.getAttribute('data-type-id'));
                if (b) b.innerHTML = '';
            }
        });

        card.classList.add('open');

        // Show shimmer loading state
        body.classList.add('da-loading');
        body.innerHTML = '<div class="da-shimmer-row"><div class="da-shimmer-line long"></div><div class="da-shimmer-line medium"></div><div class="da-shimmer-line short"></div></div>';

        var params = new URLSearchParams({ period: period || 'year' });
        var currentParams = new URLSearchParams(window.location.search);
        var officeId = currentParams.get('office_id');
        if (officeId) {
            params.set('office_id', officeId);
        }

        fetch('/risk/branch-deposit-audit/type/' + typeId + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                body.classList.remove('da-loading');
                renderOfficeDeposits(data.offices || [], typeId);
            })
            .catch(function(err) {
                console.error('Error fetching deposits:', err);
                body.classList.remove('da-loading');
                body.innerHTML = '<div class="da-error">Failed to load office deposits. Please try again.</div>';
            });
    }

    document.querySelectorAll('.da-type-header').forEach(function(header) {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            var card = header.closest('.da-type-card');
            if (!card) return;
            var typeId = card.getAttribute('data-type-id');
            var period = document.getElementById('da-period') ? document.getElementById('da-period').value : 'year';
            fetchAndRenderDeposits(typeId, period);
        });
    });
})();
</script>

{{-- Ledger table script moved to partial: resources/views/risk/partials/manual-ledger-deposit-balances.blade.php --}}

@include('risk.partials.deposit-exempt-modal', ['depositTypes' => $depositTypes ?? [], 'offices' => $offices ?? []])
@include('risk.partials.office-debt-modal')
@endsection
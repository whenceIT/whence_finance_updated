<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report – {{ $submission->office->name ?? 'Branch' }} – {{ $submission->audit_date ? $submission->audit_date->format('d M Y') : '' }}</title>
    <style>
        /* ── Print-only ── */
        @media print {
            @page { margin: 18mm 15mm; size: A4; }
            body { font-family: Arial, Helvetica, sans-serif; font-size: 10.5pt; color: #222; background: #fff; }
            .no-print { display: none !important; }
            table { page-break-inside: avoid; break-inside: avoid; }
            .section-block { page-break-inside: avoid; break-inside: avoid; margin-bottom: 18pt; }
            h2, h3 { page-break-after: avoid; }
        }

        /* ── Screen + print ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10.5pt; color: #222; background: #fff; padding: 24px; }

        .report-header {
            border: 2px solid #c0392b; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px;
            background: #fff5f5;
        }
        .report-header .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .report-header h1 { font-size: 17pt; color: #c0392b; font-weight: bold; }
        .report-header .subtitle { font-size: 10pt; color: #777; margin-top: 2px; }
        .risk-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 11pt; color: #fff; }
        .meta-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; margin-top: 12px; }
        .meta-item { background: #f9f9f9; border-radius: 4px; padding: 6px 10px; border-left: 3px solid #ccc; }
        .meta-item .meta-label { font-size: 8pt; color: #888; text-transform: uppercase; letter-spacing: .4px; }
        .meta-item .meta-value { font-size: 10.5pt; font-weight: 600; color: #333; margin-top: 2px; }

        .section-block { margin-bottom: 20px; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; }
        .section-header {
            padding: 10px 14px; color: #fff; font-size: 12pt; font-weight: bold;
            display: flex; justify-content: space-between; align-items: center;
        }
        .section-header .score-row { font-size: 9.5pt; font-weight: normal; opacity: .9; }
        .section-header .score-row span { margin-left: 10px; }

        /* Admin section header */
        .admin-block { border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
        .admin-header { background: #7f8c8d; color: #fff; padding: 9px 14px; font-size: 12pt; font-weight: bold; }

        .admin-table, .section-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
        .admin-table thead tr { background: #ecf0f1; }
        .section-table thead tr { background: #c0392b; color: #fff; }
        .admin-table th, .section-table th { padding: 6px 8px; text-align: left; font-weight: 600; }
        .admin-table td, .section-table td { padding: 5px 8px; border-top: 1px solid #eee; vertical-align: top; }
        .admin-table .val { font-weight: 600; }
        .section-table .item-name { font-weight: 600; }
        .section-table .muted { color: #777; font-size: 8.5pt; }
        .section-table .verify { color: #1a5276; font-size: 8.5pt; }
        .section-table .flag { color: #922b21; font-size: 8.5pt; }
        .status-pass { color: #27ae60; font-weight: bold; font-size: 12pt; }
        .status-fail { color: #c0392b; font-weight: bold; font-size: 12pt; }
        .status-na   { color: #aaa; font-size: 12pt; }
        .notes-cell  { font-style: italic; color: #555; font-size: 8.5pt; }

        .actions-block { border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin: 16px 0; }
        .actions-header { background: #333; color: #fff; padding: 9px 14px; font-size: 11.5pt; font-weight: bold; }

        .open-remarks-block { border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin: 16px 0; }
        .remarks-header { background: #7f8c8d; color: #fff; padding: 9px 14px; font-size: 11pt; font-weight: bold; }

        /* Print / Email toolbar */
        .no-print { text-align: right; margin-bottom: 14px; }
        .no-print button { padding: 7px 18px; font-size: 11pt; cursor: pointer; }
        #printBtn { background: #c0392b; color: #fff; border: none; border-radius: 4px; }
        #printBtn:hover { background: #a93226; }

        .page-break { page-break-before: always; break-before: page; }

        /* Conclusion / sign-off table */
        .s9-table th { background: #2c3e50; color: #fff; }

        .signoff-note { font-size: 8.5pt; color: #888; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="no-print">
    <button id="printBtn" onclick="window.print()">
        <i class="fa fa-print"></i> Print Report
    </button>
    <button onclick="window.close()" style="background:#777;color:#fff;border:none;border-radius:4px;padding:7px 18px;font-size:11pt;cursor:pointer;margin-left:6px;">
        Close
    </button>
</div>

{{-- ── HEADER BAND ──────────────────────────────────────────── --}}
<div class="report-header">
    <div class="header-top">
        <div>
            <h1>Branch Audit Checklist Report</h1>
            <div class="subtitle">Cashless Operations Edition &nbsp;·&nbsp; v3.0 &nbsp;·&nbsp; INTERNAL USE ONLY</div>
        </div>
        <span class="risk-badge" style="background:{{ $rc['color'] ?? '#666' }};">
            {{ $rc['label'] ?? ucfirst($submission->risk_rating) }}
        </span>
    </div>
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-label">Branch</div>
            <div class="meta-value">{{ $submission->office->name ?? '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Branch Code</div>
            <div class="meta-value">{{ $submission->office->external_id ?? '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Audit Date</div>
            <div class="meta-value">{{ $submission->audit_date ? $submission->audit_date->format('d M Y') : '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Auditor</div>
            <div class="meta-value">{{ $submission->auditor->name ?? ($submission->auditor_name ?? '—') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Audit Type</div>
            <div class="meta-value">{{ ucfirst(str_replace('_',' ', $submission->audit_type ?? '')) }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Unannounced</div>
            <div class="meta-value">{{ strtoupper($submission->unannounced ?? '—') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Period — Start</div>
            <div class="meta-value">{{ $submission->period_start ? $submission->period_start->format('d M Y') : '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Period — End</div>
            <div class="meta-value">{{ $submission->period_end ? $submission->period_end->format('d M Y') : '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Total ✗ Fail Count</div>
            <div class="meta-value" style="color:#c0392b;">{{ $submission->fail_count }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Prepared by</div>
            <div class="meta-value">Dr. Henry Lukama Chikweti, Ph.D</div>
        </div>
    </div>
</div>

{{-- ── ADMIN / METADATA SECTION ──────────────────────────────── --}}
<div class="admin-block">
    <div class="admin-header">
        <i class="fa fa-info-circle"></i>&nbsp; Section 0 — Administration &amp; Metadata
    </div>
    <table class="admin-table">
        <tbody>
        @php $adminFields = [
            'id'                => 'Submission ID',
            'office_id'         => 'Branch',
            'auditor_name'      => 'Auditor Name',
            'audit_date'        => 'Audit Date',
            'period_start'      => 'Period Start',
            'period_end'        => 'Period End',
            'audit_scope'       => 'Audit Scope',
            'opening_remarks'   => 'Opening Remarks',
            'audit_type'        => 'Audit Type',
            'unannounced'       => 'Unannounced',
            'manager_present'   => 'Manager Present',
            'manager_name'      => 'Manager Name',
        ]; @endphp
        @foreach($adminFields as $field => $label)
            <tr>
                <th style="width:30%;background:#fafafa;">{{ $label }}</th>
                <td class="val">
                    @if($field === 'office_id')
                        {{ $submission->office->name ?? '—' }}
                    @elseif(in_array($field, ['audit_date','period_start','period_end']))
                        {{ $submission->$field ? \Carbon\Carbon::parse($submission->$field)->format('d M Y') : '—' }}
                    @elseif($field === 'audit_scope' && $submission->$field)
                        @php $scope = is_string($submission->$field) ? json_decode($submission->$field, true) : $submission->$field; @endphp
                        @if(is_array($scope))
                            {{ implode(', ', $scope) }}
                        @else
                            {{ $submission->$field }}
                        @endif
                    @else
                        {{ $submission->$field ?? '—' }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- ── CHECKLIST SECTIONS 1–9 ────────────────────────────────── --}}
@php
    $sectionItemLabels = $sectionItems;
@endphp

@foreach($sectionShorts as $si => $sname)
    @if($si === 0) @continue @endif

    @php
        $items   = $sectionItemLabels[$si] ?? [];
        $secData = ($submission->sections ?? [])[$si] ?? ['pass' => 0, 'fail' => 0, 'na' => 0];
        $sp      = $secData['pass'] ?? 0;
        $sf      = $secData['fail'] ?? 0;
        $sna     = $secData['na']   ?? 0;
        $total   = $sp + $sf + $sna;
        $failRatio = $total > 0 ? $sf / $total : 0;
        $barColor  = $failRatio === 0              ? '#27ae60'
                   : ($failRatio <= 0.10           ? '#f39c12'
                   : ($failRatio <= 0.25            ? '#e67e22'
                   : '#c0392b'));
        $pct = $total > 0 ? round(($sp / $total) * 100) : 0;
        $hasAnswers = $total > 0;
        $secNum = $si + 1; // s2 = sec 2 → $si=1
        $fieldPrefix = 's' . $secNum;
    @endphp

    @php
        // Section display labels
        $secDisplayNames = [
            1 => 'Withinhere Wallet &amp; Digital Payment Controls',
            2 => 'Loan Portfolio Integrity',
            3 => 'Collections &amp; Recoveries',
            4 => 'Fraud Risk Indicators',
            5 => 'Staff &amp; Process Compliance',
            6 => 'System &amp; Control Environment',
            7 => 'Reporting &amp; Governance',
            8 => 'Audit Conclusion &amp; Sign-Off',
        ];
        $secDisplay    = $secDisplayNames[$si] ?? 'Section ' . $si;
        $resultCSS    = !$hasAnswers ? '' : 'background:' . $barColor . ';';
    @endphp

    <div class="section-block">
        <div class="section-header" style="background:{{ !$hasAnswers ? '#95a5a6' : $barColor }};">
            <span><i class="fa fa-list-ol"></i>&nbsp; Section {{ $si }} — {{ $secDisplay }}</span>
            @if($hasAnswers)
                <span class="score-row">
                    <span><i class="fa fa-check" style="color:#2ecc71;"></i> {{ $sp }} Pass</span>
                    <span><i class="fa fa-times" style="color:#e74c3c;"></i> {{ $sf }} Fail</span>
                    <span><i class="fa fa-minus" style="color:#aaa;"></i> {{ $sna }} N/A</span>
                    <span>{{ $pct }}%</span>
                </span>
            @endif
        </div>

        @if(!empty($items))
        <table class="section-table">
            <thead>
                <tr>
                    <th style="width:30%;">Control Item</th>
                    <th style="width:22%;">What to Check</th>
                    <th style="width:22%;">How to Verify</th>
                    <th style="width:16%;">Red Flag Indicator</th>
                    <th style="width:5%;text-align:center;">Result</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $field => $label)
                @php
                    $val   = $submission->$field ?? '';
                    $notes = $submission->{$field . '_notes'} ?? '';
                    if ($val === null) $val = '';
                    $lo = strtolower((string)$val);
                    if ($si === 4) {
                        // Fraud section: not_present = pass, present = fail
                        if ($lo === 'not_present') { $cls='status-pass'; $icon='✓'; $label_text='Not Present (Pass)'; }
                        elseif ($lo === 'present') { $cls='status-fail'; $icon='✗'; $label_text='Present (Fail)'; }
                        else { $cls='status-na'; $icon='—'; $label_text='N/A'; }
                    } else {
                        if ($lo === 'pass')             { $cls='status-pass'; $icon='✓'; $label_text='Pass'; }
                        elseif ($lo === 'fail')         { $cls='status-fail'; $icon='✗'; $label_text='Fail'; }
                        elseif ($lo === 'na')           { $cls='status-na';   $icon='—'; $label_text='N/A'; }
                        else                            { $cls='status-na';   $icon='—'; $label_text='—'; }
                        }
                @endphp
                <tr>
                    <td class="item-name">{{ $label }}</td>
                    @php
                        $check = '';
                        $verify = '';
                        $flag   = '';
                        // Pull check/verify/flag from checklist definition
                        $defMap = [
                            's2' => [
                                's2_1' => ['check'=>'No physical cash found anywhere at the branch — tills, desks, safe, bags.','verify'=>'Physically inspect the branch. Record any cash found.','flag'=>'Any cash found is an immediate red flag — escalate same day.'],
                                's2_2' => ['check'=>'Clients pay via Withinhere app, Whence Financial Services app, or company mobile money lines only.','verify'=>'Review Withinhere wallet log and mobile money records.','flag'=>'Client paid via staff member\'s personal mobile money or handed cash to staff.'],
                                's2_3' => ['check'=>'Mobile money receipt → Withinhere wallet transfer = same transaction, no delay.','verify'=>'Cross-reference mobile money timestamps with Withinhere wallet inflows.','flag'=>'6-hour gap between mobile money receipt and wallet transfer — policy breach.'],
                                's2_4' => ['check'=>'Only the Branch Manager initiates mobile-money-to-wallet transfers.','verify'=>'Review transfer logs. Check who initiated each transfer.','flag'=>'6 transfers initiated by loan officer — unauthorised.'],
                                's2_5' => ['check'=>'Withinhere wallet balance matches loan system records.','verify'=>'Compare wallet statement to loan system disbursement records.','flag'=>'K24,000 gap between loan system and wallet with no explanation.'],
                                's2_6' => ['check'=>'Every loan disbursed through the Withinhere branch wallet.','verify'=>'Pull disbursements and verify each against Withinhere outflow log.','flag'=>'Loan disbursed via mobile money — no wallet outflow on record.'],
                                's2_7' => ['check'=>'Client disbursement channel preference documented and matched.','verify'=>'Pull 5 loan files and verify channel preference vs actual disbursement.','flag'=>'File states Withinhere; log shows outflow to unrelated mobile number.'],
                                's2_8' => ['check'=>'Inter-branch transfers have formal authorisation signed by independent supervisor.','verify'=>'Check wallet log for outflows to other branches and match to authorisation forms.','flag'=>'K50,000 transferred to another branch — no authorisation form.'],
                                's2_9' => ['check'=>'Full Withinhere wallet transaction log reviewed.','verify'=>'Review for out-of-hours transactions, unknown recipients, reversals.','flag'=>'K7,000 outflow at 11:52 PM by BM — no matching loan disbursement.'],
                                's2_10'=> ['check'=>'Every failed, reversed, or flagged transaction has a written resolution.','verify'=>'Request the Withinhere exception log and written explanations.','flag'=>'3 failed transfers with no documentation or follow-up.'],
                            ],
                            's3' => [
                                's3_1' => ['check'=>'Every active loan file contains: national ID copy, signed application, credit appraisal, approval signature, Withinhere receipt.','verify'=>'Pull 10 random files and check each for required documents.','flag'=>'File missing client signature and ID copy — should not have been disbursed.'],
                                's3_2' => ['check'=>'Each staff member\'s loan approvals are within their signed authority limit.','verify'=>'List period approvals and compare to approver\'s authority letter.','flag'=>'Officer with K3,500 limit approved K5,000 loan — control breach.'],
                                's3_3' => ['check'=>'All active loan clients are real — phone verification conducted.','verify'=>'Call 15 random clients. Verify they received the loan via Withinhere.','flag'=>'2 of 15 clients said they never received a loan — possible ghost loans.'],
                                's3_4' => ['check'=>'Each system disbursement matches a Withinhere wallet outflow on the same date.','verify'=>'Compare system disbursement total with wallet outflows day by day.','flag'=>'System shows K50,000 disbursed; wallet shows only K30,000 — K20,000 gap.'],
                                's3_5' => ['check'=>'Interest rate charged matches loan agreement and policy rate.','verify'=>'Calculate expected interest for 5 files; compare to actual ledger entries.','flag'=>'Policy rate 15%; client ledger shows 20% — undisclosed fee.'],
                                's3_6' => ['check'=>'Expired/rolled-over loans have a fresh credit appraisal and approval on file.','verify'=>'Identify loans past maturity and verify re-approval documentation.','flag'=>'Loan #207 matured Jan 2026, still active with no re-approval.'],
                                's3_7' => ['check'=>'Stated loan purpose is plausible and verified.','verify'=>'For school fee loans confirm it is school paying period and amount is adequate.','flag'=>'Loan amount not adequate for stated school fees — possible diversion.'],
                            ],
                        ];
                        if (isset($defMap[$fieldPrefix][$field])) {
                            $check  = $defMap[$fieldPrefix][$field]['check'];
                            $verify = $defMap[$fieldPrefix][$field]['verify'];
                            $flag   = $defMap[$fieldPrefix][$field]['flag'];
                        }
                        // Also pull S5 (Fraud) separately
                        if ($fieldPrefix === 's5') {
                            $s5def = [
                                's5_1' => ['check'=>'Disbursements pending more than 7 days have a written explanation and a Withinhere outflow record.','verify'=>'List all pending disbursements and verify wallet outflows exist.','flag'=>'15 loans pending more than 7 days — no wallet outflows — possible fraudulent approvals.'],
                                's5_2' => ['check'=>'Sudden volume spikes verified independently via wallet data.','verify'=>'Compare 6-month loan counts; spikes >2× mean require 100% file review.','flag'=>'Branch averaged 10/month; last month 50 loans — all must be individually reviewed.'],
                                's5_3' => ['check'=>'Strong performance reports reconcile with Withinhere wallet inflows.','verify'=>'Cross-reference performance report against wallet statement for the period.','flag'=>'85% target achieved; wallet inflows total only K3,200 — where are the collections?'],
                                's5_4' => ['check'=>'Outperforming staff have wallet inflows that match their attributed collections.','verify'=>'Compare individual collections vs wallet inflows attributed to same staff member.','flag'=>'Officer claims K60,000 of K70,000 branch collections; wallet shows only K9,000 total.'],
                                's5_5' => ['check'=>'Every triggered early-warning sign has a documented Playbook intervention.','verify'=>'Request the Playbook intervention log. Each sign ➜ response date, action, outcome.','flag'=>'Sign #2 triggered in February — no intervention documented.'],
                                's5_6' => ['check'=>'No client reports of payments missing from their account or record.','verify'=>'Review complaints register; call 5 delinquent clients; check wallet.','flag'=>'Client #82 paid K1,500 via mobile money — no record in wallet or loan account.'],
                            ];
                            $def = $s5def[$field] ?? null;
                            if ($def) { $check = $def['check']; $verify = $def['verify']; $flag = $def['flag']; }
                        }
                        // S6
                        if ($fieldPrefix === 's6') {
                            $s6def = [
                                's6_1' => ['check'=>'Staff salaries/advances paid through official payroll only, not wallet or personal mobile money.','verify'=>'Review payroll vs wallet transfer records.','flag'=>'K500 Withinhere transfer to staff member\'s personal account — not on payroll.'],
                                's6_2' => ['check'=>'Three functions (initiate wallet transfers, record transactions, approve loans) are performed by different people.','verify'=>'List who does each function. Any name in more than one column is a failure.','flag'=>'BM initiates all wallet transfers AND approves all loans — no independent check.'],
                                's6_3' => ['check'=>'Every loan follows full documented procedure.','verify'=>'Trace 5 loan files through the procedure checklist.','flag'=>'Loan #305 has no client interview or credit check — disbursed on verbal instruction.'],
                                's6_4' => ['check'=>'Every system/Withinhere override has formal documented authorisation.','verify'=>'Request override log + admin logs for the period.','flag'=>'Withinhere transfer limit override with no supporting authorisation document.'],
                                's6_5' => ['check'=>'Every staff member has a signed accountability document on file.','verify'=>'Pull HR file for each staff. Verify accountability form references cashless policy.','flag'=>'Loan officer has no signed form and is unaware of wallet-transfer rule.'],
                                's6_6' => ['check'=>'No staff member has gone more than 12 months without leave and a designated cover.','verify'=>'Check leave records for the past year.','flag'=>'BM has not taken leave in 18 months — sole wallet-transfer initiator with no cover.'],
                                's6_7' => ['check'=>'No financial relationship between staff and clients.','verify'=>'Review guarantor records; check wallet logs for transfers to staff personal numbers.','flag'=>'Loan officer\'s personal number received K2,000 transfer from a client.'],
                            ];
                            $def = $s6def[$field] ?? null;
                            if ($def) { $check = $def['check']; $verify = $def['verify']; $flag = $def['flag']; }
                        }
                        // S7
                        if ($fieldPrefix === 's7') {
                            $s7def = [
                                's7_1' => ['check'=>'Both systems block transactions exceeding a user\'s authorised level.','verify'=>'Have junior staff attempt a transfer/approval above their limit in both systems.','flag'=>'Withinhere allowed loan officer to initiate wallet transfer — only BM should.'],
                                's7_2' => ['check'=>'Every system action (login, transfer, edit) is logged and reviewed at least monthly.','verify'=>'Request audit log from both systems for the period.','flag'=>'Withinhere shows K7,000 transfer at 11:52 PM — no business-hours explanation.'],
                                's7_3' => ['check'=>'Exception/override reports are generated, reviewed, and signed off.','verify'=>'Ask to see last three months of exception reports and their sign-off log.','flag'=>'Exception reports generated but stored unread — no review evidence.'],
                                's7_4' => ['check'=>'Staff user access levels reflect their job roles in both systems.','verify'=>'Request user access report from both systems and compare to job descriptions.','flag'=>'Three former employees still have active logins — access never revoked.'],
                                's7_5' => ['check'=>'No unofficial handwritten registers or parallel spreadsheets are used.','verify'=>'Walk the branch and check for notebooks or spreadsheets tracking loans/collections.','flag'=>'Loan officer maintains personal notebook of client payments outside the system.'],
                                's7_6' => ['check'=>'Each staff member has a unique login in both systems; passwords are never shared.','verify'=>'Watch each staff member log in. No password sharing permitted.','flag'=>'BM logs in as loan officer "to save time" — audit trail compromised.'],
                            ];
                            $def = $s7def[$field] ?? null;
                            if ($def) { $check = $def['check']; $verify = $def['verify']; $flag = $def['flag']; }
                        }
                        // S8 (definition-based; pull from config or fall back)
                        if ($fieldPrefix === 's8') {
                            $s8def = [
                                's8_1' => ['check'=>'All performance metrics reported match Withinhere wallet data.','verify'=>'Compare KPI report to wallet statement for the same period.','flag'=>'Revenue reported above wallet inflows with no variance explanation.'],
                                's8_2' => ['check'=>'No KPI manipulation via re-loans, re-booking, or post-dating.','verify'=>'Cross-check disbursements near period-end with re-payment patterns.','flag'=>'End-of-month spike followed by bulk early repayments — possible re-loan scheme.'],
                                's8_3' => ['check'=>'Every required escalation has a documented record.','verify'=>'Review escalation log for the period. Missing entries are a control failure.','flag'=>'Three incidents requiring escalation with no documentation in the log.'],
                                's8_4' => ['check'=>'Every finding from the previous audit has been addressed or formally signed off as de-risked.','verify'=>'Compare previous audit action plan to current status.','flag'=>'2 of 5 previous findings marked "in progress" for more than 60 days with no update.'],
                                's8_5' => ['check'=>'Branch activity complies with the institution\'s governance framework.','verify'=>'Request any required governance filings, board approvals, or management sign-offs.','flag'=>'Branch initiated a loan product not on the approved product list — no governance approval.'],
                                's8_6' => ['check'=>'All board/management report submissions are complete and on file.','verify'=>'Check the report submission register. Confirm dates, recipients, and acknowledgements.','flag'=>'Two monthly management reports missing — no submission record found.'],
                            ];
                            $def = $s8def[$field] ?? null;
                            if ($def) { $check = $def['check']; $verify = $def['verify']; $flag = $def['flag']; }
                        }
                        // S9 (Conclusion) — these have their own specific block below
                        if ($fieldPrefix === 's9') { continue; }
                    @endphp
                    <tr>
                        <td class="item-name">{{ $label }}</td>
                        <td class="muted">{{ $check }}</td>
                        <td class="verify">{{ $verify }}</td>
                        <td class="flag">{{ $flag }}</td>
                        <td style="text-align:center;" class="{{ $cls }}"><span style="font-size:12pt;">{{ $icon }}&nbsp;</span> <small>{{ $label_text }}</small></td>
                    </tr>
                    @if(!empty($notes))
                    <tr>
                        <td colspan="5" class="notes-cell">
                            <i class="fa fa-comment-o"></i>&nbsp; {{ $notes }}
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif

        @php
            // Section-specific summary notes
            $notesKeys = [
                's2' => 's2_notes', 's3' => 's3_notes', 's4' => 's4_notes',
                's5' => 's5_notes', 's6' => 's6_notes', 's7' => 's7_notes', 's8' => 's8_notes',
            ];
            $notesKey = $notesKeys[$fieldPrefix] ?? '';
            $secNotes = $submission->$notesKey ?? '';
        @endphp
        @if(!empty($secNotes))
        <div style="padding:8px 12px;background:#f9f9f9;border-top:1px solid #eee;font-size:8.5pt;color:#555;font-style:italic;">
            <i class="fa fa-file-text-o"></i>&nbsp; Section Notes: {{ $secNotes }}
        </div>
        @endif
    </div>
@endforeach

{{-- ── OPENING REMARKS ────────────────────────────────────────── --}}
@if(!empty($submission->opening_remarks))
<div class="open-remarks-block">
    <div class="remarks-header"><i class="fa fa-comment-o"></i>&nbsp; Opening Remarks</div>
    <div style="padding:10px 14px;font-style:italic;color:#444;font-size:10.5pt;">
        {{ $submission->opening_remarks }}
    </div>
</div>
@endif

{{-- ── KEY FINDINGS, IMMEDIATE ACTIONS, RECOMMENDATIONS ────────── --}}
@php $hasActions = ($submission->key_findings || $submission->immediate_actions || $submission->recommendations); @endphp
@if($hasActions)
<div class="page-break"></div>
<div class="actions-block">
    <div class="actions-header">
        <i class="fa fa-lightbulb-o"></i>&nbsp; Key Findings, Immediate Actions &amp; Recommendations
    </div>
    <table class="section-table">
        <tbody>
            @if(!empty($submission->key_findings))
            <tr>
                <th style="width:22%;background:#c0392b;color:#fff;"><i class="fa fa-exclamation-triangle" style="color:#f1c40f;"></i>&nbsp; Key Findings</th>
                <td>{{ $submission->key_findings }}</td>
            </tr>
            @endif
            @if(!empty($submission->immediate_actions))
            <tr>
                <th style="width:22%;background:#e67e22;color:#fff;"><i class="fa fa-flash"></i>&nbsp; Immediate Actions</th>
                <td>{{ $submission->immediate_actions }}</td>
            </tr>
            @endif
            @if(!empty($submission->recommendations))
            <tr>
                <th style="width:22%;background:#2980b9;color:#fff;"><i class="fa fa-check-circle"></i>&nbsp; Recommendations</th>
                <td>{{ $submission->recommendations }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endif

{{-- ── CONCLUSION &amp; SIGN-OFF ──────────────────────────────── --}}
<div class="section-block">
    <div class="section-header" style="background:#2c3e50;">
        <span><i class="fa fa-pencil-square-o"></i>&nbsp; Section 9 — Audit Conclusion &amp; Sign-Off</span>
    </div>
    @php
        $s9Labels = [
            's9_1' => 'Total ✗ Count & Risk Rating confirmed',
            's9_2' => 'Key Findings documented (max 5, ranked by severity)',
            's9_3' => 'Immediate Actions assigned (within 24–48 hours)',
            's9_4' => 'Recommendations for permanent fixes documented',
            's9_5' => 'Follow-up Audit Date scheduled',
        ];
    @endphp
    <table class="section-table s9-table">
        <thead>
            <tr style="background:#2c3e50;color:#fff;">
                <th style="width:40%;">Conclusion Item</th>
                <th style="width:10%;text-align:center;">Result</th>
                <th style="width:50%;">Notes / Finding</th>
            </tr>
        </thead>
        <tbody>
            @php $s9RiskText = strtoupper($submission->risk_rating ?? 'pending'); @endphp
            <tr>
                <td><strong>Overall Risk Rating</strong> <span class="muted">(s9_1)</span></td>
                <td style="text-align:center;">
                    <span class="{{ strtolower($submission->risk_rating) === 'low' ? 'status-pass' : (strtolower($submission->risk_rating) === 'critical' || strtolower($submission->risk_rating) === 'high' ? 'status-fail' : 'status-fail') }}">{{ $s9RiskText }}</span>
                </td>
                <td style="color:#555;">{{ $submission->fail_count }} pass/fail items assessed across Sections 1–8.&nbsp;
                    @if($s9RiskText === 'CRITICAL') CRITICAL — suspend operations and escalate immediately.@elseif($s9RiskText === 'HIGH') HIGH — escalate to Regional Manager.@elseif($s9RiskText === 'MEDIUM') MEDIUM — action plan required within 7 days.@else LOW — standard monitoring, next audit in 3 months.@endif
                </td>
            </tr>
            @for($i = 1; $i <= 5; $i++)
                @php
                    $f9 = "s9_{$i}";
                    $f9n = $f9 . '_notes';
                    $v9 = $submission->$f9 ?? '';
                    $n9 = $submission->$f9n ?? '';
                    $v9l = strtolower((string)$v9);
                    if ($v9l === 'pass') { $ic='&#10003;'; $cls9='status-pass'; $lt='Pass'; }
                    elseif ($v9l === 'fail') { $ic='&#10007;'; $cls9='status-fail'; $lt='Fail'; }
                    else { $ic='&#8212;'; $cls9='status-na'; $lt='—'; }
                @endphp
                <tr>
                    <td><strong>{{ $s9Labels[$f9] }}</strong> <span class="muted">({{ $f9 }})</span></td>
                    <td style="text-align:center;" class="{{ $cls9 }}"><span style="font-size:12pt;">{{ $ic }}&nbsp;</span> {{ $lt }}</td>
                    <td>{{ $n9 ?: '—' }}</td>
                </tr>
            @endfor
            <tr>
                <td><strong>Section 9 Notes / Observations</strong></td>
                <td colspan="2">{{ $submission->s9_notes ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <div style="padding:8px 12px;background:#f9f9f9;border-top:1px solid #eee;"></div>
</div>

{{-- ── ESCALATION &amp; FOLLOW-UP ────────────────────────────────── --}}
<div class="page-break"></div>
<div class="section-block">
    <div class="section-header" style="background:#34495e;">
        <span><i class="fa fa-arrow-up"></i>&nbsp; Escalation &amp; Follow-Up</span>
    </div>
    <table class="section-table s9-table">
        <thead><tr style="background:#34495e;color:#000;"><th style="width:35%;">Field</th><th style="width:65%;">Value</th></tr></thead>
        <tbody>
            <tr>
                <th style="background:#C2C0C0;">Escalation Required</th>
                <td>{{ $submission->escalation_required ? 'YES — ' . ucfirst($submission->escalation_required) : 'No' }}</td>
            </tr>
            <tr>
                <th style="background:#C2C0C0;">Follow-Up Date</th>
                <td>{{ $submission->followup_date ? \Carbon\Carbon::parse($submission->followup_date)->format('d M Y') : '—' }}</td>
            </tr>
            <tr>
                <th style="background:#C2C0C0;">Escalation Notified Date</th>
                <td>{{ $submission->signoff_datetime ? \Carbon\Carbon::parse($submission->signoff_datetime)->format('d M Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <th style="background:#C2C0C0;">Escalation / Risk Manager</th>
                <td>{{ $submission->signoff_datetime ?? '—' }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ── SIGN-OFF &amp; ACKNOWLEDGEMENT ────────────────────────────── --}}
<div class="page-break"></div>
<div class="section-block">
    <div class="section-header" style="background:#2c3e50;">
        <span><i class="fa fa-pen"></i>&nbsp; Sign-Off &amp; Manager Acknowledgement</span>
    </div>
    <table class="section-table s9-table">
        <thead><tr style="background:#2c3e50;color:#fff;"><th style="width:35%;">Field</th><th style="width:65%;">Detail</th></tr></thead>
        <tbody>
            <tr>
                <th style="background:#C2C0C0;">Auditor Signature</th>
                <td>{{ $submission->auditor_signature ?? '—' }}</td>
            </tr>
            <tr>
                <th style="background:#C2C0C0;">Sign-Off Date &amp; Time</th>
                <td>{{ $submission->signoff_datetime ? \Carbon\Carbon::parse($submission->signoff_datetime)->format('d M Y, H:i') : '—' }}</td>
            </tr>
            <tr>
                <th style="background:#C2C0C0;">Manager Acknowledged</th>
                <td>{{ $submission->manager_acknowledgement ? 'YES — ' . ucfirst($submission->manager_acknowledgement) : 'Not yet acknowledged' }}</td>
            </tr>
            @if(!empty($submission->manager_comments))
            <tr>
                <th style="background:#C2C0C0;">Manager Comments</th>
                <td>{{ $submission->manager_comments }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- ── FOOTER ────────────────────────────────────────────────── --}}
<div style="margin-top:20px;border-top:1px solid #ccc;padding-top:8px;font-size:8pt;color:#888;text-align:center;">
    Generated by Withinhere Branch Audit Checklist v3.0 (Cashless Operations Edition)&nbsp;·&nbsp;
    Prepared by Dr. Henry Lukama Chikweti, Ph.D&nbsp;·&nbsp;
    INTERNAL USE ONLY&nbsp;·&nbsp;
    Submission ID: {{ $submission->id }}&nbsp;·&nbsp;
    Printed: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
</div>
</body>
</html>

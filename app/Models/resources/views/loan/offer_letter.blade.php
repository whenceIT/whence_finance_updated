<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Agreement</title>
    <style>
    @page {
        size: A4;
        margin: 2cm;
    }
    @media print {
    a[href]:after {
        content: none !important;
    }

    /* Optional: Hide specific link entirely */
    .no-print {
        display: none !important;
    }
}


    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        font-size: 14pt;
        text-transform: uppercase;
        margin-bottom: 10px;
        page-break-after: avoid;
    }

    p {
        margin: 8px 0;
        text-align: justify;
        page-break-inside: avoid;
    }

    .section-title {
        font-weight: bold;
        margin-top: 15px;
        text-transform: uppercase;
        page-break-after: avoid;
    }

    .box {
        border: 1px solid #000;
        padding: 10px;
        margin: 10px 0;
        page-break-inside: avoid;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
        page-break-inside: auto;
    }

    thead {
        display: table-header-group;
    }

    tr, td, th {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
        page-break-inside: avoid;
    }

    ul, ol {
        page-break-inside: avoid;
    }

    .signature-section p {
        margin-top: 30px;
        page-break-inside: avoid;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 10px;
    }

    img {
        max-width: 100%;
        height: auto;
        page-break-inside: avoid;
    }

    .page-break {
        page-break-before: always;
    }
</style>

</head>
<body>

<div style="text-align: right; margin: 20px;" class="no-print">
    <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px;">🖨️ Print Agreement</button>
</div>

@php
    $formattedDate = \Carbon\Carbon::now()->format('jS F Y');
@endphp
@if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
    <div class="logo-container">
        <img src="{{ asset('uploads/' . \App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
             class="img-responsive" width="120" />
    </div>
@endif

<h2>Loan Agreement</h2>

<p>
    This Loan Agreement is made and entered into as of the date of execution, by and between:
</p>

<p>
    <strong>Borrower:</strong> 
    <strong> {{ $loan->client->full_name ?? $loan->client->first_name . ' ' . $loan->client->middle_name . ' ' . $loan->client->last_name }} 
    (N.R.C: {{ $loan->client->external_id }})</strong>, residing at <strong>{{ $loan->client->address }} {{ $loan->client->street }}</strong><br>
    (hereinafter referred to as the "Borrower", which expression shall, unless repugnant to the context, include his/her legal representatives, administrators, nominees, and assigns),
</p>

<p>
    <strong>AND</strong>
</p>

<p>
    <strong>Lender:</strong> Bedford Microfinance Limited (BML), Flat #2, Permanent Court, Ridgeway, Church Road, Lusaka. 
    (hereinafter referred to as the "Lender", which expression shall, unless repugnant to the context, include its legal representatives, administrators, nominees, and assigns).
</p>

<p>
    WHEREAS, the Lender has agreed to lend, and the Borrower has agreed to borrow, a loan amount 
    <strong> ZMW {{ number_format($loan->disbursed_cash_amount ?? $loan->disbursed_cash_amount ?? $loan->principal, 2) }}</strong>
    <strong> (ZMW {{ ucfirst((new NumberFormatter('en', NumberFormatter::SPELLOUT))->format($loan->disbursed_cash_amount ?? $loan->principal)) }})</strong> 
    for a period of <strong> {{ $loan->loan_term }} {{ $loan->loan_term_type }}</strong>, and the Borrower hereby accepts the loan and agrees to repay the same in accordance with the terms and conditions set forth herein.
</p>

<p>
    NOW THEREFORE, in consideration of the foregoing and the mutual covenants contained herein, the parties agree as follows:
</p>



<div class="section-title">1. Interest and Charges</div>
<ul>
    <li>Interest rate: {{ number_format($loan->interest_rate, 2) }}% per {{ $loan->loan_term_type }}</li>
    <li>Repayment due by: {{ \Carbon\Carbon::parse($loan->expected_first_repayment_date)->format('d M, Y') }}</li>
    @foreach($loan->charges as $charge)
    <li>
        {{ $charge->charge->name ?? 'Charge' }} - {{ number_format($charge->amount, 2) }} (deducted from disbursed amount)
    </li>
@endforeach

@if($loan->deduction_mode && $loan->deduction_carried_balance > 0)
    <li>
        Carried Balance Deduction - {{ number_format($loan->deduction_carried_balance, 2) }} (settled previous loan)
    </li>
@endif


    <li>
        Late payments shall attract a daily penalty of 1% starting from the 
        <strong>4th day</strong> after the due date until the loan is paid in full.
    </li>
    <li>The repayment date is fixed unless BML agrees otherwise in writing.</li>
</ul>

@php

function calculateStraightLineBreakdown($loan)
{
    $principal =  $loan->principal;
    $rate = $loan->interest_rate;
    $term = $loan->loan_term;
    $termType = strtolower($loan->term_type);
    $rateType = strtolower($loan->interest_rate_type); // 'month' or 'year'

    // Convert term to months
    switch ($termType) {
        case 'day':
            $months = round($term / 30, 2);
            break;
        case 'week':
            $months = round(($term * 7) / 30, 2);
            break;
        case 'year':
            $months = $term * 12;
            break;
        default:
            $months = $term;
    }

    // Convert rate to monthly decimal
    $monthlyRate = $rateType === 'year' ? ($rate / 12) : $rate;
    $monthlyRateDecimal = $monthlyRate / 100;

    // Flat interest: P × r × t
    $totalInterest = $loan->principal * $monthlyRateDecimal * $months;
    $monthlyPrincipal = $months > 0 ? $principal / $months : 0;
    $monthlyInterest = $months > 0 ? $totalInterest / $months : 0;
    $monthlyInstallment = $monthlyPrincipal + $monthlyInterest;
    $totalPayable = $principal + $totalInterest;

    return [
        'principal' => $principal,
        'term_in_months' => $months,
        'monthly_principal' => $monthlyPrincipal,
        'monthly_interest' => $monthlyInterest,
        'monthly_installment' => $monthlyInstallment,
        'total_interest' => $totalInterest,
        'total_payable' => $totalPayable,
    ];
}






@endphp

<div class="section-title">2. Loan Repayment Terms</div>

@php
    $repayment = calculateStraightLineBreakdown($loan);
@endphp
@php
    $firstDueDate = $loan->repayment_schedules->first()?->due_date;

    if ($firstDueDate) {
        $carbonDate = \Carbon\Carbon::parse($firstDueDate);

        if (strtolower($loan->loan_term_type) === 'weeks') {
            $repaymentDay = $carbonDate->format('l'); // e.g., 'Monday'
        } else {
            $repaymentDay = $carbonDate->format('jS'); // e.g., '5th'
        }
    } else {
        $repaymentDay = '___';
    }

    $fees = $repayment['fees'] ?? 0;
    $installmentFrequency = strtolower($loan->loan_term_type) === 'weeks' ? 'weekly' : 'monthly';
    $repaymentLabel = strtolower($loan->loan_term_type) === 'weeks' ? 'of every week' : 'day of every month';
@endphp

<ul style="list-style-type: none; padding-left: 20px;">
<li><strong>2.1</strong> The Borrower shall repay the loan in equal {{ $installmentFrequency }} instalments of ZMW <strong>{{ number_format($repayment['monthly_installment'], 2) }}</strong> on or before the <strong>{{ $repaymentDay }}</strong> {{ $repaymentLabel }}, along with charges of ZMW <strong>
    {{ $loan->charges->map(fn($c) => ($c->charge->name ?? 'Charge') . ' - ZMW ' . number_format($c->amount, 2))->implode(', ') }}
</strong>, in accordance with the Repayment Schedule attached hereto as Schedule I.</li>
    <li><strong>2.2</strong> Loan Repayments via BML collection channels (bank account, Airtel/MTN money,  Ewallet cash).</li>
    <li><strong>2.3</strong> Borrower must make full, consolidated repayments net of any withdrawal fees or bank charges.</li>
    <li><strong>2.4</strong> Borrower must Notify BML before closing/ changing of bank accounts.</li>
</ul>




<div class="section-title">3. Disbursements</div>
@php
            // Calculate total disbursement fees
            $disbursementFees = $loan->charges->where('charge.charge_type', 'disbursement')->sum('amount');

            // Calculate total payout
            $totalPayout = (!empty($loan->disbursed_cash_amount) ? $loan->disbursed_cash_amount : $loan->principal)
               - $disbursementFees
               - $loan->deduction_carried_balance;

        @endphp
        <ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>3.1</strong> Upon eligibility confirmation, BML will disburse ZMW <strong>{{$totalPayout}}</strong> to the Borrower within 24 hours.</li>
</ul>



<div class="section-title">4. Nature of Agreement</div>
<ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>4.1</strong> This is a one-time Loan agreement; no obligations exist beyond what is stated herein.</li>
</ul>

<div class="section-title">5. Events of Default</div>
<ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>5.1</strong> Misrepresentation, breach of terms, legal proceedings, business closure, unusual debt settlement, or insolvency action will make the loan immediately due.</li>
    <li><strong>5.2</strong> Any dispute will be under the exclusive jurisdiction of the competent courts where the Lender resides or operates.</li>
    <li><strong>5.3</strong> Borrower indemnifies BML against losses and agrees to reimburse related legal/enforcement costs.</li>
    <li><strong>5.4</strong> The Borrower irrevocably grants the Lender and its authorised agents the right of access to his/her premises or any other location where the collateral may be found, for the purpose of retrieving the pledged items, should the Borrower default.</li>
</ul>


<div class="section-title">6. Borrower’s Responsibility to Notify</div>
<ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>6.1</strong> The Borrower agrees to notify the Lender via official email within three (3) working days of any change in:
        <ul>
            <li>Permanent or residential address</li>
            <li>Employment status</li>
            <li>Employer or organisational structure</li>
            <li>Change of Salary Date</li>
        </ul>
        Notification must be sent to the Lender’s official email address and copied to <strong>info@bedfordmicrofinance.com</strong>. Failure to comply may result in legal or enforcement action as the Lender deems necessary.
    </li>
</ul>

<div class="section-title">7. Termination</div>
<ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>7.1</strong> This Agreement shall terminate automatically upon full repayment of the loan and all outstanding obligations in accordance with this Agreement.</li>
</ul>


<div class="section-title">8. Severability</div>
<ul style="list-style-type: none; padding-left: 20px;">
    <li><strong>8.1</strong> If any provision of this Agreement is deemed unlawful, invalid, or unenforceable, such provision shall be severed from the Agreement without affecting the validity or enforceability of the remaining provisions.</li>
</ul>

<p style="margin-top: 20px;"><strong>THIS AGREEMENT IS A LEGALLY BINDING CONTRACT. READ AND UNDERSTAND FULLY BEFORE SIGNING.</strong></p>
<div class="page-break"></div>
<div class="section-title">Schedule I – Repayment Details</div>
@php
    // Previous straight-line breakdown (already calculated in controller or blade)
    $repayment = calculateStraightLineBreakdown($loan); // assume this helper is available

    // Charges (optional)
    $managementFee = $loan->charges->firstWhere('name', 'Management Fee')->amount ?? 0;

    // Installment schedule dates
    $firstDate = $loan->repayment_schedules->first()?->due_date;
    $lastDate = $loan->repayment_schedules->last()?->due_date;
@endphp

<table class="table table-bordered">
    <tbody>
        <tr>
            <th>Loan Amount:</th>
            @php
    $finalPrincipal = !empty($loan->disbursed_cash_amount) ? $loan->disbursed_cash_amount : $loan->principal;
@endphp
            <td>ZMW {{ number_format($finalPrincipal, 2) }}</td>
        </tr>
        @php
    $broughtForward = 0;

    if ($loan->previous_loan_id) {
        $bfTransaction = \App\Models\LoanTransaction::where('loan_id', $loan->previous_loan_id)
            ->where('transaction_type', 'principal_bd')
            ->latest('date')
            ->first();

        $broughtForward = $bfTransaction ? $bfTransaction->credit : 0;
    }
@endphp
@if($broughtForward > 0)
<tr>
    <th>Principal Balance:</th>
    <td>ZMW {{ number_format($broughtForward, 2) }}</td>
</tr>
@endif

        <tr>
            <th>Interest Rate:</th>
            <td>{{ number_format($loan->interest_rate, 2) }}% 
            {{ strtolower($loan->interest_rate_type) === 'week' ? 'weekly' : (strtolower($loan->interest_rate_type) === 'month' ? 'monthly' : 'annually') }}

            </td>
        </tr>
        <tr>
            <th>Loan Term:</th>
            <td>{{ $loan->loan_term }} {{ ucfirst($loan->loan_term_type) }}</td>
        </tr>
        <tr>
            <th>Fees:</th>
            <td>{{ $loan->charges->map(fn($charge) => $charge->charge->name . ' - ' . number_format($charge->amount, 2))->implode(', ') }}</td>
        </tr>
        <tr>
            <th>Total Repayment:</th>
            <td>ZMW {{ number_format($repayment['total_payable'], 2) }}</td>
        </tr>
        <tr>
            <th>Monthly Installment:</th>
            <td>ZMW {{ number_format($repayment['monthly_installment'], 2) }}</td>
        </tr>
        @php
    use Carbon\Carbon;

    $dueDates = [];
    $startDate = $loan->expected_first_repayment_date ? Carbon::parse($loan->expected_first_repayment_date) : null;
    $term = $loan->loan_term;
    $termType = strtolower($loan->loan_term_type); // e.g., 'week', 'month', 'year'

    if ($startDate) {
        if ($termType === 'week' || $termType === 'weeks') {
            for ($i = 0; $i < $term; $i++) {
                $dueDates[] = $startDate->copy()->addWeeks($i)->format('d M Y');
            }
        } elseif ($termType === 'month' || $termType === 'months') {
            for ($i = 0; $i < $term; $i++) {
                $dueDates[] = $startDate->copy()->addMonthsNoOverflow($i)->format('d M Y');
            }
        } elseif ($termType === 'year' || $termType === 'years') {
            for ($i = 0; $i < $term * 12; $i++) {
                $dueDates[] = $startDate->copy()->addMonthsNoOverflow($i)->format('d M Y');
            }
        } elseif ($termType === 'day' || $termType === 'days') {
            for ($i = 0; $i < $term; $i++) {
                $dueDates[] = $startDate->copy()->addDays($i)->format('d M Y');
            }
        }
    }
@endphp

<tr>
    <th>Installment Due Dates:</th>
    <td>
        @if (!empty($dueDates))
            {{ implode(', ', $dueDates) }}
        @else
            Not Available
        @endif
    </td>
</tr>


    </tbody>
</table>

<p class="section-title">Bank Details for Repayment</p>
<p>
    Name: Bedford Microfinance Limited<br>
    Account No.: 62848823747<br>
    Bank: FNB<br>
    Branch: Industrial<br>
    Branch Code: 260002
</p>
<div class="page-break"></div>
<div class="section-title">Execution Page</div>
<p><strong>For and on behalf of Bedford Microfinance Limited</strong></p>
<p>APPROVER:<br>Maria Haabazoka<br>Chief Executive Officer<br>Sign: ____________________ Date: _________________</p>
<p>PROCESSING OFFICER:<br>___________________<br>Credit Officer<br>Sign: _______________ Date: ________________</p>

<p><strong>BORROWER</strong></p>
<p>Duly accepted on this ___ day of ___________ 2025, by:</p>
<ul>
    <li>Name: ______________________________</li>
    <li>Residential Address: ________________________________________</li>
    <li>NRC: _________________________</li>
    <li>Employer: _________________________</li>
    <li>Sign: _________________________</li>
</ul>

<p><strong>Next of Kin:</strong></p>
<ul>
    <li>Name: _________________________</li>
    <li>Phone: _________________________</li>
    <li>Address: _________________________</li>
</ul>

<p><strong>GUARANTOR</strong></p>
<ul>
    <li>Name: _________________________</li>
    <li>NRC: _________________________</li>
    <li>Residential Address: _________________________</li>
    <li>Employer: _________________________</li>
    <li>Position: _________________________</li>
</ul>
</body>
</html>

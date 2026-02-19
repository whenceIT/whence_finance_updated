<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report PDF</title>

    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            margin: 20px;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header p {
            margin: 5px 0;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-label {
            text-align: right;
            font-weight: bold;
        }

        .total-amount {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>

@php
    $selectedSections = request()->get('sections', []);
@endphp

<div class="container">
    <div class="header">
        <h1>General Ledger</h1>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
        <p>Branch: {{ $officeName }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Loan ID</th>
                <th>Name</th>
                <th>Client Name</th>
                <th>Credit</th>
                <th>Debit</th>
            </tr>
        </thead>
        <tbody>

<tr>
   <td colspan="3"></td>
    <td class="total-label">Cash Brought Forward:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($openingBalance, 2) }}</td>
</tr>        

<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Income:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($totalIncome, 2) }}</td>
</tr>

{{-- ADVANCES --}}
@if(in_array('advances', $selectedSections))
    @foreach ($advances as $advance)
    <tr>
        <td>{{ $advance->date_approved }}</td>
        <td>{{ $advance->id }}</td>
        <td>-</td>
        <td>Advance</td>
        <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
        <td>{{ number_format($advance->amount, 2) }}</td>
        <td></td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Advances:</td>
    <td></td>
    <td class="total-amount">{{ number_format($advances->sum('amount'), 2) }}</td>
    <td ></td>
</tr>

{{-- ADVANCES PAID --}}
@if(in_array('advances_paid', $selectedSections))
    @foreach ($advancesPaid as $advance)
    <tr>
        <td>{{ $advance->last_update_date }}</td>
        <td>{{ $advance->advance_id }}</td>
        <td>-</td>
        <td>Advance Paid</td>
        <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
        <td></td>
        <td>{{ number_format($advance->amount_paid, 2) }}</td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Advances Paid:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($advancesPaid->sum('amount_paid'), 2) }}</td>
</tr>

{{-- EXPENSES --}}
@if(in_array('expenses', $selectedSections))
    @foreach ($expenses as $expense)
    <tr>
        <td>{{ $expense->date }}</td>
        <td>{{ $expense->id }}</td>
        <td>-</td>
        <td>Expense</td>
        <td>{{ $expense->expense_type }}</td>
        <td></td>
        <td>{{ number_format($expense->amount, 2) }}</td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Expenses:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($expenses->sum('amount'), 2) }}</td>
</tr>

{{-- DEPOSITS --}}
@if(in_array('deposits', $selectedSections))
    @foreach ($deposits as $deposit)
    <tr>
        <td>{{ $deposit->date }}</td>
        <td>{{ $deposit->id }}</td>
        <td>-</td>
        <td>Deposit</td>
        <td>{{ $deposit->deposit_type }}</td>
        <td>{{ number_format($deposit->amount, 2) }}</td>
        <td></td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Deposits:</td>
    <td></td>
    <td class="total-amount">{{ number_format($deposits->sum('amount'), 2) }}</td>
    <td></td>
</tr>

{{-- FULL PAYMENTS --}}
@if(in_array('full_payments', $selectedSections))
    @foreach ($fullPayments as $payment)
    <tr>
        <td>{{ $payment->date }}</td>
        <td>{{ $payment->id }}</td>
        <td>{{ $payment->loan_id }}</td>
        <td>Full Payment</td>
        <td>
            @if(!empty($payment->loan) && !empty($payment->loan->client))
                {{$payment->loan->client->first_name}} {{$payment->loan->client->last_name}}
            @endif
        </td>
        <td>{{ number_format($payment->credit, 2) }}</td>
        <td></td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Full Payments:</td>
    <td></td>
    <td class="total-amount">{{ number_format($fullPayments->sum('credit'), 2) }}</td>
    <td></td>
</tr>

{{-- RELOAN --}}
@if(in_array('reloan', $selectedSections))
    @foreach ($reloanedAmount as $reloan)
    <tr>
        <td>{{ $reloan->date }}</td>
        <td>{{ $reloan->id }}</td>
        <td>{{ $reloan->loan_id }}</td>
        <td>Reloaned Amount</td>
        <td>
            @if(!empty($reloan->loan) && !empty($reloan->loan->client))
                {{$reloan->loan->client->first_name}} {{$reloan->loan->client->last_name}}
            @endif
        </td>
        <td>{{ number_format($reloan->credit, 2) }}</td>
        <td></td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Reloaned:</td>
    <td></td>
    <td class="total-amount">{{ number_format($reloanedAmount->sum('credit'), 2) }}</td>
    <td></td>
</tr>

{{-- PART PAYMENTS --}}
@if(in_array('part_payments', $selectedSections))
    @foreach ($partPayment as $part)
    <tr>
        <td>{{ $part->date }}</td>
        <td>{{ $part->id }}</td>
        <td>{{ $part->loan_id }}</td>
        <td>Part Payment</td>
        <td>
            @if(!empty($part->loan) && !empty($part->loan->client))
                {{$part->loan->client->first_name}} {{$part->loan->client->last_name}}
            @endif
        </td>
        <td></td>
        <td>{{ number_format($part->credit, 2) }}</td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total Part Payments:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($partPayment->sum('credit'), 2) }}</td>
</tr>

{{-- NEW LOANS --}}
@if(in_array('new_loans', $selectedSections))
    @foreach ($newLoans as $loan)
    <tr>
        <td>{{ $loan->date }}</td>
        <td>{{ $loan->id }}</td>
        <td>{{ $loan->loan_id }}</td>
        <td>New Loan</td>
        <td>
            @if(!empty($loan->loan) && !empty($loan->loan->client))
                {{$loan->loan->client->first_name}} {{$loan->loan->client->last_name}}
            @endif
        </td>
        <td></td>
        <td>{{ number_format($loan->debit, 2) }}</td>
    </tr>
    @endforeach
@endif
<tr>
    <td colspan="3"></td>
    <td class="total-label">Total New Loans:</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($newLoans->sum('debit'), 2) }}</td>
</tr>

{{-- GRAND TOTALS --}}
@php
    $totalCredit = $depositsTotal  +
                   $expensesTotal +
                   $advancesTotal +
                   $newLoansTotal;
                  

    $totalDebit = $advancesPaid->sum('amount_paid') +
                   $reloanedAmountTotal +
                   $fullPaymentsTotal +
                  $totalIncome + 
                  $partPaymentTotal + 
                  $openingBalance;
@endphp

<tr>
    <td colspan="3"></td>
    <td class="total-label">Totals:</td>
    <td></td>
    <td class="total-amount">{{ number_format($totalCredit, 2) }}</td>
    <td class="total-amount">{{ number_format($totalDebit, 2) }}</td>
</tr>

<tr>
    <td colspan="3"></td>
    <td class="total-label">Cash Balance at {{$endDate}} :</td>
    <td></td>
    <td></td>
    <td class="total-amount">{{ number_format($closingBalance, 2) }}</td>
</tr>

        </tbody>
    </table>
</div>
</body>
</html>




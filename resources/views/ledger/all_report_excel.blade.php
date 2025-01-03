<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report</title>

    
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
	/* Center-align the credit and debit columns */
th:nth-child(7), th:nth-child(8), td:nth-child(7), td:nth-child(8) {
    text-align: center;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>General Ledger</h1>
            <p>Period: {{ $startDate }} to {{ $endDate }}</p>
            
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Loan ID</th>
                    <th>Branch</th>
                    <th>Name</th>
                    <th>Particulars</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    
                </tr>
            </thead>
            <tbody>
                <!-- Advancess -->
            @foreach ($allBranchData as $branch)
                @foreach ($branch['advances'] as $advance)
                <tr>
                    <td>{{ $advance->date_approved }}</td>
                    <td>{{ $advance->id }}</td>
                    <td> - </td>
                    <td> {{ $branch['officeName'] }} </td>
                    <td> Advance </td>
                    <td> {{ $advance->first_name }} {{ $advance->last_name }}</td>
                    <td></td>
                    <td>{{ number_format($advance->amount, 2) }}</td>
                    
                </tr>
                @endforeach

                <!-- Expenses -->
                @foreach ($branch['expenses'] as $expense)
                <tr>
                    <td>{{ $expense->date }}</td>
                    <td>{{ $expense->id }}</td>
                    <td> - </td>
                    <td> {{ $branch['officeName'] }} </td>
                    <td> Expense </td> 
                    <td>{{ $expense->expense_type }}</td>
                    <td></td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    
                </tr>
                @endforeach

                <!-- Full Payments -->
                @foreach ($branch['fullPayments'] as $payment)
                <tr>
                    <td>{{ $payment->date }}</td>
                    <td>{{ $payment->id }}</td> 
                    <td>{{ $payment->loan_id }}</td>
                    <td> {{ $branch['officeName'] }} </td>
                    <td>Full Payment</td>
                    <td>
                        @if($payment->client && $payment->client->first_name && $payment->client->last_name)
                            {{ $payment->client->first_name }} {{ $payment->client->last_name}}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($payment->credit, 2) }}</td>
                    <td></td>
                    
                </tr>
                @endforeach

                <!-- Reloaned Amount -->
                @foreach ($branch['reloanedAmount'] as $reloan)
                <tr>
                    <td>{{ $reloan->date }}</td>
                    <td>{{ $reloan->id }}</td> 
                    <td>{{ $reloan->loan_id }}</td> 
                    <td> {{ $branch['officeName'] }} </td>
                    <td>Reloaned Amount</td>
                    <td>
                        @if($reloan->client && $reloan->client->first_name && $reloan->client->last_name)
                            {{ $reloan->client->first_name }} {{ $reloan->client->last_name }}
                        @else
                            -
                        @endif
                    </td> 
                    <td>{{ number_format($reloan->credit, 2) }}</td>
                    <td></td>
                    
                </tr>
                @endforeach

                <!-- Part Payments -->
                @foreach ($branch['partPayment'] as $part)
                <tr>
                    <td>{{ $part->date }}</td>
                    <td>{{ $part->id }}</td> 
                    <td>{{ $part->loan_id }}</td> 
                    <td> {{ $branch['officeName'] }} </td>
                    <td>Part Payment</td>
                    <td>
                        @if($part->client && $part->client->first_name && $part->client->last_name)
                            {{ $part->client->first_name }} {{ $part->client->last_name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($part->credit, 2) }}</td>
                    <td></td>
                    
                </tr>
                @endforeach

                <!-- New Loans -->
                @foreach ($branch['newLoans'] as $loan)
                <tr>
                    <td>{{ $loan->date }}</td>
                    <td>{{ $loan->id }}</td> 
                    <td>{{ $loan->loan_id }}</td>
                    <td> {{ $branch['officeName'] }} </td> 
                    <td>New Loan</td>
                    <td>
                        @if($loan->client && $loan->client->first_name)
                            {{ $loan->client->first_name }}
                        @else
                            N/A
                        @endif
                    </td> 
                    <td></td>
                    <td>{{ number_format($loan->debit, 2) }}</td>
                </tr>
                @endforeach
            @endforeach
           
                <!-- Closing Balance -->
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Closing Balance:</strong></td>
                    <td><strong>{{ number_format($totalClosingBalance, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Total Income:</strong></td>
                    <td><strong>{{ number_format($incomeTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report PDF</title>

    <!-- Adjusted Font Style for Smaller Font -->
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px; /* Decreased from the default */
            margin: 20px;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            font-size: 14px; /* Decreased header font size */
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
            padding: 4px; /* Reduced padding */
            text-align: left;
            font-size: 10px; /* Adjusted to smaller font size */
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
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
                <!-- Advances -->
		@foreach ($advances as $advance)
                <tr>
                    <td>{{ $advance->date_approved }}</td>
                    <td>{{ $advance->id }}</td>
                    <td> - </td>
                    <td> Advance </td>
		    <td> {{ $advance->first_name }} {{ $advance->last_name }}</td>
			<td></td>
                    <td>{{ number_format($advance->amount, 2) }}</td>
                    
                </tr>
		@endforeach

		<!--Advances Paid-->
                @foreach ($advancesPaid as $advance)
                <tr>
                    <td>{{ $advance->last_update_date }}</td>
                    <td>{{ $advance->advance_id }}</td>
                    <td> - </td>
                    <td> Advance Paid</td>
                    <td> {{ $advance->first_name }} {{ $advance->last_name }}</td>
                    <td>{{ number_format($advance->amount_paid, 2) }}</td>
                    <td></td>
                </tr>
                @endforeach

                <!-- Expenses -->
                @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->date }}</td>
                    <td>{{ $expense->id }}</td>
                    <td> - </td>
                    <td> Expense </td> 
		    <td>{{ $expense->expense_type }}</td>
			<td></td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    
                </tr>
                @endforeach

                <!-- Full Payments -->
                @foreach ($fullPayments as $payment)

                  <?php
                $client_identification = $payment->client_id;
              //  $client = \App\Models\Client::where('id',$client_identification)->get();
                $client = \App\Models\Client::find($client_identification);
                ?>
                <tr>
                    <td>{{ $payment->date }}</td>
                    <td>{{ $payment->id }}</td> 
                    <td>{{ $payment->loan_id }}</td>
                    <td>Full Payment</td>
                    <td>
                        @if(!empty($payment->loan))
                                    @if(!empty($payment->loan->client))
                                        {{$payment->loan->client->first_name}}  {{$payment->loan->client->last_name}}
                                    @endif
                                @endif
                    </td>
                    
                    
		    <td>{{ number_format($payment->credit, 2) }}</td>
          
			<td></td>
                </tr>
                @endforeach

                <!-- Reloaned Amount -->
                @foreach ($reloanedAmount as $reloan)
                <tr>
                    <td>{{ $reloan->date }}</td>
                    <td>{{ $reloan->id }}</td> 
                    <td>{{ $reloan->loan_id }}</td> 
                    <td>Reloaned Amount</td>
                    <td>
                        @if(!empty($reloan->loan))
                                    @if(!empty($reloan->loan->client))
                                        {{$reloan->loan->client->first_name}}  {{$reloan->loan->client->last_name}}
                                    @endif
                                @endif
                    </td> 
                    
                    
                    <td>{{ number_format($reloan->credit, 2) }}</td>
			<td></td>	       
	</tr>
                @endforeach

                <!-- Part Payments -->
                @foreach ($partPayment as $part)
                <tr>
                    <td>{{ $part->date }}</td>
                    <td>{{ $part->id }}</td> 
                    <td>{{ $part->loan_id }}</td> 
                    <td>Part Payment</td>
                    <td>
                           @if(!empty($part->loan))
                                    @if(!empty($part->loan->client))
                                        {{$part->loan->client->first_name}}  {{$part->loan->client->last_name}}
                                    @endif
                                @endif
                    </td>
                    
                    
		    <td>{{ number_format($part->credit, 2) }}</td>
			<td></td>
                </tr>
                @endforeach

                <!-- New Loans -->
                @foreach ($newLoans as $loan)
                <tr>
                    <td>{{ $loan->date }}</td>
                    <td>{{ $loan->id }}</td> 
                    <td>{{ $loan->loan_id }}</td> 
                    <td>New Loan</td>
                    <td>
                        @if(!empty($loan))
                                    @if(!empty($loan->client))
                                        {{$loan->client->first_name}}  {{$loan->client->last_name}}
                                    @endif
                                @endif
                    </td> 
                   <td></td> 
                    <td>{{ number_format($loan->debit, 2) }}</td>
                   
                </tr>
                @endforeach

                <!-- Closing Balance -->
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Closing Balance:</strong></td>
                    <td><strong>{{ number_format($closingBalance, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Transaction Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px;
        }
        .logo {
            max-width: 150px; /* Adjust as needed */
            height: auto;
            object-fit: contain;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
@if(!empty($start_date) && !empty($end_date))
    <div class="header-container">
        <div class="header-left">
            <h1>Meanwood Finance Corporation Limited</h1>
            <p>MFCL @if($office_id == 0)
    All Offices
@elseif(\App\Models\Office::find($office_id))
    {{ \App\Models\Office::find($office_id)->name }}
@else
    Unknown Office
@endif </p>
            <h2>Transaction Report for period {{ \Carbon\Carbon::parse($start_date)->format('F j, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }}</h2>

            @php
                // Get the logged-in user
                $loggedInUser = Auth::user();
                $loggedInUserName = $loggedInUser ? $loggedInUser->first_name . ' ' . $loggedInUser->last_name : 'Unknown User';
            @endphp

            <p><strong>Printed By:</strong> {{ $loggedInUserName }}</p>
            <p><strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('F j, Y H:i:s') }}</p>
        </div>
        <div class="header-right hidden">
            @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}" class="logo" width="150"/>
            @endif
        </div>
    </div>
@endif
    @if($data->isNotEmpty())
        @foreach ($data as $date => $transactionsByUser)
            <h3>Date: {{ $date }}</h3>
            @foreach ($transactionsByUser as $userId => $transactions)
                <table id="data_table">
                <thead>
                @foreach($data as $day => $users)
<tr class="day-title">
    <td colspan="6"><strong>{{ \Carbon\Carbon::parse($day)->format('F j, Y') }}</strong></td> <!-- Day title -->
</tr>

<tr>
    <th>{{ trans_choice('general.office', 1) }} </th>
    <th>{{ trans_choice('general.account', 1) }} #</th>
    <th>{{ trans_choice('general.name', 1) }}</th>
    <th>{{ trans_choice('general.date', 1) }}</th>
    <th>{{ trans_choice('general.debit', 1) }}</th>
    <th>{{ trans_choice('general.credit', 1) }}</th>
    <th>{{ trans_choice('general.narration', 1) }}</th>
</tr>

@foreach($users as $user_id => $transactions)
    @php
        // Fetch the user's full name using created_by_id from the users table
        $createdById = $transactions->first()->created_by_id ?? null;
        $user = $createdById ? \App\Models\User::find($createdById) : null;
        $userFullName = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User';
    @endphp

    <tr class="user-title">
        <td colspan="6"><strong>User Name: {{ $userFullName }}</strong></td> <!-- Use user name instead of user_id -->
    </tr>

    <tbody>
    @php
        // Group only repayment transactions by loan_id and sum them
        $groupedRepaymentTransactions = $transactions->where('transaction_type', 'repayment')
            ->groupBy('loan_id')->map(function($trans) {
                return [
                    'total_debit' => $trans->sum('debit'),
                    'total_credit' => $trans->sum('credit'),
                    'first_trans' => $trans->first() // To get other details like gl_account, date, client name, etc.
                ];
            });

        // Other transaction types (disbursement, fee, accrual) should not use grouping logic
        $nonGroupedTransactions = $transactions->whereIn('transaction_type', ['disbursement', 'fee', 'accrual','principal_bd','interest_bd','manual_entry','loan_termination','fee_accrual','interest_accrual']);
    @endphp

    {{-- Process repayment transactions with grouping --}}
    @foreach($groupedRepaymentTransactions as $loan_id => $group)
    @php
    $key = $group['first_trans']; // Use the first transaction's details for display
    $debitValue = $group['total_debit'] ?? 0;
    $creditValue = $group['total_credit'] ?? 0;

    // Determine client name for debit or credit line (for individual client or group)
    if (!empty($key->loan)) {
        if ($key->loan->client_type == "client" && !empty($key->loan->client)) {
            if ($key->loan->client->client_type == "individual") {
                $clientFullName = trim($key->loan->client->first_name . ' ' .
                                       ($key->loan->client->middle_name ?? '') . ' ' .
                                       $key->loan->client->last_name);
            } else {
                $clientFullName = $key->loan->client->full_name;
            }
        } elseif (!empty($key->loan->group)) {
            $clientFullName = $key->loan->group->name ?? '-';
        }
    } else {
        $clientFullName = '-';
    }

    $cycle = 0;
    if (!empty($key->loan)) {
        $cycle = \App\Models\Loan::where('client_id', $key->loan->client_id)
            ->where('loan_product_id', $key->loan->loan_product_id)
            ->where('id', '<=', $key->loan->id ?? 0)
            ->count();
    }

    // Construct the generic account number with the cycle included
    $officeExternalId = $key->office->external_id ?? '000';
    $loanProductId = $key->loan->loan_product_id ?? '000';
    $loanId = $key->loan->id ?? '0000';

    // Add the cycle as a 2-digit number to the account number
    $accountNumber = str_pad($officeExternalId, 3, '0', STR_PAD_LEFT) .
                     str_pad($loanProductId, 3, '0', STR_PAD_LEFT) .
                     str_pad($loanId, 4, '0', STR_PAD_LEFT) .
                     str_pad($cycle, 2, '0', STR_PAD_LEFT);

    // Initialize $creditGlCode to avoid "undefined variable" issues
    $creditGlCode = $accountNumber; // Default value for credit GL code

    // Construct the narration by prioritizing the client name and adding notes if available
    $narration = trans_choice('general.' . $key->transaction_type, 1);
    if (!empty($clientFullName) && $clientFullName !== '-') {
        $narration .= ' - ' . $clientFullName;
    }
    if (!empty($key->notes)) {
        $narration .= ' - ' . $key->notes;
    }

    // Repayment logic for debit and credit
    $transactionWithNullSubType = $transactions->firstWhere('transaction_sub_type', null);
    if ($transactionWithNullSubType) {
        $debitGlCode = $transactionWithNullSubType->gl_account->gl_code ?? '-';
        $debitName = $transactionWithNullSubType->gl_account->name ?? '-';
    } else {
        $debitGlCode = $accountNumber;
        $debitName = $clientFullName;
    }

    $creditName = $clientFullName; // Client name for credit
@endphp

<!-- Debit Line for repayment -->
@if($debitValue > 0)
<tr>
<td>{{ $key->office->name }}</td>
    <td>@if($key->name == 'Group Post')
        {{ $key->gl_account->gl_code ?? '-' }}
    @else
        {{ $debitGlCode }}
    @endif</td>
    <td>@if($key->name == 'Group Post')
        {{ $key->gl_account->name }}
    @else
        {{ $debitName }}
    @endif</td>
    <td>{{ isset($key->date) ? $key->date : '-' }}</td>
    <td>{{ number_format($debitValue, 2) }}</td>
    <td>-</td> <!-- Credit column is empty on debit row -->
    <td>{{ $narration }}</td>
</tr>
@endif

    <!-- Credit Line for repayment -->
    @if($creditValue > 0)
    <tr>
    <td>{{ $key->office->name }}</td>
        <td>{{ $creditGlCode }}</td>
        <td>{{ $creditName }}</td>
        <td>{{ isset($key->date) ? $key->date : '-' }}</td>
        <td>-</td> <!-- Debit column is empty on credit row -->
        <td>{{ number_format($creditValue, 2) }}</td>
        <td>{{ $narration }}</td>
    </tr>
    @endif
    @endforeach

    {{-- Process disbursement, fee, and accrual transactions without grouping --}}
    @foreach($nonGroupedTransactions as $key)
    @php
        $debitValue = $key->debit ?? 0;
        $creditValue = $key->credit ?? 0;

        // Determine client name for debit or credit line (for individual client or group)
        if (!empty($key->loan)) {
            if ($key->loan->client_type == "client" && !empty($key->loan->client)) {
                if ($key->loan->client->client_type == "individual") {
                    $clientFullName = trim($key->loan->client->first_name . ' ' .
                                           ($key->loan->client->middle_name ?? '') . ' ' .
                                           $key->loan->client->last_name);
                } else {
                    $clientFullName = $key->loan->client->full_name;
                }
            } elseif (!empty($key->loan->group)) {
                $clientFullName = $key->loan->group->name ?? '-';
            }
        } else {
            $clientFullName = '-';
        }

        $cycle = 0;
    if (!empty($key->loan)) {
        $cycle = \App\Models\Loan::where('client_id', $key->loan->client_id)
            ->where('loan_product_id', $key->loan->loan_product_id)
            ->where('id', '<=', $key->loan->id ?? 0)
            ->count();
    }

    // Construct the generic account number with the cycle included
    $officeExternalId = $key->office->external_id ?? '000';
    $loanProductId = $key->loan->loan_product_id ?? '000';
    $loanId = $key->loan->id ?? '0000';

    // Add the cycle as a 2-digit number to the account number
    $accountNumber = str_pad($officeExternalId, 3, '0', STR_PAD_LEFT) .
                     str_pad($loanProductId, 3, '0', STR_PAD_LEFT) .
                     str_pad($loanId, 4, '0', STR_PAD_LEFT) .
                     str_pad($cycle, 2, '0', STR_PAD_LEFT);
        // Construct the narration by prioritizing the client name and adding notes if available
        $narration = trans_choice('general.' . $key->transaction_type, 1);
        if (!empty($clientFullName) && $clientFullName !== '-') {
            $narration .= ' - ' . $clientFullName;
        }
        if (!empty($key->notes)) {
            $narration .= ' - ' . $key->notes;
        }

        // Fee, disbursement, accrual, principal_bd, and interest_bd logic
                            if ($key->transaction_type == 'disbursement') {
                                $debitGlCode = $accountNumber;
                                $debitName = $clientFullName;
                                $creditGlCode = $key->gl_account->gl_code ?? '-';
                                $creditName = $key->gl_account->name ?? '-';
                            } elseif ($key->transaction_type == 'fee') {
                                $debitGlCode = $key->gl_account->gl_code ?? '-';
                                $debitName = $key->gl_account->name ?? '-';
                                $creditGlCode = $accountNumber;
                                $creditName = $clientFullName;
                            } elseif ($key->transaction_type == 'accrual') {
                                $debitGlCode = $accountNumber;
                                $debitName = $clientFullName;
                                $creditGlCode = $key->gl_account->gl_code ?? '-';
                                $creditName = $key->gl_account->name ?? '-';
                                
                            } elseif ($key->transaction_type == 'loan_termination') {
                                $debitGlCode = $key->gl_account->gl_code;
                                $debitName = $key->gl_account->name ?? '-';
                                $creditGlCode = $accountNumber;
                                $creditName = $clientFullName;
                            } elseif ($key->transaction_type == 'fee_accrual') {
                                $debitGlCode = $key->gl_account->gl_code;
                                $debitName = $key->gl_account->name ?? '-';
                                $creditGlCode = $key->gl_account->gl_code ?? '-';
                                $creditName = $key->gl_account->name ?? '-';
                            } elseif ($key->transaction_type == 'interest_accrual') {
                                $debitGlCode = $accountNumber;
                                $debitName = $clientFullName;
                                $creditGlCode = $key->gl_account->gl_code ?? '-';
                                $creditName = $key->gl_account->name ?? '-';
                            }elseif ($key->transaction_type == 'principal_bd') {
                                $debitGlCode = $key->gl_account->gl_code ?? '-';
                                $debitName = $key->gl_account->name ?? '-';
                                $creditGlCode = $accountNumber;
                                $creditName = $clientFullName;
                            } elseif ($key->transaction_type == 'interest_bd') {
                                $debitGlCode = $key->gl_account->gl_code ?? '-';
                                $debitName = $key->gl_account->name ?? '-';
                                $creditGlCode = $accountNumber;
                                $creditName = $clientFullName;
                            }
    @endphp

    <!-- Debit Line for disbursement, fee, or accrual -->
    @if($debitValue > 0)
    <tr>
    <td>{{ $key->office->name }}</td>
        <td>{{ $debitGlCode }}</td>
        <td>{{ $debitName }}</td>
        <td>{{ isset($key->date) ? $key->date : '-' }}</td>
        <td>{{ number_format($debitValue, 2) }}</td>
        <td>-</td> <!-- Credit column is empty on debit row -->
        <td>{{ $narration }}</td>
    </tr>
    @endif

    <!-- Credit Line for disbursement, fee, or accrual -->
    @if($creditValue > 0)
    <tr>
    <td>{{ $key->office->name }}</td>
        <td>{{ $creditGlCode }}</td>
        <td>{{ $creditName }}</td>
        <td>{{ isset($key->date) ? $key->date : '-' }}</td>
        <td>-</td> <!-- Debit column is empty on credit row -->
        <td>{{ number_format($creditValue, 2) }}</td>
        <td>{{ $narration }}</td>
    </tr>
    @endif
    @endforeach

    </tbody>

    <!-- Total for user on the day -->
    <tr class="bg-dark">
        <td colspan="4"><strong>Total for {{ $userFullName }} on {{ \Carbon\Carbon::parse($day)->format('F j, Y') }}</strong></td> <!-- Use the user's full name instead of user_id -->
        <td><strong>{{ number_format($transactions->sum('debit'), 2) }}</strong></td>
        <td><strong>{{ number_format($transactions->sum('credit'), 2) }}</strong></td>
        <td></td>
    </tr>

    <tr>
        <td colspan="8"><hr style="border: 1px solid black;"></td>
    </tr>
@endforeach

<!-- Total for the day -->
<tr class="bg-dark">
    <td colspan="4"><strong>Total for {{ \Carbon\Carbon::parse($day)->format('F j, Y') }}</strong></td>
    <td><strong>{{ number_format($users->flatten()->sum('debit'), 2) }}</strong></td>
    <td><strong>{{ number_format($users->flatten()->sum('credit'), 2) }}</strong></td>
    <td></td>
</tr>

<tr>
    <td colspan="8"><hr style="border: 2px solid black;"></td>
</tr>
@endforeach

                </thead>
            </table>
            @endforeach
        @endforeach
    @else
        <p>No transactions found for the given period.</p>
    @endif
</body>
</html>

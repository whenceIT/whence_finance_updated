<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Branch Deposit Transactions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        p { font-size: 13px; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        th { background: #667eea; color: #fff; text-align: left; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Branch Deposit Transactions</h1>
    <p>Period: {{ $selectedMonth }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Office</th>
                <th>Amount</th>
                <th>Deposit Type</th>
                <th>Reference</th>
                <th>Method</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deposits as $deposit)
                <tr>
                    <td>{{ $deposit->date ? date('Y-m-d', strtotime($deposit->date)) : 'N/A' }}</td>
                    <td>{{ $deposit->office->name ?? 'N/A' }}</td>
                    <td>{{ number_format($deposit->amount, 2) }}</td>
                    <td>{{ $deposit->depositTypeInfo->name ?? 'N/A' }}</td>
                    <td>{{ $deposit->bankDepositLog->reference_number ?? 'N/A' }}</td>
                    <td>{{ $deposit->bankDepositLog->deposit_method ?? 'N/A' }}</td>
                    <td>
                        @if($deposit->bankDepositLog && $deposit->bankDepositLog->user)
                            {{ $deposit->bankDepositLog->user->first_name ?? '' }} {{ $deposit->bankDepositLog->user->last_name ?? '' }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #888;">No deposit transactions found for the selected period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
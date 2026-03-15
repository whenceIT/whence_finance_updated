<!DOCTYPE html>
<html>
<head>
    <title>Consolidated Age Analysis Report by Principal Balance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>Consolidated Age Analysis Report by Principal Balance</h3>
        @if(!empty($end_date))
            <p>As at: <strong>{{ $end_date }}</strong></p>
        @endif
        <p>Report run date: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ trans_choice('general.loan', 1) }} {{ trans_choice('general.officer', 1) }}</th>
                <th>{{ trans_choice('general.office', 1) }}</th>
                <th>{{ trans_choice('general.client', 1) }}</th>
                <th>{{ trans_choice('general.phone', 1) }}</th>
                <th>{{ trans_choice('general.loan', 1) }} {{ trans_choice('general.id', 1) }}</th>
                <th>{{ trans_choice('general.product', 1) }}</th>
                <th>{{ trans_choice('general.fund', 1) }}</th>
                <th>Current</th>
                <th>{{ trans_choice('general.below', 1) }} 30 {{ trans_choice('general.day', 2) }}</th>
                <th>30 - 89 {{ trans_choice('general.day', 2) }}</th>
                <th>90 - 119 {{ trans_choice('general.day', 2) }}</th>
                <th>120 - 179 {{ trans_choice('general.day', 2) }}</th>
                <th>{{ trans_choice('general.over', 1) }} 179 {{ trans_choice('general.day', 2) }}</th>
                <th>{{ trans_choice('general.total', 1) }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key)
                <?php
                    // Calculate loan details
                    $on_time_amount = 0; // Example calculation
                    $p_30_amount = 0;
                    $p_60_amount = 0;
                    $p_90_amount = 0;
                    $p_180_amount = 0;
                    $p_180_plus_amount = 0;
                    $p_amount = $on_time_amount + $p_30_amount + $p_60_amount + $p_90_amount + $p_180_amount + $p_180_plus_amount;
                ?>
                <tr>
                    <td>{{ $key->loan_officer->first_name ?? '' }} {{ $key->loan_officer->last_name ?? '' }}</td>
                    <td>{{ $key->office->name ?? 'N/A' }}</td>
                    <td>
                        @if($key->client_type == "client")
                            @if($key->client->client_type == "individual")
                                {{ $key->client->first_name }} {{ $key->client->middle_name }} {{ $key->client->last_name }}
                            @elseif($key->client->client_type == "business")
                                {{ $key->client->full_name }}
                            @endif
                        @elseif($key->client_type == "group")
                            {{ $key->group->name ?? '' }}
                        @endif
                    </td>
                    <td>{{ $key->client->mobile ?? $key->group->mobile ?? 'N/A' }}</td>
                    <td>{{ $key->id }}</td>
                    <td>{{ $key->loan_product->name ?? 'N/A' }}</td>
                    <td>{{ 'Fund Name' }}</td> <!-- Example fund, you may need to customize this -->
                    <td>{{ number_format($on_time_amount, 2) }}</td>
                    <td>{{ number_format($p_30_amount, 2) }}</td>
                    <td>{{ number_format($p_60_amount, 2) }}</td>
                    <td>{{ number_format($p_90_amount, 2) }}</td>
                    <td>{{ number_format($p_180_amount, 2) }}</td>
                    <td>{{ number_format($p_180_plus_amount, 2) }}</td>
                    <td>{{ number_format($p_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th>{{ number_format($total_on_time_amount, 2) }}</th>
                <th>{{ number_format($total_p_30_amount, 2) }}</th>
                <th>{{ number_format($total_p_60_amount, 2) }}</th>
                <th>{{ number_format($total_p_90_amount, 2) }}</th>
                <th>{{ number_format($total_p_180_amount, 2) }}</th>
                <th>{{ number_format($total_p_180_plus_amount, 2) }}</th>
                <th>{{ number_format($total_p_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

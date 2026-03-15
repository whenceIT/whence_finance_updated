<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Book</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { font-weight: bold; }
    </style>
</head>
<body>
    <h3>Loan Book Report</h3>
    <p>
        Start Date: {{ $start_date }} <br>
        End Date: {{ $end_date }}
    </p>

    <table>
        <thead>
        <tr>
            <th>Loan ID</th>
            <th>Client</th>
            <th>Office</th>
            <th>Officer</th>
            <th>Status</th>
            <th>Disbursement Date</th>
            <th>Principal</th>
            <th>Principal Paid</th>
            <th>Principal Balance</th>
            <th>Interest Paid</th>
            <th>Interest Balance</th>
            <th>Fees Paid</th>
            <th>Fees Balance</th>
            <th>True Balance</th>
            <th>Cycle</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $loan)
            <tr>
                <td>{{ $loan->id }}</td>
                <td>{{ trim(optional($loan->client)->first_name.' '.optional($loan->client)->last_name) }}</td>
                <td>{{ optional($loan->office)->name }}</td>
                <td>{{ trim(optional($loan->loan_officer)->first_name.' '.optional($loan->loan_officer)->last_name) }}</td>
                <td>{{ $loan->status }}</td>
                <td>{{ $loan->disbursement_date }}</td>
                <td>{{ $loan->principal }}</td>
                <td>{{ $loan->principal_paid }}</td>
                <td>{{ $loan->principal_balance }}</td>
                <td>{{ $loan->interest_paid }}</td>
                <td>{{ $loan->interest_balance }}</td>
                <td>{{ $loan->fees_paid }}</td>
                <td>{{ $loan->fees_balance }}</td>
                <td>{{ $loan->true_balance }}</td>
                <td>{{ $loan->cycle }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

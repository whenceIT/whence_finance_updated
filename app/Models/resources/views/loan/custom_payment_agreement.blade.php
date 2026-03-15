<!DOCTYPE html>
<html>
<head>
    <title>Payment Agreement Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 2cm;
        }
        h2 {
            text-align: center;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .logo-container {
        text-align: center;
        margin-bottom: 10px;
    }
    </style>
</head>
<body>
@if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
    <div class="logo-container">
        <img src="{{ asset('uploads/' . \App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
             class="img-responsive" width="120" />
    </div>
@endif
    <h2>PAYMENT AGREEMENT FORM</h2>

    <p>
        I/We <strong>{{ $loan->client->full_name }}</strong> being the Borrower /Guarantor under credit contract
        No. <strong>{{ $loan->id }}</strong> made on the <strong>{{ \Carbon\Carbon::parse($loan->created_at)->format('jS') }}</strong>
        day of <strong>{{ \Carbon\Carbon::parse($loan->created_at)->format('F Y') }}</strong> hereby acknowledge having an overdue
        debt at Vantage Finance Limited with a total debt as of
        <strong>{{ \Carbon\Carbon::now()->format('jS F, Y') }}</strong> broken down as follows:
    </p>

    <p>
        Principal amount: K<strong>{{ number_format($loan->repayment_schedules->sum('principal'), 2) }}</strong><br>
        Interest Due: K<strong>{{ number_format($loan->repayment_schedules->sum('interest'), 2) }}</strong><br>
        Overdue Charges: K<strong>0.00</strong>
    </p>

    <p>In order to settle the above-mentioned total debt and all future charges, I/We commit to pay as follows:</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Date</th>
                <th>Amount to Pay (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->repayment_schedules as $schedule)
                <tr>
                    <td>{{ $schedule->installment }}</td>
                    <td>{{ \Carbon\Carbon::parse($schedule->due_date)->format('d-m-Y') }}</td>
                    <td>{{ number_format($schedule->principal + $schedule->interest, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>
        It is agreed by the Parties as follows:<br><br>
        ___________________________________________________________________________________________<br><br>
        ___________________________________________________________________________________________<br><br>
        ___________________________________________________________________________________________<br><br>
        I/We take note of the legal consequences in case of failure to adhere to this and accept this agreement.
    </p>

    <p>
        Date: ______ day of __________ 20____ <br>
        Signed at: Vantage Finance Limited <br><br>
        Signature of Client/Guarantor/Other Party: _________________________<br><br>
        Name and Signature (VF) Staff: _________________________________
    </p>
</body>
</html>

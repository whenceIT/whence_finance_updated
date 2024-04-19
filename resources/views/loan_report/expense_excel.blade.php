<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report </title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }


    .style-1 {
        color: white;
        padding-left: 10pt;
        font-size: 14pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
        background-color: #339933;
    }
    .style-2 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
    .style-3 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
</style>
</head>
<body>
    
   
    <h1>Expense Report</h1>
    <p>Start Date: {{ $start_date }}</p>
    <p>End Date: {{ $end_date }}</p>
    <p>Office ID: {{ $office_id }} </p>
    <p>Office Name: {{ $office->name }} </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Expense</th>
                <th>Expense Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
           
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->id }}</td>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->type ? $expense->type->name : '-' }}</td>
                    <td>{{ $expense->amount }}</td>
                    
                </tr>
            @endforeach
            
            <tr>
                <td colspan="3"></td>
                <td><b>Total</b></td>
                <td>{{ number_format($expenses->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
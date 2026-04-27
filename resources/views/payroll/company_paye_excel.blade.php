<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight:bold; font-size:16px;">
                Whence Financial Services - PAYE Report ({{ date("F Y", strtotime($month)) }})
            </th>
        </tr>

        {{-- Totals --}}
        <tr>
            <th>Total Employees</th>
            <th>{{ $totals['employees'] }}</th>
        </tr>
        <tr>
            <th>Total Basic Pay</th>
            <th>{{ $totals['basic_pay'] }}</th>
        </tr>
        <tr>
            <th>Total PAYE</th>
            <th>{{ $totals['paye'] }}</th>
        </tr>

        <tr></tr>

        {{-- Headers --}}
        <tr>
            <th>TPIN</th>
            <th>Employee Name</th>
            <th>Employment Nature</th>
            <th>Gross Emoluments</th>
            <th>Chargeable Emoluments</th>
            <th>Total Tax Credited</th>
            <th>Tax Deducted</th>
            <th>Tax Adjusted</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
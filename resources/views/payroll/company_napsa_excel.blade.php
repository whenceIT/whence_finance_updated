<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight:bold; font-size:16px;">
                Whence Financial Services - NAPSA Report ({{ date("F Y", strtotime($month)) }})
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
            <th>Total NAPSA</th>
            <th>{{ $totals['napsa'] }}</th>
        </tr>

        <tr></tr>

        {{-- Headers --}}
        <tr>
            <th>Company NAPSA No</th>
            <th>Year</th>
            <th>Month</th>
            <th>Social Security Number (NAPSA)</th>
            <th>NRC</th>
            <th>Employee Name</th>
            <th>Date of Birth</th>
            <th>Gross Pay</th>
            <th>Employee Contribution</th>
            <th>Employer Contribution</th>
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
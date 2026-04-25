@php
    $displayMonth = \Carbon\Carbon::parse($month)->format('F Y');
@endphp

<table>

    {{-- ✅ HEADER --}}
    <tr>
        <td colspan="12" style="font-size:18px; font-weight:bold;">
            Whence Financial Services
        </td>
    </tr>

    <tr>
        <td colspan="12" style="font-size:14px;">
            {{ $displayMonth }} — Monthly Payroll Register
        </td>
    </tr>

    <tr></tr>

    {{-- ✅ "CARDS" (SIMULATED) --}}
    <tr>
        <td colspan="2" style="background:#00c0ef; color:white; font-weight:bold;">
            Total Employees
        </td>
        <td colspan="2" style="background:#00a65a; color:white; font-weight:bold;">
            Total Basic Pay
        </td>
        <td colspan="2" style="background:#0073b7; color:white; font-weight:bold;">
            Total Net Pay
        </td>
        <td colspan="2" style="background:#f39c12; color:white; font-weight:bold;">
            Total NAPSA
        </td>
        <td colspan="2" style="background:#dd4b39; color:white; font-weight:bold;">
            Total NHIMA
        </td>
        <td colspan="2" style="background:#605ca8; color:white; font-weight:bold;">
            Total PAYE
        </td>
    </tr>

    <tr colspan="2" style="background:#f39c12; color:white; font-weight:bold;">
        <td colspan="2">{{ $data->count() }}</td>
        <td colspan="2">{{ number_format($totals['basic_pay'],2) }}</td>
        <td colspan="2">{{ number_format($totals['net_pay'],2) }}</td>
        <td colspan="2">{{ number_format($totals['napsa'],2) }}</td>
        <td colspan="2">{{ number_format($totals['nhima'],2) }}</td>
        <td colspan="2">{{ number_format($totals['paye'],2) }}</td>
    </tr>

    <tr></tr>

    {{-- ✅ TABLE HEADER --}}
    <tr style="background:#1f2d3d;color:white;">
        <th>Staff</th>
        <th>Branch</th>
        <th>Email</th>

        @foreach($fields as $field)
            <th>{{ $field->name }}</th>
        @endforeach

        <th>Net Pay</th>
        <th>Job Level</th>
        <th>Date</th>
    </tr>

    {{-- ✅ DATA --}}
    @foreach($data as $row)
        <tr>
            <td>{{ $row['Staff'] }}</td>
            <td>{{ $row['Branch'] }}</td>
            <td>{{ $row['Email'] }}</td>

            @foreach($fields as $field)
                <td>{{ number_format($row[$field->name] ?? 0, 2) }}</td>
            @endforeach

            <td>{{ number_format($row['Net Pay'], 2) }}</td>
            <td>{{ $row['Job Level'] }}</td>
            <td>{{ $row['Date'] }}</td>
        </tr>
    @endforeach

    {{-- ✅ TOTAL ROW --}}
    <tr style="font-weight:bold; background:#f4f6f9;">
        <td colspan="3">TOTALS</td>

        @foreach($fields as $field)
            @php $name = strtolower($field->name); @endphp
            <td>
                @if(str_contains($name, 'basic'))
                    {{ number_format($totals['basic_pay'],2) }}
                @elseif(str_contains($name, 'napsa'))
                    {{ number_format($totals['napsa'],2) }}
                @elseif(str_contains($name, 'nhima'))
                    {{ number_format($totals['nhima'],2) }}
                @elseif(str_contains($name, 'paye'))
                    {{ number_format($totals['paye'],2) }}
                @else
                    -
                @endif
            </td>
        @endforeach

        <td>{{ number_format($totals['net_pay'],2) }}</td>
        <td></td>
        <td></td>
    </tr>

</table>
<table>
    <thead>
        <tr style="background:#007bff;color:white;">
            <th>Staff</th>
            @php
                $fields = App\Models\PayrollTemplateMeta::all();
            @endphp
            @foreach($fields as $field)
                <th>{{ $field->name }}</th>
            @endforeach
            <th>Net Pay</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $payroll) {{-- Use $data directly, not $data['data'] --}}
            <tr>
                <td>{{ $payroll['Staff'] }}</td>
                @foreach($fields as $field)
                    <td>{{ isset($payroll[$field->name]) ? number_format($payroll[$field->name],2) : '0.00' }}</td>
                @endforeach
                <td>{{ isset($payroll['Net Pay']) ? number_format($payroll['Net Pay'],2) : '0.00' }}</td>
                <td>{{ $payroll['Date'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expenses Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h2   { margin: 0 0 10px; }
        .meta { margin-bottom: 15px; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f0f0f0; text-align: left; }
        td.right, th.right { text-align: right; }
    </style>
</head>
<body>
    <h2>Expenses Report</h2>
    <div class="meta">
        <strong>Range:</strong>
        {{ $start ? $start->toDateString() : 'ALL' }} → {{ $end ? $end->toDateString() : 'ALL' }}
        @if(!empty($office_id))
            | <strong>Office:</strong> {{ optional(\App\Models\Office::find($office_id))->name }}
        @endif
        | <strong>Generated:</strong> {{ $generated }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th class="right">Amount</th>
                <th>Date</th>
                <th>Office</th>
                <th>Description</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>{{ optional($row->type)->name }}</td>
                    <td class="right">{{ number_format($row->amount,2) }}</td>
                    <td>{{ $row->date }}</td>
                    <td>{{ $row->office->name }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ optional($row->created_by)->first_name }} {{ optional($row->created_by)->last_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No expenses found for this range.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="right">{{ number_format($data->sum('amount'), 2) }}</th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>

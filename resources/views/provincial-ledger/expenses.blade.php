@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="margin:0;">Provincial Expenses</h3>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-danger" onclick="openTransactionModal('expense')"><i class="fa fa-plus"></i> Record Expense</button>
        </div>
    </div>
    
    <div class="well">
        <strong>Total Expenses:</strong> K{{ number_format($total, 2) }}
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Province</th>
                <th>Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $tx)
            <tr>
                <td>{{ $tx->transaction_date }}</td>
                <td>{{ $tx->title }}</td>
                <td>{{ $tx->province->name ?? 'N/A' }}</td>
                <td class="text-right">K{{ number_format($tx->amount, 2) }}</td>
                <td>{{ $tx->reference_number ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
</table>
</div>
    @include('provincial-ledger._partials.transaction-modal')
</div>
@endsection
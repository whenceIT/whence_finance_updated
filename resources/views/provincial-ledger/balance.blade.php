@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <h3>Provincial Cash Balance</h3>
    
    <div class="well">
        <strong>Net Balance:</strong> K{{ number_format($netBalance, 2) }}
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Province</th>
                <th>Total Income</th>
                <th>Total Expenses</th>
                <th>Net Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balanceByProvince as $item)
            <tr>
                <td>{{ $item->province->name ?? 'N/A' }}</td>
                <td class="text-right">K{{ number_format($item->income, 2) }}</td>
                <td class="text-right">K{{ number_format($item->expenses, 2) }}</td>
                <td class="text-right">K{{ number_format($item->income - $item->expenses, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
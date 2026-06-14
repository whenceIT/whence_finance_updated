@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="margin:0;">Provincial Ledger Dashboard</h3>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-primary" onclick="openTransactionModal('income')"><i class="fa fa-plus"></i> Record Income</button>
            <button type="button" class="btn btn-danger" onclick="openTransactionModal('expense')"><i class="fa fa-plus"></i> Record Expense</button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">Total Income</div>
                <div class="panel-body">
                    <h2 class="text-success">K{{ number_format($totalIncome, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">Total Expenses</div>
                <div class="panel-body">
                    <h2 class="text-danger">K{{ number_format($totalExpenses, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">Net Balance</div>
                <div class="panel-body">
                    <h2 class="text-info">K{{ number_format($netBalance, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <h4>Recent Transactions</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Province</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                    <tr>
                        <td>{{ $tx->transaction_date }}</td>
                        <td>{{ $tx->title }}</td>
                        <td>{{ ucfirst($tx->type) }}</td>
                        <td>{{ $tx->province->name ?? 'N/A' }}</td>
                        <td>K{{ number_format($tx->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    @include('provincial-ledger._partials.transaction-modal')
</div>
@endsection
@extends('layouts.master')
@section('title', 'Workstream 1 Expenses')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Workstream 1 — Expense Register</h3>
        <div class="box-tools pull-right">
            @if(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::hasAccess('workstream1.expenses.create'))
                <a href="{{ url('workstream1/expenses/create') }}" class="btn btn-primary btn-sm">Submit Expense</a>
            @endif
            <a href="{{ url('workstream1/expenses/dashboard') }}" class="btn btn-default btn-sm">Dashboard</a>
        </div>
    </div>
    <div class="box-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        <form method="get" class="form-inline" style="margin-bottom:15px;">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" aria-label="Start date">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" aria-label="End date">
            <select name="status" class="form-control">
                <option value="">All statuses</option>
                @foreach(['pending','approved','declined'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @if($offices->count())
                <select name="office_id" class="form-control">
                    <option value="">All branches</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ (string)request('office_id') === (string)$office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
            @endif
            <button class="btn btn-success" type="submit">Filter</button>
            <a class="btn btn-default" href="{{ url('workstream1/expenses') }}">Reset</a>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead><tr><th>ID</th><th>Date</th><th>Branch</th><th>Expense</th><th>Type</th><th>Amount</th><th>Status</th><th>Payment</th><th></th></tr></thead>
                <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->id }}</td>
                        <td>{{ $expense->date }}</td>
                        <td>{{ optional($expense->office)->name ?: 'N/A' }}</td>
                        <td>{{ $expense->name }}</td>
                        <td>{{ optional($expense->type)->name ?: 'N/A' }}</td>
                        <td>{{ number_format((float)$expense->amount, 2) }}</td>
                        <td>
                            @if($expense->status === 'approved') <span class="label label-success">Approved</span>
                            @elseif($expense->status === 'declined') <span class="label label-danger">Declined</span>
                            @else <span class="label label-warning">Pending</span> @endif
                        </td>
                        <td>{{ optional($expense->latestPayment)->status ?: 'Not recorded' }}</td>
                        <td><a class="btn btn-info btn-xs" href="{{ url('workstream1/expenses/'.$expense->id) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No expenses found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-center">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection

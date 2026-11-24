@extends('layouts.master')
@section('title')
    Daily Reports
@endsection
@section('content')
    <section class="content-header">
        <h1>Daily Reports</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('daily-reports.index') }}"><i class="fa fa-dashboard"></i> Daily Reports</a></li>
            <li class="active">All Records</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Filters</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <a href="{{ route('daily-reports.income.create') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> Record Income
                                </a>
                                <a href="{{ route('daily-reports.expense.create') }}" class="btn btn-danger btn-sm">
                                    <i class="fa fa-minus"></i> Record Expense
                                </a>
                                <a href="{{ route('daily-reports.branch-report') }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-bar-chart"></i> Branch Report
                                </a>
                                <a href="{{ route('daily-reports.reports') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-file-text"></i> Financial Reports
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <form method="GET" action="{{ route('daily-reports.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Branch</label>
                                        <select name="branch_id" class="form-control select2" style="width: 100%;">
                                            <option value="">All Branches</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Date From</label>
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Date To</label>
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Income Category</label>
                                        <select name="category" class="form-control select2" style="width: 100%;">
                                            <option value="">All Categories</option>
                                            @foreach($incomeCategories as $key => $category)
                                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Expense Status</label>
                                        <select name="status" class="form-control select2" style="width: 100%;">
                                            <option value="">All Status</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row margin-top">
                            <div class="col-md-4">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Income (Filtered)</span>
                                        <span class="info-box-number">{{ number_format($totalIncome, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Expenses (Filtered)</span>
                                        <span class="info-box-number">{{ number_format($totalExpense, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-{{ $totalIncome - $totalExpense >= 0 ? 'green' : 'red' }}">
                                    <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Net Position</span>
                                        <span class="info-box-number">{{ number_format($totalIncome - $totalExpense, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Income Records</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        @if($incomes->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Branch</th>
                                        <th>Category</th>
                                        <th>Source</th>
                                        <th>Payment Method</th>
                                        <th>Received From</th>
                                        <th class="text-right">Amount</th>
                                        <th>Recorded By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomes as $income)
                                        <tr>
                                            <td>{{ $income->income_date->format('Y-m-d') }}</td>
                                            <td>{{ $income->reference_number ?: 'N/A' }}</td>
                                            <td>{{ $income->branch ? $income->branch->name : 'N/A' }}</td>
                                            <td><span class="label bg-green">{{ $income->income_category }}</span></td>
                                            <td>{{ $income->income_source ?: 'N/A' }}</td>
                                            <td><span class="label bg-blue">{{ $income->payment_method }}</span></td>
                                            <td>{{ $income->received_from ?: 'N/A' }}</td>
                                            <td class="text-right text-green"><strong>{{ number_format($income->amount, 2) }}</strong></td>
                                            <td>{{ $income->recordedBy ? $income->recordedBy->name : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('daily-reports.income.edit', $income->id) }}" class="btn btn-default" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('daily-reports.income.delete', $income->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this income record?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-default" title="Delete"><i class="fa fa-trash text-red"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa fa-arrow-up fa-2x text-green"></i>
                                <p class="text-muted">No income records found.</p>
                            </div>
                        @endif
                    </div>
                    <div class="box-footer clearfix">
                        {{ $incomes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Expense Records</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        @if($expenses->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Branch</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Paid To</th>
                                        <th class="text-right">Amount</th>
                                        <th>Recorded By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                            <td>{{ $expense->reference_number ?: 'N/A' }}</td>
                                            <td>{{ $expense->branch ? $expense->branch->name : 'N/A' }}</td>
                                            <td><span class="label bg-red">{{ $expense->expense_category }}</span></td>
                                            <td>{{ $expense->expense_type ?: 'N/A' }}</td>
                                            <td><span class="label bg-blue">{{ $expense->payment_method }}</span></td>
                                            <td>
                                                <span class="label bg-{{ $expense->status == 'Approved' ? 'green' : ($expense->status == 'Pending' ? 'yellow' : 'red') }}">
                                                    {{ $expense->status }}
                                                </span>
                                            </td>
                                            <td>{{ $expense->paid_to ?: 'N/A' }}</td>
                                            <td class="text-right text-red"><strong>{{ number_format($expense->amount, 2) }}</strong></td>
                                            <td>{{ $expense->recordedBy ? $expense->recordedBy->name : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('daily-reports.expense.edit', $expense->id) }}" class="btn btn-default" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('daily-reports.expense.delete', $expense->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this expense record?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-default" title="Delete"><i class="fa fa-trash text-red"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa fa-arrow-down fa-2x text-red"></i>
                                <p class="text-muted">No expense records found.</p>
                            </div>
                        @endif
                    </div>
                    <div class="box-footer clearfix">
                        {{ $expenses->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>
@endsection
@extends('layouts.master')
@section('title')
    Branch Financial Report
@endsection
@section('content')
    <section class="content-header">
        <h1>Branch Financial Report</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('daily-reports.index') }}"><i class="fa fa-dashboard"></i> Daily Reports</a></li>
            <li class="active">Branch Report</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Filter Options</h3>
                    </div>
                    <div class="box-body">
                        <form method="GET" action="{{ route('daily-reports.branch-report') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Branch</label>
                                        <select name="branch_id" class="form-control select2" style="width: 100%;">
                                            <option value="">All Branches</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Date From</label>
                                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Date To</label>
                                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Generate Report</button>
                                            <a href="{{ route('daily-reports.branch-report') }}" class="btn btn-default">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(count($branchReports) > 0)
            @foreach($branchReports as $report)
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header with-border bg-{{ $report['net_position'] >= 0 ? 'green' : 'red' }}">
                                <h3 class="box-title">{{ $report['branch']->name }}</h3>
                                <div class="box-tools pull-right">
                                    <span class="badge bg-{{ $report['net_position'] >= 0 ? 'green' : 'red' }}" style="font-size: 14px;">
                                        Net: {{ number_format($report['net_position'], 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-green">
                                            <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Income</span>
                                                <span class="info-box-number">{{ number_format($report['total_income'], 2) }}</span>
                                                <small>{{ $report['income_count'] }} transactions</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-red">
                                            <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Expenses</span>
                                                <span class="info-box-number">{{ number_format($report['total_expense'], 2) }}</span>
                                                <small>{{ $report['expense_count'] }} transactions</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-{{ $report['net_position'] >= 0 ? 'green' : 'red' }}">
                                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Net Position</span>
                                                <span class="info-box-number">{{ number_format($report['net_position'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-blue">
                                            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Expense Ratio</span>
                                                <span class="info-box-number">{{ $report['total_income'] > 0 ? number_format(($report['total_expense'] / $report['total_income']) * 100, 1) : 0 }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Income by Category</h4>
                                        @if($report['income_by_category']->count() > 0)
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th class="text-right">Amount</th>
                                                        <th class="text-right">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($report['income_by_category'] as $category => $amount)
                                                        <tr>
                                                            <td>{{ $category }}</td>
                                                            <td class="text-right">{{ number_format($amount, 2) }}</td>
                                                            <td class="text-right">{{ $report['total_income'] > 0 ? number_format(($amount / $report['total_income']) * 100, 1) : 0 }}%</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="bg-green">
                                                        <td><strong>Total</strong></td>
                                                        <td class="text-right"><strong>{{ number_format($report['total_income'], 2) }}</strong></td>
                                                        <td class="text-right"><strong>100%</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted">No income records for this period.</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Expenses by Category</h4>
                                        @if($report['expense_by_category']->count() > 0)
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th class="text-right">Amount</th>
                                                        <th class="text-right">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($report['expense_by_category'] as $category => $amount)
                                                        <tr>
                                                            <td>{{ $category }}</td>
                                                            <td class="text-right">{{ number_format($amount, 2) }}</td>
                                                            <td class="text-right">{{ $report['total_expense'] > 0 ? number_format(($amount / $report['total_expense']) * 100, 1) : 0 }}%</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="bg-red">
                                                        <td><strong>Total</strong></td>
                                                        <td class="text-right"><strong>{{ number_format($report['total_expense'], 2) }}</strong></td>
                                                        <td class="text-right"><strong>100%</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted">No approved expenses for this period.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Consolidated Summary</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th class="text-right">Income</th>
                                        <th class="text-right">Expenses</th>
                                        <th class="text-right">Net Position</th>
                                        <th class="text-right">Income Count</th>
                                        <th class="text-right">Expense Count</th>
                                        <th class="text-right">Expense Ratio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandIncome = 0;
                                        $grandExpense = 0;
                                        $grandIncomeCount = 0;
                                        $grandExpenseCount = 0;
                                    @endphp
                                    @foreach($branchReports as $report)
                                        @php
                                            $grandIncome += $report['total_income'];
                                            $grandExpense += $report['total_expense'];
                                            $grandIncomeCount += $report['income_count'];
                                            $grandExpenseCount += $report['expense_count'];
                                        @endphp
                                        <tr>
                                            <td>{{ $report['branch']->name }}</td>
                                            <td class="text-right">{{ number_format($report['total_income'], 2) }}</td>
                                            <td class="text-right">{{ number_format($report['total_expense'], 2) }}</td>
                                            <td class="text-right {{ $report['net_position'] >= 0 ? 'text-green' : 'text-red' }}">
                                                <strong>{{ number_format($report['net_position'], 2) }}</strong>
                                            </td>
                                            <td class="text-right">{{ $report['income_count'] }}</td>
                                            <td class="text-right">{{ $report['expense_count'] }}</td>
                                            <td class="text-right">{{ $report['total_income'] > 0 ? number_format(($report['total_expense'] / $report['total_income']) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray">
                                        <td><strong>GRAND TOTAL</strong></td>
                                        <td class="text-right"><strong>{{ number_format($grandIncome, 2) }}</strong></td>
                                        <td class="text-right"><strong>{{ number_format($grandExpense, 2) }}</strong></td>
                                        <td class="text-right {{ ($grandIncome - $grandExpense) >= 0 ? 'text-green' : 'text-red' }}">
                                            <strong>{{ number_format($grandIncome - $grandExpense, 2) }}</strong>
                                        </td>
                                        <td class="text-right"><strong>{{ $grandIncomeCount }}</strong></td>
                                        <td class="text-right"><strong>{{ $grandExpenseCount }}</strong></td>
                                        <td class="text-right"><strong>{{ $grandIncome > 0 ? number_format(($grandExpense / $grandIncome) * 100, 1) : 0 }}%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-body text-center">
                            <i class="fa fa-search fa-3x text-muted"></i>
                            <h3 class="text-muted">No Data Found</h3>
                            <p class="text-muted">No financial records found for the selected criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>
@endsection
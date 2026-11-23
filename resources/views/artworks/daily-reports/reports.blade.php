@extends('layouts.master')
@section('title')
    Daily Reports Summary
@endsection
@section('content')
    <section class="content-header">
        <h1>Daily Reports Summary</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('daily-reports.index') }}"><i class="fa fa-dashboard"></i> Daily Reports</a></li>
            <li class="active">Financial Reports</li>
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
                        <form method="GET" action="{{ route('daily-reports.reports') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Branch</label>
                                        <select name="branch_id" class="form-control select2" style="width: 100%;">
                                            <option value="">All Branches</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
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
                                            <a href="{{ route('daily-reports.reports') }}" class="btn btn-default">Reset</a>
                                            <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Income</span>
                        <span class="info-box-number">{{ number_format($totalIncome, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expenses</span>
                        <span class="info-box-number">{{ number_format($totalExpense, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-{{ $netPosition >= 0 ? 'green' : 'red' }}">
                    <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Position</span>
                        <span class="info-box-number">{{ number_format($netPosition, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Daily Trend Chart</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="trendChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Income by Category</h3>
                    </div>
                    <div class="box-body">
                        @if($incomeByCategory->count() > 0)
                            <div class="chart-responsive">
                                <canvas id="incomeCategoryChart" style="height: 300px;"></canvas>
                            </div>
                            <table class="table table-striped table-bordered margin-top">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Count</th>
                                        <th class="text-right">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeByCategory as $item)
                                        <tr>
                                            <td>{{ $item->income_category }}</td>
                                            <td class="text-right">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-right">{{ $item->count }}</td>
                                            <td class="text-right">{{ $totalIncome > 0 ? number_format(($item->total / $totalIncome) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">No income data for this period.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Expenses by Category</h3>
                    </div>
                    <div class="box-body">
                        @if($expenseByCategory->count() > 0)
                            <div class="chart-responsive">
                                <canvas id="expenseCategoryChart" style="height: 300px;"></canvas>
                            </div>
                            <table class="table table-striped table-bordered margin-top">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Count</th>
                                        <th class="text-right">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseByCategory as $item)
                                        <tr>
                                            <td>{{ $item->expense_category }}</td>
                                            <td class="text-right">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-right">{{ $item->count }}</td>
                                            <td class="text-right">{{ $totalExpense > 0 ? number_format(($item->total / $totalExpense) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">No expense data for this period.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Income by Branch</h3>
                    </div>
                    <div class="box-body">
                        @if($incomeByBranch->count() > 0)
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Transactions</th>
                                        <th class="text-right">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeByBranch as $item)
                                        <tr>
                                            <td>{{ $item->branch ? $item->branch->name : 'Unknown' }}</td>
                                            <td class="text-right">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-right">{{ $item->count }}</td>
                                            <td class="text-right">{{ $totalIncome > 0 ? number_format(($item->total / $totalIncome) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">No income data by branch.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Expenses by Branch</h3>
                    </div>
                    <div class="box-body">
                        @if($expenseByBranch->count() > 0)
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Transactions</th>
                                        <th class="text-right">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseByBranch as $item)
                                        <tr>
                                            <td>{{ $item->branch ? $item->branch->name : 'Unknown' }}</td>
                                            <td class="text-right">{{ number_format($item->total, 2) }}</td>
                                            <td class="text-right">{{ $item->count }}</td>
                                            <td class="text-right">{{ $totalExpense > 0 ? number_format(($item->total / $totalExpense) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">No expense data by branch.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Trend Chart
    var trendCtx = document.getElementById('trendChart').getContext('2d');
    var trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Income',
                    data: @json($chartIncome),
                    borderColor: '#00a65a',
                    backgroundColor: 'rgba(0, 166, 90, 0.1)',
                    fill: true,
                    tension: 0.2
                },
                {
                    label: 'Expenses',
                    data: @json($chartExpense),
                    borderColor: '#dd4b39',
                    backgroundColor: 'rgba(221, 75, 57, 0.1)',
                    fill: true,
                    tension: 0.2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Daily Income vs Expenses Trend' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Amount' } },
                x: { title: { display: true, text: 'Date' } }
            }
        }
    });

    // Income Category Chart
    var incomeLabels = @json($incomeByCategory->pluck('income_category')->toArray());
    var incomeData = @json($incomeByCategory->pluck('total')->toArray());
    var incomeCtx = document.getElementById('incomeCategoryChart').getContext('2d');
    var incomeChart = new Chart(incomeCtx, {
        type: 'doughnut',
        data: {
            labels: incomeLabels,
            datasets: [{
                data: incomeData,
                backgroundColor: [
                    '#00a65a', '#00c0ef', '#3c8dbc', '#f39c12', '#f56954',
                    '#605ca8', '#0073b7', '#39CCCC', '#D81B60', '#001F3F'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Income by Category' }
            }
        }
    });

    // Expense Category Chart
    var expenseLabels = @json($expenseByCategory->pluck('expense_category')->toArray());
    var expenseData = @json($expenseByCategory->pluck('total')->toArray());
    var expenseCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    var expenseChart = new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: expenseLabels,
            datasets: [{
                data: expenseData,
                backgroundColor: [
                    '#dd4b39', '#f39c12', '#f56954', '#3c8dbc', '#00c0ef',
                    '#605ca8', '#00a65a', '#39CCCC', '#D81B60', '#001F3F'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Expenses by Category' }
            }
        }
    });
});
</script>
@endsection
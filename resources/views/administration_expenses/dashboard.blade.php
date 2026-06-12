@extends('layouts.master')
@section('title')
    Administration Expenses Dashboard
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Administration Expenses Dashboard</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('administration_expenses.index') }}" class="btn btn-info btn-sm">Back to List</a>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box" style="background: #f8f9fa;">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Deposits</span>
                        <span class="info-box-number">K{{ number_format($totalDeposits, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box" style="background: #f8f9fa;">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expenses</span>
                        <span class="info-box-number">K{{ number_format($totalExpenses, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box" style="background: #e8f5e9;">
                    <div class="info-box-content">
                        <span class="info-box-text">Available Balance</span>
                        <span class="info-box-number">K{{ number_format($availableBalance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <h4>Expenses by Category</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expensesByCategory as $item)
                    <tr>
                        <td>{{ $item->category ? $item->category->name : 'Unknown' }}</td>
                        <td style="text-align: right;">K{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h4>Monthly Expense Trends</h4>
        <canvas id="monthlyChart" height="200"></canvas>
    </div>
</div>
@endsection

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('monthlyChart').getContext('2d');
    var labels = @json($monthlyExpenses->pluck('month')->map(function($m) { return date('M Y', mktime(0, 0, 0, $m, 1)); }));
    var data = @json($monthlyExpenses->pluck('total'));

    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Expenses',
                data: data,
                borderColor: '#00a65a',
                backgroundColor: 'rgba(0, 166, 90, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
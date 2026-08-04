@extends('layouts.master')

@php
use Illuminate\Support\Str;
@endphp

@section('title')
    Branch Deposit Transactions
@endsection

@section('content')
@include('components.kilo-alert')
<style>
    .page-chrome { background: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #667eea; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
    .page-chrome h1 { margin: 0; font-size: 20px; font-weight: 700; }
    .page-chrome p { margin: 4px 0 0; font-size: 13px; color: #666; }
    .filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 10px 16px; margin-bottom: 18px; background: #f4f6fb; border-radius: 7px; border: 1px solid #dde3ef; }
    .filter-bar label { font-size: 13px; font-weight: 600; color: #555; white-space: nowrap; }
    .filter-bar select { font-size: 13px; padding: 5px 8px; border: 1px solid #c7cfdf; border-radius: 4px; background: #fff; color: #444; outline: none; }
    .filter-bar select:focus { border-color: #667eea; }
    #depositsTable thead th { background: #667eea; color: #fff; }
    #depositsTable tbody td { vertical-align: middle; }
    .table-responsive { margin-top: 10px; }
</style>

<div class="content-wrapper" style="margin: 20px;">
    <div class="page-chrome">
        <h1><i class="fa fa-money"></i> Branch Deposit Transactions</h1>
        <p>View all branch deposit transactions with filtering options.</p>
    </div>

    <div class="filter-bar">
        <label><i class="fa fa-calendar"></i> Month</label>
        <input type="month" id="monthFilter" value="{{ $selectedMonth }}" style="font-size:13px;padding:5px 8px;border:1px solid #c7cfdf;border-radius:4px;">

        <label><i class="fa fa-tag"></i> Deposit Type</label>
        <select id="depositTypeFilter">
            <option value="">All Deposit Types</option>
            @foreach($depositTypes as $type)
                <option value="{{ $type->id }}" {{ $depositTypeId == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
            <option value="setup_debt" {{ $depositTypeId == 'setup_debt' ? 'selected' : '' }}>Setup Debt</option>
        </select>

        <button type="button" id="applyFilters" class="btn btn-primary btn-sm" style="border-radius:4px;">
            <i class="fa fa-filter"></i> Apply Filters
        </button>
        <button type="button" id="resetFilters" class="btn btn-secondary btn-sm" style="border-radius:4px;">
            <i class="fa fa-refresh"></i> Reset
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="depositsTable">
            <thead>
                <tr>
                    <th>Created Date</th>
                    <th>Office</th>
                    <th>Amount</th>
                    <th>Deposit Type</th>
                    <th>Reference</th>
                    <th>Method</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @dd($allTransactions)
                @forelse($allTransactions as $deposit)
                    <tr>
                        <td>{{ $deposit->bankDepositLog->created_date ? date('Y-m-d', strtotime($deposit->bankDepositLog->created_date)) : 'N/A' }}</td>
                        <td>{{ $deposit->office?->name ?? 'wait...' }}</td>
                        <td>{{ number_format($deposit->amount, 2) }}</td>
                        <td>{{ $deposit->depositTypeInfo->name ?? 'N/A' }}</td>
                        <td>{{ $deposit->bankDepositLog->reference_number ?? 'N/A' }}</td>
                        <td>{{ $deposit->bankDepositLog->deposit_method ?? 'N/A' }}</td>
                        <td>
                            @if($deposit->bankDepositLog && $deposit->bankDepositLog->user)
                                {{ $deposit->bankDepositLog->user->first_name ?? '' }} {{ $deposit->bankDepositLog->user->last_name ?? '' }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 20px; color: #888;">No deposit transactions found for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    var monthFilter = document.getElementById('monthFilter');
    var depositTypeFilter = document.getElementById('depositTypeFilter');
    var applyFiltersBtn = document.getElementById('applyFilters');
    var resetFiltersBtn = document.getElementById('resetFilters');

    applyFiltersBtn.addEventListener('click', function() {
        var params = new URLSearchParams();
        if (monthFilter.value) {
            params.set('month', monthFilter.value);
        }
        if (depositTypeFilter.value) {
            params.set('deposit_type_id', depositTypeFilter.value);
        }
        window.location.href = '{{ route("branch-deposit-transactions") }}?' + params.toString();
    });

    resetFiltersBtn.addEventListener('click', function() {
        window.location.href = '{{ route("branch-deposit-transactions") }}';
    });

    monthFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFiltersBtn.click();
        }
    });

    depositTypeFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFiltersBtn.click();
        }
    });
})();
</script>
@endsection
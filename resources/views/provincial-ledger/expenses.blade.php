@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="margin:0;">Provincial Expenses</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            @if(!empty($isAdmin) && !in_array(Sintinel::get()->user_id, config('admin.provincial_ledger_enabled', []))
                <form method="GET" action="{{ route('provincial-ledger.expenses') }}" style="display:flex; gap:8px; align-items:center; margin:0;">
                    <label for="province_id" style="margin:0; font-weight:600;">Province</label>
                    <select id="province_id" name="province_id" class="form-control" onchange="this.form.submit()" style="min-width:220px;">
                        <option value="">All provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ (string)($selectedProvinceId ?? request('province_id')) === (string)$province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif

            @if(Sentinel::getUser()->role->role_id == 6)
             <button type="button" class="btn btn-danger" onclick="openTransactionModal('expense')"><i class="fa fa-plus"></i> Record Expense</button>
            @endif
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
                <th>Debited From</th>
                <th>Amount</th>
                <th>Reference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $tx)
            <tr>
                <td>{{ $tx->transaction_date }}</td>
                <td>{{ $tx->title }}</td>
                <td>{{ $tx->province->name ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $tx->contribution ?? '-')) }}</td>
                <td class="text-right">K{{ number_format($tx->amount, 2) }}</td>
                <td>{{ $tx->reference_number ?? '-' }}</td>
                
                <td>
                    @if($tx->status == 'approved')
                        <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                    @elseif($tx->status == 'pending')
                        <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                    @else
                        <span class="label label-default">{{ $tx->status ?? '-' }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
</table>
</div>
    @include('provincial-ledger._partials.transaction-modal')
</div>
@endsection
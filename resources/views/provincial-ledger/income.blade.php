@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h3 style="margin:0;">Provincial Income</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            @if(!empty($isAdmin))
                <form method="GET" action="{{ route('provincial-ledger.income') }}" style="display:flex; gap:8px; align-items:center; margin:0;">
                    <label for="province_id" style="margin:0; font-weight:600;">Province</label>
                    <select id="province_id" name="province_id" class="form-control" onchange="this.form.submit()" style="min-width:220px;">
                        <option value="">All provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ (string)($selectedProvinceId ?? request('province_id')) === (string)$province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
            <button type="button" class="btn btn-primary" onclick="openTransactionModal('income')"><i class="fa fa-plus"></i> Record Income</button>
        </div>
    </div>
    
    <div class="well">
        <strong>Total Income:</strong> K{{ number_format($total, 2) }}
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Province</th>
                <th>Contribution</th>
                <th>Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($income as $tx)
            <tr>
                <td>{{ $tx->transaction_date }}</td>
                <td>{{ $tx->title }}</td>
                <td>{{ $tx->province->name ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $tx->contribution ?? '-')) }}</td>
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
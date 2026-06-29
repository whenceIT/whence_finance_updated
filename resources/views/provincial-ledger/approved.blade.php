@extends('layouts.master')

@section('title')
    Approved Transactions - Provincial Ledger
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-check-circle"></i> Approved Transactions</h3>
        <div class="box-tools pull-right">
            <a href="{{ url('provincial-transactions/pending') }}" class="btn btn-default btn-sm">View Pending</a>
        </div>
    </div>
    <div class="box-body no-padding">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Province</th>
                    <th>Approved By</th>
                    <th>Approved At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->transaction_date ?? $tx->created_at->format('d M Y') }}</td>
                    <td><strong>{{ $tx->title }}</strong></td>
                    <td>
                        <span class="label {{ $tx->type == 'income' ? 'label-success' : 'label-danger' }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td>K{{ number_format($tx->amount, 2) }}</td>
                    <td>{{ $tx->province->name ?? 'N/A' }}</td>
                    <td>{{ $tx->approver->first_name ?? 'N/A' }} {{ $tx->approver->last_name ?? '' }}</td>
                    <td>{{ $tx->approved_at ? $tx->approved_at->format('d M Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                        <i class="fa fa-file" style="font-size: 48px; color: #999; margin-bottom: 10px;"></i>
                        <h4>No Approved Transactions</h4>
                        <p>Approve transactions from the pending list.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
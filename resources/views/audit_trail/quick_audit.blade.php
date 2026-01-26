@extends('layouts.master')
@section('title')
    Quick Audit
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Quick Audit Results for {{ $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User' }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('audit_trail/data') }}" class="btn btn-default btn-sm">Back to Audit Trail</a>
            </div>
        </div>

        <div class="box-body">
            <ul class="timeline">
                @foreach($loans as $loan)
                <li class="time-label">
                    <span class="bg-blue">Loan #{{ $loan->id }}</span>
                </li>
                <li>
                    <i class="fa fa-money bg-green"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{ $loan->created_at->format('M d, Y') }}</span>
                        <h3 class="timeline-header"><a href="#">{{ $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'Unknown Client' }}</a> - Loan Amount: {{ number_format($loan->principal, 2) }}</h3>
                        <div class="timeline-body">
                            <strong>Status:</strong> <span class="label label-{{ $loan->status == 'disbursed' ? 'success' : ($loan->status == 'pending' ? 'warning' : 'default') }}">{{ ucfirst($loan->status) }}</span><br>
                            <strong>Client ID:</strong> {{ $loan->client_id }}<br>
                            <strong>Loan Product:</strong> {{ $loan->loan_product_id }}<br>
                            @if($loan->principal >= 4000)
                                <div style="color: red; font-weight: bold;">🚩 FLAG: Loans more than K4,000 principal require collateral.</div>
                            @endif
                            @php
                                $hasDisbursement = $loan->transactions->contains('transaction_type', 'disbursement');
                            @endphp
                            @if($loan->status == 'disbursed' && !$hasDisbursement)
                                <div style="color: red; font-weight: bold;">🚩 FLAG: Loan has been disbursed without disbursement transaction found.</div>
                            @endif
                            @if($loan->transactions && $loan->transactions->count() > 0)
                                <strong>Transactions ({{ $loan->transactions->count() }}):</strong>
                                <ul>
                                    @foreach($loan->transactions as $transaction)
                                    <li>{{ $transaction->transaction_type }} - Amount: {{ number_format($transaction->debit - $transaction->credit, 2) }} on {{ $transaction->date }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <strong>No transactions found.</strong>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
                <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                </li>
            </ul>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
@endsection
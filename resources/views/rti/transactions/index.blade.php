@extends('layouts.master')

@section('title')
    RTI Transaction History
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-history"></i> RTI Transaction History
                    </h4>
                    <div class="heading-elements">
                        <a href="{{ route('rti.loans.dashboard') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                        <a href="{{ route('rti.loans.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-list"></i> All Loans
                        </a>
                    </div>
                </div>

                <div class="panel-body">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('rti.transactions.index') }}" class="form-inline" style="margin-bottom:15px;">
                        <div class="form-group" style="margin-right:8px;">
                            <select name="office_id" class="form-control input-sm">
                                <option value="">All Branches</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <select name="status" class="form-control input-sm">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="number" name="loan_id" class="form-control input-sm"
                                   placeholder="Loan #" value="{{ request('loan_id') }}" style="width:90px;">
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="date" name="date_from" class="form-control input-sm"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="date" name="date_to" class="form-control input-sm"
                                   value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('rti.transactions.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Loan #</th>
                                    <th>Branch</th>
                                    <th>Type</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    <th>Approved At</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ $tx->id }}</td>
                                        <td>{{ $tx->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('rti.loans.show', $tx->loan_id) }}">
                                                #{{ $tx->loan_id }}
                                            </a>
                                        </td>
                                        <td>{{ optional($tx->office)->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($tx->debit > 0)
                                                <span class="label label-primary">Disbursement</span>
                                            @else
                                                <span class="label label-info">Repayment</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tx->debit > 0)
                                                <span class="text-danger">K{{ number_format($tx->debit, 2) }}</span>
                                            @else
                                                &mdash;
                                            @endif
                                        </td>
                                        <td>
                                            @if($tx->credit > 0)
                                                <span class="text-success">K{{ number_format($tx->credit, 2) }}</span>
                                            @else
                                                &mdash;
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label {{ $tx->status_badge_class }}">
                                                {{ $tx->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($tx->approver)
                                                {{ $tx->approver->first_name }} {{ $tx->approver->last_name }}
                                            @else
                                                &mdash;
                                            @endif
                                        </td>
                                        <td>
                                            {{ $tx->approved_at ? $tx->approved_at->format('d M Y H:i') : '&mdash;' }}
                                        </td>
                                        <td>{{ $tx->notes ?? '&mdash;' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted" style="padding:30px;">
                                            <i class="fa fa-inbox fa-2x"></i><br>No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $transactions->links() }}

                </div>
            </div>
        </div>
    </div>
@endsection

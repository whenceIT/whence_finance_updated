@extends('layouts.master')

@section('title')
    RTI Repayments &mdash; Pending Approvals
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-clock-o"></i>
                        RTI Repayments &mdash; Pending Approvals
                    </h4>
                    <div class="heading-elements">
                        <a href="{{ route('rti.loans.dashboard') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                        <a href="{{ route('rti.transactions.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-history"></i> All Transactions
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Loan #</th>
                                    <th>Branch</th>
                                    <th>Staff</th>
                                    <th>Repayment Amount</th>
                                    <th>Outstanding at Submission</th>
                                    <th>Notes</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ $tx->id }}</td>
                                        <td>
                                            <a href="{{ route('rti.loans.show', $tx->loan_id) }}">#{{ $tx->loan_id }}</a>
                                        </td>
                                        <td>{{ optional($tx->office)->name ?? optional(optional($tx->loan)->office)->name ?? 'N/A' }}</td>
                                        <td>
                                            @php $staff = optional($tx->loan)->staff; @endphp
                                            {{ $staff ? $staff->first_name . ' ' . $staff->last_name : 'N/A' }}
                                        </td>
                                        <td>
                                            <strong class="text-success">K{{ number_format($tx->credit, 2) }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $loan = $tx->loan;
                                                $outstanding = $loan ? $loan->outstanding_balance : 0;
                                            @endphp
                                            <span class="{{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                                K{{ number_format($outstanding, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ $tx->notes ?? '&mdash;' }}</td>
                                        <td>{{ $tx->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('rti.repayment.approve', $tx->id) }}"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Approve K{{ number_format($tx->credit, 2) }} repayment for loan #{{ $tx->loan_id }}?')">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-xs btn-success">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST"
                                                  action="{{ route('rti.repayment.decline', $tx->id) }}"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Decline this repayment?')">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-xs btn-danger">
                                                    <i class="fa fa-times"></i> Decline
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted" style="padding:30px;">
                                            <i class="fa fa-check-circle fa-2x text-success"></i><br>
                                            No pending repayments awaiting approval.
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

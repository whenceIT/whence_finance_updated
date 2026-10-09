@php

    use Illuminate\Support\Facades\Redirect;
    use App\Models\AppraisalForm;
    use App\Models\Ticket;

    if (!Sentinel::check()) {
        redirect()->route('login')->send();
        exit;
    }

    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
@endphp

@extends('layouts.master')

@section('title')
    RTI Loan #{{ $loan->id }}
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-12">

            {{-- ============================================================
                 LOAN DETAILS PANEL
            ============================================================ --}}
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-university"></i>
                        RTI Loan #{{ $loan->id }}
                        &mdash;
                        <span class="label {{ $loan->status_badge_class }}">
                            {{ $loan->status_label }}
                        </span>
                    </h4>
                    <div class="heading-elements">
                        @if($role == 10)
                            <a href="{{ route('rti.loans.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>

                            <div class="pull-right">
                                {{-- Approve --}}
                                @if($loan->status === 'pending')
                                    <form method="POST" action="{{ route('rti.loans.approve', $loan->id) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Approve this RTI loan?')">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endif

                                {{-- Disburse --}}
                                @if($loan->status === 'approved')
                                    <form method="POST" action="{{ route('rti.loans.disburse', $loan->id) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Disburse K{{ number_format($loan->total_payable, 2) }} to {{ optional($loan->office)->name }}?')">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-paper-plane"></i> Disburse
                                        </button>
                                    </form>
                                @endif

                                {{-- Decline --}}
                                @if(in_array($loan->status, ['pending','approved']))
                                    <form method="POST" action="{{ route('rti.loans.decline', $loan->id) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Decline this RTI loan?')">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-times"></i> Decline
                                        </button>
                                    </form>
                                @endif

                                {{-- Record Repayment --}}
                                @if(in_array($loan->status, ['disbursed','partially_paid']))
                                    <a href="{{ route('rti.repayment.create', $loan->id) }}"
                                    class="btn btn-warning btn-sm">
                                        <i class="fa fa-credit-card"></i> Record Repayment
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="panel-body">
                    <div class="row">

                        {{-- Left: loan info --}}
                        <div class="col-md-6">
                            <table class="table table-condensed">
                                <tr>
                                    <th style="width:40%;">Loan ID</th>
                                    <td>#{{ $loan->id }}</td>
                                </tr>
                                <tr>
                                    <th>Branch / Office</th>
                                    <td>{{ optional($loan->office)->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Responsible Staff</th>
                                    <td>{{ optional($loan->staff)->first_name }} {{ optional($loan->staff)->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="label {{ $loan->status_badge_class }}">
                                            {{ $loan->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $loan->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($loan->approved_at)
                                <tr>
                                    <th>Approved At</th>
                                    <td>{{ $loan->approved_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($loan->disbursed_at)
                                <tr>
                                    <th>Disbursed At</th>
                                    <td>{{ $loan->disbursed_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        {{-- Right: financial summary --}}
                        <div class="col-md-6">
                            <div class="well" style="background:#f9f9f9;">
                                <h5 style="margin-top:0;font-weight:700;">
                                    <i class="fa fa-calculator"></i> Financial Summary
                                </h5>
                                <table class="table table-condensed" style="margin-bottom:0;">
                                    <tr>
                                        <td>Principal</td>
                                        <td class="text-right">
                                            <strong>K{{ number_format($loan->principal, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>RTI Interest (20%)</td>
                                        <td class="text-right">
                                            <strong>K{{ number_format($loan->interest, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr style="border-top:1px solid #ddd;">
                                        <td><strong>Total Payable</strong></td>
                                        <td class="text-right">
                                            <strong class="text-primary" style="font-size:15px;">
                                                K{{ number_format($loan->total_payable, 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Paid</td>
                                        <td class="text-right text-success">
                                            <strong>K{{ number_format($loan->total_paid, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr style="border-top:2px solid #ddd;">
                                        <td><strong>Outstanding Balance</strong></td>
                                        <td class="text-right">
                                            @if($loan->outstanding_balance > 0)
                                                <strong class="text-danger" style="font-size:16px;">
                                                    K{{ number_format($loan->outstanding_balance, 2) }}
                                                </strong>
                                            @else
                                                <strong class="text-success" style="font-size:16px;">
                                                    K0.00 — Fully Paid
                                                </strong>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>{{-- /row --}}
                </div>{{-- /panel-body --}}
            </div>{{-- /panel --}}

            {{-- ============================================================
                 TRANSACTION HISTORY
            ============================================================ --}}
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-history"></i> Transaction / Payment History
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    <th>Running Balance</th>
                                    <th>Notes</th>
                                    @if(Sentinel::hasAccess('rti.approve_repayment'))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $tx)
                                    <tr>
                                        <td>{{ $tx->id }}</td>
                                        <td>{{ $tx->created_at->format('d M Y H:i') }}</td>
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
                                            @if($tx->status === 'approved')
                                                <strong>K{{ number_format($tx->running_balance, 2) }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $tx->notes ?? '&mdash;' }}</td>
                                        @if(Sentinel::hasAccess('rti.approve_repayment'))
                                            <td>
                                                @if($tx->status === 'pending' && $tx->credit > 0)
                                                    <form method="POST"
                                                          action="{{ route('rti.repayment.approve', $tx->id) }}"
                                                          style="display:inline;"
                                                          onsubmit="return confirm('Approve repayment of K{{ number_format($tx->credit, 2) }}?')">
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
                                                @else
                                                    &mdash;
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Sentinel::hasAccess('rti.approve_repayment') ? 10 : 9 }}"
                                            class="text-center text-muted" style="padding:30px;">
                                            <i class="fa fa-inbox fa-2x"></i><br>
                                            No transactions recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /col --}}
    </div>{{-- /row --}}
@endsection

@extends('layouts.master')

@section('title')
    RTI Branch Loans &mdash; Dashboard
@endsection

@section('content')
    @include('rti._partials.flash')

    {{-- ================================================================
         PAGE HEADER
    ================================================================ --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-university"></i> RTI Branch Loans &mdash; Dashboard
                    </h4>
                    <div class="heading-elements">
                        @if(Sentinel::hasAccess('rti.create'))
                            <a href="{{ route('rti.loans.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New RTI Loan
                            </a>
                        @endif
                        <a href="{{ route('rti.loans.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-list"></i> All Loans
                        </a>
                        @if(Sentinel::hasAccess('rti.approve_repayment'))
                            <a href="{{ route('rti.repayment.pending') }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-clock-o"></i> Pending Approvals
                                @if($pendingRepayments > 0)
                                    <span class="badge">{{ $pendingRepayments }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                <div class="panel-body">

                    {{-- ------------------------------------------------
                         FILTERS
                    ------------------------------------------------ --}}
                    <form method="GET" action="{{ route('rti.loans.dashboard') }}" class="form-inline" style="margin-bottom:20px;">
                        <div class="form-group" style="margin-right:8px;">
                            <label class="sr-only">Branch</label>
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
                            <label class="sr-only">Status</label>
                            <select name="status" class="form-control input-sm">
                                <option value="">All Statuses</option>
                                @foreach(\App\Models\OfficeLoan::statuses() as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="date" name="date_from" class="form-control input-sm"
                                   placeholder="From" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="date" name="date_to" class="form-control input-sm"
                                   placeholder="To" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('rti.loans.dashboard') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </form>

                    {{-- ------------------------------------------------
                         KPI STAT BOXES
                    ------------------------------------------------ --}}
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua"><i class="fa fa-list-ol"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total RTI Loans</span>
                                    <span class="info-box-number">{{ $totalLoans }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-blue"><i class="fa fa-money"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Disbursed</span>
                                    <span class="info-box-number">K{{ number_format($totalDisbursed, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-orange"><i class="fa fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Payable</span>
                                    <span class="info-box-number">K{{ number_format($totalPayable, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Repaid</span>
                                    <span class="info-box-number">K{{ number_format($totalRepaid, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-exclamation-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Outstanding Balance</span>
                                    <span class="info-box-number">K{{ number_format($totalOutstanding, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fully Paid</span>
                                    <span class="info-box-number">{{ $fullyPaid }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Partially Paid</span>
                                    <span class="info-box-number">{{ $partiallyPaid }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-gray"><i class="fa fa-clock-o"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Loans</span>
                                    <span class="info-box-number">{{ $pendingLoans }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ------------------------------------------------
                         RECENT LOANS TABLE
                    ------------------------------------------------ --}}
                    <h5 style="margin-top:10px;margin-bottom:10px;font-weight:600;">
                        <i class="fa fa-table"></i> Recent Loans
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Staff</th>
                                    <th>Principal</th>
                                    <th>Interest (20%)</th>
                                    <th>Total Payable</th>
                                    <th>Total Paid</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    <tr>
                                        <td>{{ $loan->id }}</td>
                                        <td>{{ optional($loan->office)->name ?? 'N/A' }}</td>
                                        <td>{{ optional($loan->staff)->first_name }} {{ optional($loan->staff)->last_name }}</td>
                                        <td>K{{ number_format($loan->principal, 2) }}</td>
                                        <td>K{{ number_format($loan->interest, 2) }}</td>
                                        <td><strong>K{{ number_format($loan->total_payable, 2) }}</strong></td>
                                        <td>K{{ number_format($loan->total_paid, 2) }}</td>
                                        <td>
                                            @if($loan->outstanding_balance > 0)
                                                <span class="text-danger"><strong>K{{ number_format($loan->outstanding_balance, 2) }}</strong></span>
                                            @else
                                                <span class="text-success"><strong>K0.00</strong></span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label {{ $loan->status_badge_class }}">
                                                {{ $loan->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $loan->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('rti.loans.show', $loan->id) }}"
                                               class="btn btn-xs btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted" style="padding:30px;">
                                            <i class="fa fa-inbox fa-2x"></i><br>No RTI loans found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>{{-- /panel-body --}}
            </div>{{-- /panel --}}
        </div>
    </div>
@endsection

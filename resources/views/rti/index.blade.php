@extends('layouts.master')

@section('title')
    RTI Branch Loans
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-university"></i> RTI Branch Loans
                    </h4>
                    <div class="heading-elements">
                        @if(Sentinel::hasAccess('rti.create'))
                            <a href="{{ route('rti.loans.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New RTI Loan
                            </a>
                        @endif
                        <a href="{{ route('rti.loans.dashboard') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </div>
                </div>

                <div class="panel-body">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('rti.loans.index') }}" class="form-inline" style="margin-bottom:15px;">
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
                            <input type="date" name="date_from" class="form-control input-sm"
                                   value="{{ request('date_from') }}" placeholder="From">
                        </div>
                        <div class="form-group" style="margin-right:8px;">
                            <input type="date" name="date_to" class="form-control input-sm"
                                   value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('rti.loans.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                            <thead>
                                <tr style="background:#f4f4f4;">
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Staff</th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>Total Payable</th>
                                    <th>Paid</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Disbursed At</th>
                                    <th>Actions</th>
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
                                                <span class="text-danger">K{{ number_format($loan->outstanding_balance, 2) }}</span>
                                            @else
                                                <span class="text-success">K0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label {{ $loan->status_badge_class }}">
                                                {{ $loan->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : '&mdash;' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('rti.loans.show', $loan->id) }}"
                                               class="btn btn-xs btn-primary"
                                               title="View">
                                                <i class="fa fa-eye"></i>
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

                    {{ $loans->links() }}

                </div>
            </div>
        </div>
    </div>
@endsection

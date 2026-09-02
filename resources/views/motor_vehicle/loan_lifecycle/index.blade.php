@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Motor Vehicle Loan Lifecycle</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Loan Portfolio</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('motor-vehicle.product-configurations') }}" class="btn btn-success btn-xs">Product Configurations</a>
            <a href="{{ route('motor-vehicle.approval-matrices') }}" class="btn btn-info btn-xs">Approval Matrices</a>
        </div>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ route('motor-vehicle-loans.index') }}" class="form-inline">
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search client or registration..." value="{{ request('search') }}">
            </div>
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select name="branch_id" class="form-control">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <hr>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Client</th>
                    <th>Vehicle</th>
                    <th>Branch</th>
                    <th>Consultant</th>
                    <th>Approved Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>#{{ $loan->id }}</td>
                    <td>{{ $loan->client->display_name ?? 'N/A' }}</td>
                    <td>{{ $loan->vehicle->make ?? '' }} {{ $loan->vehicle->model ?? '' }} ({{ $loan->vehicle->registration_number ?? '' }})</td>
                    <td>{{ $loan->originatingBranch->name ?? 'N/A' }}</td>
                    <td>{{ $loan->loanConsultant->first_name ?? '' }} {{ $loan->loanConsultant->last_name ?? '' }}</td>
                    <td>K{{ number_format($loan->approved_amount, 2) }}</td>
                    <td>
                        <span class="label label-{{ $loan->status == 'active_loan' ? 'success' : ($loan->status == 'default' ? 'danger' : 'warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('motor-vehicle-loans.show', $loan->id) }}" class="btn btn-primary btn-xs">View Lifecycle</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">No loans found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $loans->appends(request()->query())->links() }}
    </div>
</div>

</section>

@endsection

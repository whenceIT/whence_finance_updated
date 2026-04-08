@extends('layouts.master')
@section('title', 'Collateral Report')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral Report</h3>
        </div>
        <div class="box-body">
            <form method="get" action="{{ route('collateral.report') }}" class="form-inline" style="margin-bottom: 15px;">
                <div class="form-group" style="margin-right: 10px;">
                    <select name="status" class="form-control input-sm">
                        <option value="">All Statuses</option>
                        <option value="active"{{ request('status') == 'active' ? ' selected' : '' }}>Active</option>
                        <option value="sold"{{ request('status') == 'sold' ? ' selected' : '' }}>Sold</option>
                        <option value="defaulted"{{ request('status') == 'defaulted' ? ' selected' : '' }}>Defaulted</option>
                        <option value="repossessed"{{ request('status') == 'repossessed' ? ' selected' : '' }}>Repossessed</option>
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <select name="office_id" class="form-control input-sm">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}"{{ request('office_id') == $office->id ? ' selected' : '' }}>{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <select name="province_id" class="form-control input-sm">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}"{{ request('province_id') == $province->id ? ' selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <select name="collateral_type_id" class="form-control input-sm">
                        <option value="">All Types</option>
                        @foreach($collateralTypes as $type)
                            <option value="{{ $type->id }}"{{ request('collateral_type_id') == $type->id ? ' selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <select name="loan_status" class="form-control input-sm">
                        <option value="">All Loan Statuses</option>
                        @foreach($loanStatuses as $loanStatus)
                            <option value="{{ $loanStatus }}"{{ request('loan_status') == $loanStatus ? ' selected' : '' }}>{{ ucfirst($loanStatus) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('collateral.report') }}" class="btn btn-default btn-sm">Reset</a>
            </form>

            <form method="post" action="{{ route('collateral.export') }}" style="margin-bottom: 15px;">
                {{ csrf_field() }}
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="office_id" value="{{ request('office_id') }}">
                <input type="hidden" name="province_id" value="{{ request('province_id') }}">
                <input type="hidden" name="collateral_type_id" value="{{ request('collateral_type_id') }}">
                <input type="hidden" name="loan_status" value="{{ request('loan_status') }}">
                <button type="submit" class="btn btn-success btn-sm">Export CSV</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Loan</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Condition</th>
                        <th>Current Worth</th>
                        <th>Date Purchased</th>
                        <th>Office</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($collaterals as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ optional($item->loan)->id }}</td>
                            <td>{{ optional($item->type)->name }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ ucfirst($item->condition) }}</td>
                            <td>{{ number_format($item->current_worth, 2) }}</td>
                            <td>{{ optional($item->date_purchased)->format('Y-m-d') }}</td>
                            <td>{{ optional($item->loan->office)->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No collateral preview available.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                {{ $collaterals->links() }}
            </div>
        </div>
    </div>
@endsection

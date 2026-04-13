@extends('layouts.master')
@section('title', 'Collateral')
@section('content')
<?php
$role = Sentinel::getUser()->roles()->first()->id;
?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('collateral.create'))
                    <a href="{{ route('collateral.create') }}" class="btn btn-success btn-sm">Add Collateral</a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <form method="get" action="{{ route('collateral.index') }}" class="form-inline" style="margin-bottom: 15px;">
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
                    <select name="condition" class="form-control input-sm">
                        <option value="">All Conditions</option>
                        <option value="new"{{ request('condition') == 'new' ? ' selected' : '' }}>New</option>
                        <option value="good"{{ request('condition') == 'good' ? ' selected' : '' }}>Good</option>
                        <option value="fair"{{ request('condition') == 'fair' ? ' selected' : '' }}>Fair</option>
                        <option value="poor"{{ request('condition') == 'poor' ? ' selected' : '' }}>Poor</option>
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
                <div class="form-group" style="margin-right: 10px; min-width: 180px;">
                    <select name="loan_id" class="form-control input-sm">
                        <option value="">All Loans</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->id }}"{{ request('loan_id') == $loan->id ? ' selected' : '' }}>{{ $loan->id }} - {{ $loan->status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <input type="text" name="search" class="form-control input-sm" placeholder="Search name or description" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('collateral.index') }}" class="btn btn-default btn-sm">Reset</a>
            </form>

            <form method="post" action="{{ route('collateral.export') }}" style="margin-bottom: 15px;">
                {{ csrf_field() }}
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="office_id" value="{{ request('office_id') }}">
                <input type="hidden" name="province_id" value="{{ request('province_id') }}">
                <input type="hidden" name="collateral_type_id" value="{{ request('collateral_type_id') }}">
                <input type="hidden" name="loan_id" value="{{ request('loan_id') }}">
                <input type="hidden" name="condition" value="{{ request('condition') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
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
                                 <th>Purchased</th>
                                 <th>Current Worth</th>
                                 <th>Sold Price</th>
                                 <th>Penalty</th>
                                 <th>Office</th>
                                 <th>Actions</th>
                             </tr>
                    </thead>
                    <tbody>
                        @forelse($collateral as $item)
                             <tr>
                                 <td>{{ $item->name }}</td>
                                 <td>{{ optional($item->loan)->id }}</td>
                                 <td>{{ optional($item->type)->name }}</td>
                                 <td>{{ ucfirst($item->status) }}</td>
                                 <td>{{ ucfirst($item->condition) }}</td>
                                 <td>{{ optional($item->date_purchased)->format('Y-m-d') }}</td>
                                 <td>{{ number_format($item->current_worth, 2) }}</td>
                                 <td>{{ number_format($item->sold_price ?? 0, 2) }}</td>
                                 <td>{{ number_format($item->penalty ?? 0, 2) }}</td>
                                 <td>{{ $item->loan?->office?->name }}</td>
                                 <td>
                                  
                                     <a href="{{ route('collateral.show', $item) }}" class="btn btn-xs btn-primary">View</a>
                                     @if((Sentinel::getUser()->id == $item->created_by_id) || $role == 1)
                                         <a href="{{ route('collateral.edit', $item) }}" class="btn btn-xs btn-warning">Edit</a>

                                         <form action="{{ route('collateral.destroy', $item) }}" method="POST" style="display:inline;">
                                             @csrf
                                             @method('DELETE')
                                             <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this collateral?')">Delete</button>
                                         </form>
                                     @endif
                                 </td>
                            </tr>
                         @empty
                             <tr>
                                 <td colspan="11" class="text-center">No collateral found at this
                                 @if($role == 3)
                                     Loan Consultant
                                 @elseif($role == 4)
                                     Branch
                                 @elseif($role == 6)
                                     Province
                                 @elseif($role == 12)
                                     District
                                 @else
                                     Moment of Time
                                 @endif
                                 .</td>
                             </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                {{ $collateral->links() }}
            </div>
        </div>
    </div>
@endsection

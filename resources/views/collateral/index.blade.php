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
            <form method="get" action="{{ route('collateral.index') }}" class="form-inline" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                <select name="status" class="form-control input-sm" style="width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="pledged"{{ request('status') == 'pledged' ? ' selected' : '' }}>Pledged</option>
                    <option value="seizure_pending"{{ request('status') == 'seizure_pending' ? ' selected' : '' }}>Seizure Pending</option>
                    <option value="seized_inventory"{{ request('status') == 'seized_inventory' ? ' selected' : '' }}>Seized/Inventory</option>
                    <option value="valuation_completed"{{ request('status') == 'valuation_completed' ? ' selected' : '' }}>Valuation Completed</option>
                    <option value="listed_for_sale"{{ request('status') == 'listed_for_sale' ? ' selected' : '' }}>Listed for Sale</option>
                    <option value="sold"{{ request('status') == 'sold' ? ' selected' : '' }}>Sold</option>
                    <option value="written_off"{{ request('status') == 'written_off' ? ' selected' : '' }}>Written Off</option>
                    <option value="released"{{ request('status') == 'released' ? ' selected' : '' }}>Released</option>
                </select>
                <select name="condition" class="form-control input-sm" style="width: 120px;">
                    <option value="">All Conditions</option>
                    <option value="new"{{ request('condition') == 'new' ? ' selected' : '' }}>New</option>
                    <option value="good"{{ request('condition') == 'good' ? ' selected' : '' }}>Good</option>
                    <option value="fair"{{ request('condition') == 'fair' ? ' selected' : '' }}>Fair</option>
                    <option value="poor"{{ request('condition') == 'poor' ? ' selected' : '' }}>Poor</option>
                </select>
                <select name="collateral_type_id" class="form-control input-sm" style="width: 130px;">
                    <option value="">All Types</option>
                    @foreach($collateralTypes as $type)
                        <option value="{{ $type->id }}"{{ request('collateral_type_id') == $type->id ? ' selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @if($role == 1 || $role == 6 || $role == 12)
                <select name="province_id" class="form-control input-sm" style="width: 140px;">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}"{{ request('province_id') == $province->id ? ' selected' : '' }}>{{ $province->name }}</option>
                    @endforeach
                </select>
                @endif
                @if($role == 1 || $role == 12)
                <select name="office_id" class="form-control input-sm" style="width: 140px;">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}"{{ request('office_id') == $office->id ? ' selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
                @endif
                <select name="loan_id" class="form-control input-sm" style="width: 160px;">
                    <option value="">All Loans</option>
                    @foreach($loans as $loan)
                        <option value="{{ $loan->id }}"{{ request('loan_id') == $loan->id ? ' selected' : '' }}>{{ $loan->id }} - {{ $loan->status }}</option>
                    @endforeach
                </select>
                <select name="sort" class="form-control input-sm" style="width: 120px;">
                    <option value="desc"{{ request('sort') == 'desc' ? ' selected' : '' }}>Latest (3→1)</option>
                    <option value="asc"{{ request('sort') == 'asc' ? ' selected' : '' }}>Older (1→3)</option>
                </select>
                <input type="text" name="search" class="form-control input-sm" placeholder="Search..." value="{{ request('search') }}" style="width: 150px;">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('collateral.index') }}" class="btn btn-default btn-sm">Reset</a>
            </form>

            <form method="post" action="{{ route('collateral.export') }}" style="margin-bottom: 15px; text-align: center;">
                {{ csrf_field() }}
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="office_id" value="{{ request('office_id') }}">
                <input type="hidden" name="province_id" value="{{ request('province_id') }}">
                <input type="hidden" name="collateral_type_id" value="{{ request('collateral_type_id') }}">
                <input type="hidden" name="loan_id" value="{{ request('loan_id') }}">
                <input type="hidden" name="condition" value="{{ request('condition') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <button type="submit" class="btn btn-success btn-sm">Export CSV</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                              <tr>
                                  <th>Name</th>
                                  <th>Loan ID</th>
                                  <th>Type</th>
                                  <th>Status</th>
                                   <th>Condition</th>
                                   <th>Purchased</th>
                                  <th>Current Worth</th>
                                   <th>Sold Price<br><small style="font-weight:normal;">Approved collateral value</small></th>
                                   <th>Disposal Costs</th>
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
                                   <td class="{{ match($item->status) {
                                           'sold' => 'bg-green',
                                           'pledged' => 'bg-light',
                                           'seizure_pending' => 'bg-warning',
                                           'seized_inventory' => 'bg-info',
                                           'valuation_completed' => 'bg-aqua',
                                           'listed_for_sale' => 'bg-purple',
                                           'written_off' => 'bg-danger',
                                           'released' => 'bg-teal',
                                           default => ''
                                       } }}">
                                       {{ match($item->status) {
                                           'pledged' => 'Pledged',
                                           'seizure_pending' => 'Seizure Pending',
                                           'seized_inventory' => 'Seized/Inventory',
                                           'valuation_completed' => 'Valuation Completed',
                                           'listed_for_sale' => 'Listed for Sale',
                                           'sold' => 'Sold',
                                           'written_off' => 'Written Off',
                                           'released' => 'Released',
                                           default => ucfirst($item->status)
                                       } }}
                                   </td>
                                   <td>{{ ucfirst($item->condition) }}</td>
                                   <td>{{ optional($item->date_purchased)->format('Y-m-d') }}</td>
                                  <td>{{ number_format($item->current_worth, 2) }}</td>
                                   <td>{{ number_format($item->sold_price ?? 0, 2) }}</td>
                                   <td>
                                       @php
                                           $disposalTotal = 0;
                                           if ($item->disposal_costs && is_array($item->disposal_costs)) {
                                               foreach ($item->disposal_costs as $cost) {
                                                   $disposalTotal += (float) ($cost['amount'] ?? 0);
                                               }
                                           }
                                       @endphp
                                       {{ number_format($disposalTotal, 2) }}
                                   </td>
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
                                   <td colspan="12" class="text-center">No collateral found at this
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
            <div class="text-center" style="margin-top: 20px;">
                {{ $collateral->links() }}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.master')
@section('title', 'Collateral')
@section('content')
<?php
$role = Sentinel::getUser()->roles()->first()->id;
$userPosition = Sentinel::getUser()->position_name;
?>
        <div class="box box-primary">
        @php
            $headerStyle = '';
            $headerTextColor = '';
            if (request('key') === 'admin') {
                $headerStyle = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);';
                $headerTextColor = 'color: #fff;';
            } elseif (request('key') === 'sales') {
                $headerStyle = 'background: linear-gradient(135deg, #28a745 0%, #20c997 100%);';
                $headerTextColor = 'color: #fff;';
            } elseif (request('key') === 'valuation') {
                $headerStyle = 'background: linear-gradient(135deg, #17a2b8 0%, #0078d4 100%);';
                $headerTextColor = 'color: #fff;';
            }
        @endphp
        <div class="box-header with-border" @if($headerStyle) style="{{ $headerStyle }}" @endif>
            <h3 class="box-title"@if($headerTextColor) style="{{ $headerTextColor }}"@endif>Collateral</h3>
            @if($userPosition)
                <small class="text-muted" style="margin-left: 10px;"@if($headerTextColor) style="color: #e0e0e0; margin-left: 10px;"@endif>({{ $userPosition }})</small>
            @endif
            @if(request('key') === 'admin')
                <div style="margin-top: 10px;">
                    <h4 style="margin: 0; font-weight: 600; color: #fff;">Manage Institution Disposal Assets</h4>
                    <p style="margin: 0; font-size: 12px; color: #e0e0e0;">collateral loans that are currently seized by the institution from defaulted loans and ran away clients</p>
                </div>
            @elseif(request('key') === 'sales')
                <div style="margin-top: 10px;">
                    <h4 style="margin: 0; font-weight: 600; color: #fff;">Sales and Listings</h4>
                    <p style="margin: 0; font-size: 12px; color: #e0e0e0;">collateral in the valuation completed, listed for sale, and sold stages</p>
                </div>
            @elseif(request('key') === 'valuation')
                <div style="margin-top: 10px;">
                    <h4 style="margin: 0; font-weight: 600; color: #fff;">Valuation Pending</h4>
                    <p style="margin: 0; font-size: 12px; color: #e0e0e0;">collateral currently awaiting valuation</p>
                </div>
            @endif
            @if($role == 3 || $role == 4)
            <div class="box-tools pull-right">
                    <a href="{{ route('collateral.create') }}" class="btn btn-success btn-sm">Add Collateral</a>
            </div>
            @endif
        </div>
        <div class="box-body">
            <form method="get" action="{{ route('collateral.index') }}" class="form-inline" style="margin-bottom: 15px; display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                <select name="status" class="form-control input-sm" style="width: 140px;">
                    @if(request('key') === 'admin')
                        <option value="">All Statuses</option>
                        <option value="seized_inventory"{{ request('status') == 'seized_inventory' ? ' selected' : '' }}>Seized/Inventory</option>
                        <option value="valuation_completed"{{ request('status') == 'valuation_completed' ? ' selected' : '' }}>Valuation Completed</option>
                        <option value="listed_for_sale"{{ request('status') == 'listed_for_sale' ? ' selected' : '' }}>Listed for Sale</option>
                        <option value="written_off"{{ request('status') == 'written_off' ? ' selected' : '' }}>Written Off</option>
                    @elseif(request('key') === 'sales')
                        <option value="">All Statuses</option>
                        <option value="valuation_completed"{{ request('status') == 'valuation_completed' ? ' selected' : '' }}>Valuation Completed</option>
                        <option value="listed_for_sale"{{ request('status') == 'listed_for_sale' ? ' selected' : '' }}>Listed for Sale</option>
                        <option value="sold"{{ request('status') == 'sold' ? ' selected' : '' }}>Sold</option>
                    @elseif(request('key') === 'valuation')
                        <option value="seized_inventory"{{ request('status') == 'seized_inventory' ? ' selected' : '' }}>Seized/Inventory</option>
                    @else
                        <option value="">All Statuses</option>
                        <option value="pledged"{{ request('status') == 'pledged' ? ' selected' : '' }}>Pledged</option>
                        <option value="seizure_pending"{{ request('status') == 'seizure_pending' ? ' selected' : '' }}>Seizure Pending</option>
                        <option value="seized_inventory"{{ request('status') == 'seized_inventory' ? ' selected' : '' }}>Seized/Inventory</option>
                        <option value="valuation_completed"{{ request('status') == 'valuation_completed' ? ' selected' : '' }}>Valuation Completed</option>
                        <option value="listed_for_sale"{{ request('status') == 'listed_for_sale' ? ' selected' : '' }}>Listed for Sale</option>
                        <option value="sold"{{ request('status') == 'sold' ? ' selected' : '' }}>Sold</option>
                        <option value="written_off"{{ request('status') == 'written_off' ? ' selected' : '' }}>Written Off</option>
                        <option value="released"{{ request('status') == 'released' ? ' selected' : '' }}>Released</option>
                        <option value="release_pending"{{ request('status') == 'release_pending' ? ' selected' : '' }}>Release Pending</option>
                    @endif
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
                <input type="hidden" name="key" value="{{ request('key') }}">
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

            @if(request('key') === 'admin')
            <div class="row" style="margin-bottom: 20px;">
                @php
                    $statLabels = [
                        'seized_inventory' => 'Seized/Inventory',
                        'valuation_completed' => 'Valuation Completed',
                        'listed_for_sale' => 'Listed for Sale',
                        'written_off' => 'Written Off',
                    ];
                @endphp
                @foreach($statLabels as $statusKey => $label)
                    <div class="col-md-2 col-sm-3 col-xs-6">
                        <div class="small-box" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                            <div class="inner">
                                <h4 style="margin: 0; font-weight: 600; color: #495057;">{{ number_format($disposalStats[$statusKey]['count']) }}</h4>
                                <p style="margin: 4px 0 0; font-size: 12px; color: #6c757d;">Count</p>
                                <h4 style="margin: 8px 0 0; font-weight: 600; color: #495057;">{{ number_format($disposalStats[$statusKey]['sum'], 2) }}</h4>
                                <p style="margin: 4px 0 0; font-size: 12px; color: #6c757d;">Current Worth</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-tag" style="color: #6c757d;"></i>
                            </div>
                            <a href="{{ route('collateral.index', array_merge(request()->all(), ['status' => $statusKey])) }}" class="small-box-footer" style="font-size: 12px;">
                                {{ $label }}
                            </a>
                        </div>
                    </div>
                @endforeach
                <div class="col-md-2 col-sm-3 col-xs-6">
                    <div class="small-box" style="background: #667eea; border: 1px solid #764ba2;">
                        <div class="inner">
                            <h4 style="margin: 0; font-weight: 600; color: #fff;">{{ number_format($totalCount) }}</h4>
                            <p style="margin: 4px 0 0; font-size: 12px; color: #e0e0e0;">Total Count</p>
                            <h4 style="margin: 8px 0 0; font-weight: 600; color: #fff;">{{ number_format($totalWorth, 2) }}</h4>
                            <p style="margin: 4px 0 0; font-size: 12px; color: #e0e0e0;">Total Worth</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-cube" style="color: #e0e0e0;"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
                                <th>Current Worth<br><small style="font-weight:normal;">Approved collateral value</small></th>
                                <th>Sold Price</th>
                                <th>Disposal Costs</th>
                                <th>Created By</th>
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
                                            'release_pending' => 'bg-orange',
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
                                            'release_pending' => 'Release Pending',
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
                                    <td>
                                        {{ optional($item->created_by)->first_name }} {{ optional($item->created_by)->last_name }}
                                        <br>
                                        <small>{{ optional($item->created_by)->position_name }}</small>
                                    </td>
                                    <td>{{ $item->loan?->office?->name }}</td>
                                  <td>
                                     <a href="{{ route('collateral.show', $item) }}" class="btn btn-xs btn-primary">View</a>
                                      @if($role == 1 || (Sentinel::getUser()->id == $item->created_by_id && $item->status === 'pledged') || ($role == 4 && in_array($item->status, ['pledged', 'seizure_pending'])))
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
                                    <td colspan="13" class="text-center">No collateral found at this
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

@extends('layouts.master')
@section('title', 'Generate Collateral Report')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Generate Report &mdash; {{ $report['label'] }}</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-info btn-sm" id="open-reports-modal"><i class="fa fa-bar-chart"></i> Show Reports</button>
            </div>
        </div>
        <div class="box-body">
            <p class="text-muted">{{ $report['description'] }}</p>

            <form method="post" action="{{ route('collateral.reports.generate', $type) }}" class="form-horizontal">
                {{ csrf_field() }}

                <div class="form-group">
                    <label class="control-label col-md-3">Date From</label>
                    <div class="col-md-4">
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from') }}">
                    </div>
                    <label class="control-label col-md-2">Date To</label>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" value="{{ old('date_to') }}">
                    </div>
                </div>

                @if($type === 'inventory_valuation')
                <div class="form-group">
                    <label class="control-label col-md-3">Age In Inventory Based On</label>
                    <div class="col-md-4">
                        <select name="age_basis" class="form-control">
                            <option value="created">Date Created</option>
                            <option value="seized">Date Seized</option>
                        </select>
                        <small class="form-help-text">Used to calculate how long an asset has been in inventory.</small>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="control-label col-md-3">Province</label>
                    <div class="col-md-3">
                        <select name="province_id" class="form-control">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->id }}"{{ old('province_id') == $p->id ? ' selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="control-label col-md-2">District</label>
                    <div class="col-md-3">
                        <select name="district_id" class="form-control">
                            <option value="">All Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}"{{ old('district_id') == $d->id ? ' selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Office / Branch</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control">
                            <option value="">All Offices</option>
                            @foreach($offices as $o)
                                <option value="{{ $o->id }}"{{ old('office_id') == $o->id ? ' selected' : '' }}>{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="control-label col-md-2">Asset Type</label>
                    <div class="col-md-3">
                        <select name="collateral_type_id" class="form-control">
                            <option value="">All Types</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}"{{ old('collateral_type_id') == $t->id ? ' selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Category</label>
                    <div class="col-md-3">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}"{{ old('category') == $key ? ' selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="control-label col-md-2">Condition</label>
                    <div class="col-md-3">
                        <select name="condition" class="form-control">
                            <option value="">All Conditions</option>
                            <option value="new"{{ old('condition') == 'new' ? ' selected' : '' }}>New</option>
                            <option value="good"{{ old('condition') == 'good' ? ' selected' : '' }}>Good</option>
                            <option value="fair"{{ old('condition') == 'fair' ? ' selected' : '' }}>Fair</option>
                            <option value="poor"{{ old('condition') == 'poor' ? ' selected' : '' }}>Poor</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <a href="{{ route('collateral.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('collateral.reports._report_modal')
@stop

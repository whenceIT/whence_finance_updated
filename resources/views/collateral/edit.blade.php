@extends('layouts.master')
@section('title', 'Edit Collateral')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Collateral</h3>
            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-default btn-sm">Cancel</button>
            </div>
        </div>
        <form method="post" action="{{ route('collateral.update', $collateral) }}" class="form-horizontal">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label col-md-2">Status</label>
                    <div class="col-md-8">
                        @php
                            $statusLabel = match($collateral->status) {
                                'pledged' => 'Pledged',
                                'seizure_pending' => 'Seizure Pending',
                                'seized_inventory' => 'Seized/Inventory',
                                'valuation_completed' => 'Valuation Completed',
                                'listed_for_sale' => 'Listed for Sale',
                                'sold' => 'Sold',
                                'written_off' => 'Written Off',
                                'released' => 'Released',
                                default => ucfirst($collateral->status),
                            };
                        @endphp
                        <input type="text" readonly class="form-control" value="{{ $statusLabel }}">
                    </div>
                </div>

                <div class="form-group{{ $errors->has('serial_num') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Serial Number</label>
                    <div class="col-md-8">
                        <input type="text" name="serial_num" class="form-control" value="{{ old('serial_num', $collateral->serial_num) }}" required>
                        {!! $errors->first('serial_num', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('current_worth') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Current Worth</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="current_worth" class="form-control" value="{{ old('current_worth', $collateral->current_worth) }}" required>
                        {!! $errors->first('current_worth', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Approved Value</label>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="approved_value" class="form-control" value="{{ old('approved_value', $collateral->approved_value ?? $collateral->current_worth) }}">
                    </div>
                    <label class="control-label col-md-2">Condition</label>
                    <div class="col-md-2">
                        <select name="condition" class="form-control" required>
                            <option value="new"{{ old('condition', $collateral->condition) == 'new' ? ' selected' : '' }}>New</option>
                            <option value="good"{{ old('condition', $collateral->condition) == 'good' ? ' selected' : '' }}>Good</option>
                            <option value="fair"{{ old('condition', $collateral->condition) == 'fair' ? ' selected' : '' }}>Fair</option>
                            <option value="poor"{{ old('condition', $collateral->condition) == 'poor' ? ' selected' : '' }}>Poor</option>
                        </select>
                        {!! $errors->first('condition', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('date_resold') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Date Resold</label>
                    <div class="col-md-4">
                        <input type="date" name="date_resold" class="form-control" value="{{ old('date_resold', optional($collateral->date_resold)->format('Y-m-d')) }}">
                        {!! $errors->first('date_resold', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">Lifecycle Dates</label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Pledged At</label>
                                <input type="date" name="pledged_at" class="form-control" value="{{ old('pledged_at', optional($collateral->pledged_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Seized At</label>
                                <input type="date" name="seized_at" class="form-control" value="{{ old('seized_at', optional($collateral->seized_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Valuated At</label>
                                <input type="date" name="valuated_at" class="form-control" value="{{ old('valuated_at', optional($collateral->valuated_at)->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <label>Listed At</label>
                                <input type="date" name="listed_at" class="form-control" value="{{ old('listed_at', optional($collateral->listed_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Sold At</label>
                                <input type="date" name="sold_at" class="form-control" value="{{ old('sold_at', optional($collateral->sold_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Written Off At</label>
                                <input type="date" name="written_off_at" class="form-control" value="{{ old('written_off_at', optional($collateral->written_off_at)->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <label>Released At</label>
                                <input type="date" name="released_at" class="form-control" value="{{ old('released_at', optional($collateral->released_at)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Description</label>
                    <div class="col-md-8">
                        <textarea name="description" class="form-control">{{ old('description', $collateral->description) }}</textarea>
                        {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('stage_icon') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Stage Icon (SVG)</label>
                    <div class="col-md-8">
                        <textarea name="stage_icon" class="form-control" rows="3" placeholder="<svg>...</svg>">{{ old('stage_icon', $collateral->stage_icon) }}</textarea>
                        {!! $errors->first('stage_icon', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" onclick="window.history.back()" class="btn btn-default">Cancel</button>
            </div>
        </form>
    </div>
@endsection

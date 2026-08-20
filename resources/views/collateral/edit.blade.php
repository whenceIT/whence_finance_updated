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
                        <input type="text" readonly class="form-control" value="{{ ucfirst($collateral->status) }}">
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

                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Description</label>
                    <div class="col-md-8">
                        <textarea name="description" class="form-control">{{ old('description', $collateral->description) }}</textarea>
                        {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('stage') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Stage</label>
                    <div class="col-md-4">
                        <select name="stage" class="form-control">
                            <option value="">Select stage</option>
                            @foreach($stageOptions as $value => $label)
                                <option value="{{ $value }}"{{ old('stage', $collateral->stage) == $value ? ' selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('stage', '<span class="help-block">:message</span>') !!}
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

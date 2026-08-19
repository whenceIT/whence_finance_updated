@extends('layouts.master')
@section('title', 'Add Collateral')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Add Collateral</h3>
            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-default btn-sm">Cancel</button>
            </div>
        </div>
        <form method="post" action="{{ route('collateral.store') }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group{{ $errors->has('loan_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Loan</label>
                    <div class="col-md-8">
                        <select name="loan_id" class="form-control select2" required>
                            <option value="">Select loan</option>
                            @php
                                $selectedLoanId = old('loan_id', $loanId ?? '');
                            @endphp
                            @foreach($loans as $loan)
                                @php
                                    $client = optional($loan->client);
                                @endphp

                                <option 
                                    value="{{ $loan->id }}"
                                    {{ $selectedLoanId == $loan->id ? 'selected' : '' }}
                                >
                                    #{{ $loan->id }} 
                                    | K{{ number_format($loan->principal, 2) }} 
                                    - {{ $client->first_name ?? 'Unknown' }} {{ $client->last_name ?? '' }}
                                    ({{ ucfirst($loan->status) }})
                                </option>                            
                            @endforeach
                        </select>
                        {!! $errors->first('loan_id', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('collateral_type_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Collateral Type</label>
                    <div class="col-md-8">
                        <select name="collateral_type_id" class="form-control select2">
                            <option value="">Select type</option>
                            @foreach($collateralTypes as $type)
                                <option value="{{ $type->id }}"{{ old('collateral_type_id') == $type->id ? ' selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('collateral_type_id', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Name</label>
                    <div class="col-md-8">
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Description</label>
                    <div class="col-md-8">
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('initial_price') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Initial Price</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="initial_price" class="form-control" value="{{ old('initial_price') }}" required>
                        {!! $errors->first('initial_price', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Current Market Price</label>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="current_worth" class="form-control" value="{{ old('current_worth') }}" required>
                        {!! $errors->first('current_worth', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('date_purchased') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Date Purchased</label>
                    <div class="col-md-4">
                        <input type="date" name="date_purchased" class="form-control" value="{{ old('date_purchased') }}" required>
                        {!! $errors->first('date_purchased', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Status</label>
                    <div class="col-md-2">
                        <select name="status" class="form-control" required>
                            <option value="active"{{ old('status') == 'active' ? ' selected' : '' }}>Active</option>
                            <option value="sold"{{ old('status') == 'sold' ? ' selected' : '' }}>Sold</option>
                            <option value="defaulted"{{ old('status') == 'defaulted' ? ' selected' : '' }}>Defaulted</option>
                            <option value="repossessed"{{ old('status') == 'repossessed' ? ' selected' : '' }}>Repossessed</option>
                        </select>
                        {!! $errors->first('status', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('condition') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Condition</label>
                    <div class="col-md-4">
                        <select name="condition" class="form-control" required>
                            <option value="new"{{ old('condition') == 'new' ? ' selected' : '' }}>New</option>
                            <option value="good"{{ old('condition') == 'good' ? ' selected' : '' }}>Good</option>
                            <option value="fair"{{ old('condition') == 'fair' ? ' selected' : '' }}>Fair</option>
                            <option value="poor"{{ old('condition') == 'poor' ? ' selected' : '' }}>Poor</option>
                        </select>
                        {!! $errors->first('condition', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('stage') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Stage</label>
                    <div class="col-md-4">
                        <select name="stage" class="form-control" required>
                            <option value="">Select stage</option>
                            @foreach($stageOptions as $value => $label)
                                <option value="{{ $value }}"{{ old('stage') == $value ? ' selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('stage', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('stage_icon') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Stage Icon (SVG)</label>
                    <div class="col-md-8">
                        <textarea name="stage_icon" class="form-control" rows="3" placeholder="<svg>...</svg>">{{ old('stage_icon') }}</textarea>
                        {!! $errors->first('stage_icon', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
                </div>

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary" id="save-collateral-btn">
                    <span class="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> </span>
                    <span class="text">Save Collateral</span>
                </button>
                <button type="button" onclick="window.history.back()" class="btn btn-default">Cancel</button>
            </div>
            <script>
                $(document).ready(function() {
                    $('#save-collateral-btn').click(function() {
                        $(this).find('.spinner').show();
                        $(this).find('.text').text('Saving...');
                    });
                });
            </script>
        </form>
    </div>
@endsection


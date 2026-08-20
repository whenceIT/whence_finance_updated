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
                        <small class="form-help-text">How much did the customer purchase this item</small>
                        {!! $errors->first('initial_price', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Current Market Price</label>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="current_worth" class="form-control" value="{{ old('current_worth') }}" required>
                        <div id="create-analysis-verdict"></div>
                        <small class="form-help-text">Current collateral value (current pricing according to items condition)</small>
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
                            <small class="form-help-text">
                                Pledged is collateral not brought in but registered as collateral.<br>
                                Brought in collateral is one brought to the offices.<br>
                                Seized Collateral is collateral collected by the office from client's premise (at a costs)
                            </small>
                            {!! $errors->first('stage', '<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-2">Disposal Costs (Estimate)</label>
                        <div class="col-md-4">
                            <input type="number" step="0.01" name="create_disposal_costs" class="form-control" value="0" id="create_disposal_costs">
                            <small class="form-help-text">Estimated expenses incurred during collateral disposal</small>
                        </div>
                    </div>

                    <!-- <div class="cd-panel cd-analysis-panel" style="margin-top: 20px;">
                        <div class="cd-panel-header">
                            <h3>Create Analysis</h3>
                        </div>
                        <div class="cd-panel-body">
                            <div class="cd-fieldgrid">
                                <div>
                                    <div class="cd-field">
                                        <span class="cd-label">Current Worth</span>
                                        <span class="cd-value" id="create-analysis-current-worth">{{ number_format(old('current_worth', 0), 2) }}</span>
                                    </div>
                                    <div class="cd-field">
                                        <span class="cd-label">Loan Balance</span>
                                        <span class="cd-value" id="create-analysis-loan-balance">0.00</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="cd-field">
                                        <span class="cd-label">Disposal Costs</span>
                                        <span class="cd-value" id="create-analysis-disposal-costs">0.00</span>
                                    </div>
                                    <div class="cd-field">
                                        <span class="cd-label">Net Proceeds</span>
                                        <span class="cd-value" id="create-analysis-net-proceeds">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 12px;">
                                <div id="create-analysis-verdict"></div>
                            </div>
                        </div>
                    </div>
                </div> -->

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
                    var loanBalances = @json($loanBalances ?? []);
                    var defaultLoanId = '{{ old('loan_id', $loanId ?? '') }}';

                    function getLoanBalance() {
                        var loanId = $('select[name="loan_id"]').val();
                        if (loanId && loanBalances[loanId] !== undefined) {
                            return parseFloat(loanBalances[loanId]);
                        }
                        return 0;
                    }

                    function updateCreateAnalysis() {
                        var currentWorth = parseFloat($('input[name="current_worth"]').val()) || 0;
                        var disposalCosts = parseFloat($('input[name="create_disposal_costs"]').val()) || 0;
                        var loanBalance = getLoanBalance();
                        var netProceeds = currentWorth - disposalCosts;

                        $('#create-analysis-current-worth').text(currentWorth.toFixed(2));
                        $('#create-analysis-loan-balance').text(loanBalance.toFixed(2));
                        $('#create-analysis-disposal-costs').text(disposalCosts.toFixed(2));
                        $('#create-analysis-net-proceeds').text(netProceeds.toFixed(2));

                        if (netProceeds >= loanBalance) {
                            $('#create-analysis-verdict').html('<span class="label label-success">Covers Loan Balance</span>');
                            $('#save-collateral-btn').prop('disabled', false);
                        } else {
                            var shortfall = loanBalance - netProceeds;
                            $('#create-analysis-verdict').html('<span class="label label-danger">Below Loan Balance</span><div class="alert alert-danger" style="margin-top: 10px;">Net proceeds are <strong>' + netProceeds.toFixed(2) + '</strong>, but the loan balance is <strong>' + loanBalance.toFixed(2) + '</strong>. You need <strong>' + shortfall.toFixed(2) + '</strong> more to cover the loan balance.</div>');
                            $('#save-collateral-btn').prop('disabled', true);
                        }
                    }

                    $('select[name="loan_id"], input[name="current_worth"], input[name="create_disposal_costs"]').on('input change', function() {
                        updateCreateAnalysis();
                    });

                    $('#save-collateral-btn').click(function(e) {
                        var loanBalance = getLoanBalance();
                        var currentWorth = parseFloat($('input[name="current_worth"]').val()) || 0;
                        var disposalCosts = parseFloat($('input[name="create_disposal_costs"]').val()) || 0;
                        var netProceeds = currentWorth - disposalCosts;

                        if (netProceeds < loanBalance) {
                            e.preventDefault();
                            alert('The collateral value must cover the loan balance. Net proceeds must be at least ' + loanBalance.toFixed(2) + '.');
                            return false;
                        }
                        $(this).find('.spinner').show();
                        $(this).find('.text').text('Saving...');
                    });
                });
            </script>
        </form>
    </div>
    <style>
        .form-help-text {
            display: block;
            margin-top: 4px;
            color: #8a94a6;
            font-size: 12px;
        }
        .cd-analysis-panel {
            background: #fff;
            border: 1px solid #e6e9ef;
            border-radius: 6px;
            overflow: hidden;
        }
        .cd-analysis-panel .cd-panel-header {
            padding: 14px 20px;
            border-bottom: 1px solid #eef0f4;
        }
        .cd-analysis-panel .cd-panel-header h3 {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .cd-analysis-panel .cd-panel-body {
            padding: 20px;
        }
        .cd-fieldgrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .cd-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f2f5;
        }
        .cd-field:last-child {
            border-bottom: none;
        }
        .cd-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        .cd-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
        }
    </style>
@endsection


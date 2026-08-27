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
                @php
                    $currentStep = old('status', 'pledged');
                    $allStatuses = [
                        'pledged' => 'Pledged',
                        'seizure_pending' => 'Seizure Pending',
                        'seized_inventory' => 'Seized/Inventory',
                        'valuation_completed' => 'Valuation Completed',
                        'listed_for_sale' => 'Listed for Sale',
                        'sold' => 'Sold',
                        'written_off' => 'Written Off',
                        'released' => 'Released',
                    ];
                @endphp

                <x-collateral-timeline :currentStatus="$currentStep" :showHeader="true" />

                <div style="margin-bottom: 20px;">
                    <div style="background: #fff; border: 1px solid #e6e9ef; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 10px 16px; border-bottom: 1px solid #eef0f4; display: flex; align-items: center; justify-content: space-between; cursor: pointer;" id="createAnalysisHeader">
                            <h3 style="font-size: 13px; font-weight: 700; color: #2c3e50; margin: 0; text-transform: uppercase; letter-spacing: .03em;">Create Analysis</h3>
                            <i class="fa fa-chevron-down" style="color: #6b7280; font-size: 12px; transition: transform 0.3s;" id="createAnalysisIcon"></i>
                        </div>
                        <div class="cd-panel-body" id="createAnalysisBody" style="padding: 14px 16px; display: none;">
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                                <div style="display: flex; justify-content: space-between; padding: 2px 0; border-bottom: 1px solid #f0f2f5;">
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;">Current Worth</span>
                                    <span style="font-size: 13px; color: #2c3e50; font-weight: 600;" id="create-analysis-current-worth">{{ number_format(old('current_worth', 0), 2) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f2f5;">
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;">Loan Balance</span>
                                    <span style="font-size: 13px; color: #2c3e50; font-weight: 600;" id="create-analysis-loan-balance">0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f2f5;">
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;">Net Proceeds</span>
                                    <span style="font-size: 13px; color: #2c3e50; font-weight: 600;" id="create-analysis-net-proceeds">0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f2f5;">
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;">Status</span>
                                    <span style="font-size: 13px; color: #2c3e50; font-weight: 600;" id="create-analysis-status">{{ $allStatuses[$currentStep] ?? 'Pledged' }}</span>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <div id="create-analysis-verdict"></div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <option value="{{ $loan->id }}" {{ $selectedLoanId == $loan->id ? 'selected' : '' }}>
                                    #{{ $loan->id }} | K{{ number_format($loan->principal, 2) }} - {{ $client->first_name ?? 'Unknown' }} {{ $client->last_name ?? '' }} ({{ ucfirst($loan->status) }})
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

                <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Category</label>
                    <div class="col-md-8">
                        <select name="category" class="form-control select2" required>
                            <option value="">Select category</option>
                            @foreach(\App\Models\Collateral::categoryOptions() as $catKey => $catLabel)
                                <option value="{{ $catKey }}"{{ old('category') == $catKey ? ' selected' : '' }}>{{ $catLabel }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('category', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('serial_num') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Serial Number</label>
                    <div class="col-md-8">
                        <input type="text" name="serial_num" class="form-control" value="{{ old('serial_num') }}">
                        {!! $errors->first('serial_num', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Description</label>
                    <div class="col-md-8">
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('date_purchased') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Date Purchased</label>
                    <div class="col-md-4">
                        <input type="date" name="date_purchased" class="form-control" value="{{ old('date_purchased') }}" required>
                        {!! $errors->first('date_purchased', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Date Pledged</label>
                    <div class="col-md-2">
                        <input type="date" name="pledged_at" class="form-control" value="{{ old('pledged_at') }}">
                        {!! $errors->first('pledged_at', '<span class="help-block">:message</span>') !!}
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
                        <small class="form-help-text">Current collateral value (current pricing according to items condition)</small>
                        {!! $errors->first('current_worth', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('vetted_valuation') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Vetted Valuation</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="vetted_valuation" class="form-control" value="{{ old('vetted_valuation') }}">
                        <small class="form-help-text">Amount determined after staff vetted the collateral</small>
                        {!! $errors->first('vetted_valuation', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group" id="vvc-items-group">
                    <label class="control-label col-md-2">Vetted By</label>
                    <div class="col-md-4">
                        <select name="vetted_valuation_by" id="vetted-valuation-by" class="form-control select2" style="width: 100%;">
                            <option value="">Select user</option>
                        </select>
                        <small class="form-help-text">User who vetted the selected loan</small>
                    </div>
                </div>
                </div>

                <div class="form-group" id="vvc-items-group">
                    <label class="control-label col-md-2">Vetted Valuation Cost</label>
                    <div class="col-md-10">
                        @if(old('vvc_items'))
                            @foreach(old('vvc_items', []) as $idx => $item)
                                <div class="vvc-item-row" style="margin-bottom: 8px; display: flex; gap: 8px; align-items: center;">
                                    <input type="text" name="vvc_items[{{$idx}}][name]" class="form-control vvc-name" value="{{ $item['name'] ?? '' }}" style="width: 50%;" placeholder="Description">
                                    <input type="number" step="0.01" name="vvc_items[{{$idx}}][amount]" class="form-control vvc-amount" value="{{ $item['amount'] ?? '' }}" style="width: 30%;" placeholder="Amount">
                                    <button type="button" class="btn btn-danger btn-xs remove-vvc-item"><i class="fa fa-minus"></i></button>
                                </div>
                            @endforeach
                         @endif
                        <div id="vvc-items-list"></div>
                        <button type="button" class="btn btn-success btn-xs" id="add-vvc-item" style="margin-top: 8px;"><i class="fa fa-plus"></i> Add Cost Item</button>
                        <div style="margin-top: 8px; font-weight: 600;">
                            Total: <span id="vvc-total">0.00</span>
                        </div>
                        <input type="hidden" name="vetted_valuation_cost" id="vetted_valuation_cost_input" value="">
                        <small class="form-help-text">Cost spent to acquire the collateral (leave empty if no amount was spent)</small>
                        {!! $errors->first('vetted_valuation_cost', '<span class="help-block">:message</span>') !!}
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
        </form>
    </div>
    <style>
        .form-help-text {
            display: block;
            margin-top: 4px;
            color: #8a94a6;
            font-size: 12px;
        }
    </style>
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
                var loanBalance = getLoanBalance();
                var netProceeds = currentWorth;

                $('#create-analysis-current-worth').text(currentWorth.toFixed(2));
                $('#create-analysis-loan-balance').text(loanBalance.toFixed(2));
                $('#create-analysis-net-proceeds').text(netProceeds.toFixed(2));

                if (netProceeds >= loanBalance) {
                    $('#create-analysis-verdict').html('<span class="label label-success">Covers Loan Balance</span>');
                    // $('#save-collateral-btn').prop('disabled', false);
                } else {
                    var shortfall = loanBalance - netProceeds;
                    $('#create-analysis-verdict').html('<span class="label label-danger">Below Loan Balance</span><div class="alert alert-danger" style="margin-top: 10px;">Net proceeds are <strong>' + netProceeds.toFixed(2) + '</strong>, but the loan balance is <strong>' + loanBalance.toFixed(2) + '</strong>. You need <strong>' + shortfall.toFixed(2) + '</strong> more to cover the loan balance.</div>');
                    // $('#save-collateral-btn').prop('disabled', true);
                }
            }

            $('select[name="loan_id"], input[name="current_worth"]').on('input change', function() {
                updateCreateAnalysis();
            });

            $('#createAnalysisHeader').on('click', function() {
                $('#createAnalysisBody').slideToggle();
                $('#createAnalysisIcon').toggleClass('fa-chevron-down').toggleClass('fa-chevron-up');
            });

            var vvcIndex = @json(count(old('vvc_items', [])));
            var vvcTemplate = '<div class="vvc-item-row" style="margin-bottom: 8px; display: flex; gap: 8px; align-items: center;">' +
                '<input type="text" name="vvc_items[__idx__][name]" class="form-control vvc-name" value="" style="width: 50%;" placeholder="Description">' +
                '<input type="number" step="0.01" name="vvc_items[__idx__][amount]" class="form-control vvc-amount" value="" style="width: 30%;" placeholder="Amount">' +
                '<button type="button" class="btn btn-danger btn-xs remove-vvc-item"><i class="fa fa-minus"></i></button>' +
                '</div>';

            $('#add-vvc-item').click(function() {
                var row = $(vvcTemplate.replace(/__idx__/g, vvcIndex));
                $('#vvc-items-list').append(row);
                vvcIndex++;
                updateVvcTotal();
            });

            $(document).on('click', '.remove-vvc-item', function() {
                $(this).closest('.vvc-item-row').remove();
                updateVvcTotal();
            });

            $(document).on('input', '.vvc-amount', function() {
                updateVvcTotal();
            });

            function updateVvcTotal() {
                var total = 0;
                $('.vvc-amount').each(function() {
                    var val = parseFloat($(this).val()) || 0;
                    total += val;
                });
                $('#vvc-total').text(total.toFixed(2));
                $('#vetted_valuation_cost_input').val(total.toFixed(2));
            }

            updateVvcTotal();

            function updateVettedByDisplay() {
                var loanId = $('select[name="loan_id"]').val();
                if (loanId) {
                    $.ajax({
                        url: '/api/loan-vetted-by/' + loanId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.data) {
                                var userId = response.data.id;
                                var userName = response.data.first_name + ' ' + response.data.last_name;
                                $('#vetted-valuation-by').empty();
                                $('#vetted-valuation-by').append('<option value="' + userId + '">' + userName + '</option>');
                                $('#vetted-valuation-by').val(userId).trigger('change');
                                $('#vetted-valuation-by').prop('disabled', true);
                            } else {
                                $('#vetted-valuation-by').empty();
                                $('#vetted-valuation-by').append('<option value="">Select user</option>');
                                $('#vetted-valuation-by').val('').trigger('change');
                                $('#vetted-valuation-by').prop('disabled', false);
                            }
                        },
                        error: function() {
                            $('#vetted-valuation-by').empty();
                            $('#vetted-valuation-by').append('<option value="">Select user</option>');
                            $('#vetted-valuation-by').val('').trigger('change');
                            $('#vetted-valuation-by').prop('disabled', false);
                        }
                    });
                } else {
                    $('#vetted-valuation-by').empty();
                    $('#vetted-valuation-by').append('<option value="">Select user</option>');
                    $('#vetted-valuation-by').val('').trigger('change');
                    $('#vetted-valuation-by').prop('disabled', false);
                }
            }

            $('select[name="loan_id"]').on('change', function() {
                updateVettedByDisplay();
            });

            $('#vetted-valuation-by').select2({
                placeholder: 'Search for a user...',
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: '/loan/users/search',
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    }
                }
            });

            updateVettedByDisplay();

            $('#save-collateral-btn').click(function(e) {
                var loanBalance = getLoanBalance();
                var currentWorth = parseFloat($('input[name="current_worth"]').val()) || 0;
                var netProceeds = currentWorth;

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
@endsection

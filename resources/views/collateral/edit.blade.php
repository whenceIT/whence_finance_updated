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

                <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Category</label>
                    <div class="col-md-8">
                        <select name="category" class="form-control select2">
                            <option value="">Select category</option>
                            @foreach(\App\Models\Collateral::categoryOptions() as $catKey => $catLabel)
                                <option value="{{ $catKey }}"{{ old('category', $collateral->category) == $catKey ? ' selected' : '' }}>{{ $catLabel }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('category', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('approved_value') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Approved Value</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="approved_value" class="form-control" value="{{ old('approved_value', $collateral->approved_value ?? $collateral->current_worth) }}">
                    </div>
                    <label class="control-label col-md-2">Condition</label>
                    <div class="col-md-4">
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

                <div class="form-group{{ $errors->has('initial_price') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Initial Price</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="initial_price" class="form-control" value="{{ old('initial_price', $collateral->initial_price) }}" required>
                        <small class="form-help-text">How much did the customer purchase this item</small>
                        {!! $errors->first('initial_price', '<span class="help-block">:message</span>') !!}
                    </div>
                    <label class="control-label col-md-2">Current Market Price</label>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="current_worth" class="form-control" value="{{ old('current_worth', $collateral->current_worth) }}" required>
                        <small class="form-help-text">Current collateral value (current pricing according to items condition)</small>
                        {!! $errors->first('current_worth', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('vetted_valuation') ? ' has-error' : '' }}">
                    <label class="control-label col-md-2">Vetted Valuation</label>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="vetted_valuation" class="form-control" value="{{ old('vetted_valuation', $collateral->vetted_valuation) }}">
                        <small class="form-help-text">Amount determined after staff vetted the collateral</small>
                        {!! $errors->first('vetted_valuation', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">Vetted By</label>
                    <div class="col-md-4">
                        <select name="vetted_valuation_by" id="vetted-valuation-by" class="form-control select2" style="width: 100%;">
                            @if($collateral->vetted_valuation_by)
                                @php $vettedBy = optional(optional($collateral->loan)->vetted_by_field); @endphp
                                <option value="{{ $collateral->vetted_valuation_by }}">{{ $vettedBy->first_name ?? '' }} {{ $vettedBy->last_name ?? '' }}</option>
                            @endif
                            <option value="">Select user</option>
                        </select>
                        <small class="form-help-text">User who vetted the selected loan</small>
                        <input type="hidden" name="vetted_valuation_by_existing" value="{{ $collateral->vetted_valuation_by }}">
                    </div>
                </div>

                <div class="form-group" id="vvc-items-group">
                    <label class="control-label col-md-2">Vetted Valuation Cost</label>
                    <div class="col-md-10">
                        @php
                            $vvcItems = old('vvc_items', $collateral->vvc_items ?? []);
                        @endphp
                        @if(is_array($vvcItems) && count($vvcItems) > 0)
                            @foreach($vvcItems as $idx => $item)
                                <div class="vvc-item-row" style="margin-bottom: 8px; display: flex; gap: 8px; align-items: center;">
                                    <input type="text" name="vvc_items[{{$idx}}][name]" class="form-control vvc-name" value="{{ $item['name'] ?? '' }}" style="width: 50%;" placeholder="Description">
                                    <input type="number" step="0.01" name="vvc_items[{{$idx}}][amount]" class="form-control vvc-amount" value="{{ $item['amount'] ?? '' }}" style="width: 30%;" placeholder="Amount">
                                    <button type="button" class="btn btn-danger btn-xs remove-vvc-item"><i class="fa fa-minus"></i></button>
                                </div>
                            @endforeach
                        @endif
                        <div id="vvc-items-list"></div>
                        <div class="vvc-item-row d-none" style="margin-bottom: 8px; display: flex; gap: 8px; align-items: center;">
                            <input type="text" name="vvc_items[__idx__][name]" class="form-control vvc-name" value="" style="width: 50%;" placeholder="Description">
                            <input type="number" step="0.01" name="vvc_items[__idx__][amount]" class="form-control vvc-amount" value="" style="width: 30%;" placeholder="Amount">
                            <button type="button" class="btn btn-danger btn-xs remove-vvc-item"><i class="fa fa-minus"></i></button>
                        </div>
                        <button type="button" class="btn btn-success btn-xs" id="add-vvc-item" style="margin-top: 8px;"><i class="fa fa-plus"></i> Add Cost Item</button>
                        <div style="margin-top: 8px; font-weight: 600;">
                            Total: <span id="vvc-total">0.00</span>
                        </div>
                        <input type="hidden" name="vetted_valuation_cost" id="vetted_valuation_cost_input" value="{{ old('vetted_valuation_cost', $collateral->vetted_valuation_cost) }}">
                        <small class="form-help-text">Cost spent to acquire the collateral (leave empty if no amount was spent)</small>
                        {!! $errors->first('vetted_valuation_cost', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
             </div>
             <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save</button>
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
            var vvcIndex = @json(count(old('vvc_items', $collateral->vvc_items ?? [])));

            $('#add-vvc-item').click(function() {
                var row = $('.vvc-item-row.d-none').clone();
                var newName = 'vvc_items[' + vvcIndex + '][name]';
                var newAmount = 'vvc_items[' + vvcIndex + '][amount]';
                row.find('.vvc-name').attr('name', newName).val('');
                row.find('.vvc-amount').attr('name', newAmount).val('');
                row.removeClass('d-none');
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

            @if($collateral->vetted_valuation_by)
            $('#vetted-valuation-by').prop('disabled', true);
            @endif
        });
    </script>
@endsection

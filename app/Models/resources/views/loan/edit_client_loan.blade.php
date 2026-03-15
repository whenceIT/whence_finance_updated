@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.loan',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.loan',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/client_loan/'.$loan->id.'/update')}}"
              class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="loan_officer_id"
                           class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The financial institution representative who has responsibility for, and interacts with, the client/group associated with a loan account"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="loan_officer_id" class="form-control select2" id="loan_officer_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($loan->loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <label for="loan_purpose_id"
                           class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="Provides an indication of how the funds provided through the loan will be directed and can be used to group loans with the same purpose for reporting"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="loan_purpose_id" class="form-control select2" id="loan_purpose_id">
                            <option></option>
                            @foreach(\App\Models\LoanPurpose::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($loan->loan_purpose_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group hidden">
                    <label for="fund_id"
                           class="control-label col-md-2">{{trans_choice('general.fund',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="The original source of your funds (for example a grant)."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="fund_id" class="form-control select2" id="fund_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                             <option value="{{$key->id}}">{{$key->name}}</option>
                         @endforeach
                        </select>
                    </div>
                    <label for="created_date"
                           class="control-label col-md-2">{{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="The date the loan account application was received"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="created_date" class="form-control date-picker"
                               value="{{$loan->created_date}}"
                               required id="created_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="external_id"
                           class="control-label col-md-2">{{trans_choice('general.external_id',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="external_id" class="form-control"
                               value="{{$loan->client->external_id}}"
                               id="external_id">
                    </div>
                </div>
                <div class="form-group">
                    <label for="principal"
                           class="control-label col-md-2">{{trans_choice('general.principal',1)}}
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="principal" class="form-control"
                               min="{{$loan->loan_product->minimum_principal}}"
                               max="{{$loan->loan_product->maximum_principal}}"
                               value="{{$loan->applied_amount}}"
                               required id="principal">
                    </div>
                    <label for="loan_term"
                           class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="loan_term" class="form-control"
                               min="{{$loan->loan_product->minimum_loan_term}}"
                               max="{{$loan->loan_product->maximum_loan_term}}"
                               value="{{$loan->loan_term}}"
                               required id="loan_term">
                    </div>
                    <div class="col-md-2">
                        <select name="loan_term_type" class="form-control " id="loan_term_type"
                                required>
                            <option value="days"
                                    @if($loan->loan_term_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan->loan_term_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan->loan_term_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                            <option value="years"
                                    @if($loan->loan_term_type=="years") selected @endif>{{trans_choice('general.year',2)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="repayment_frequency"
                           class="control-label col-md-2">{{trans_choice('general.repayment',1)}}
                        {{trans_choice('general.every',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="repayment_frequency" class="form-control" min="0"
                               value="{{$loan->repayment_frequency}}"
                               required id="repayment_frequency">
                    </div>
                    <div class="col-md-2">
                        <select name="repayment_frequency_type" class="form-control " id="repayment_frequency_type"
                                required>
                            <option value="days"
                                    @if($loan->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="override_interest"
                           class="control-label col-md-2">{{trans_choice('general.override',1)}} {{trans_choice('general.interest',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="Set yes if you want the system to use this interest per period for calculation"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="override_interest" class="form-control select2" id="override_interest">
                            <option value="0" @if($loan->override_interest==0) selected @endif>{{trans_choice('general.no',1)}}</option>
                            <option value="1" @if($loan->override_interest==1) selected @endif>{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>
                    <div id="override_interest_rate_div" style="display: none">
                        <label for="override_interest_rate"
                               class="control-label col-md-2">{{trans_choice('general.override',1)}} {{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                        </label>
                        <div class="col-md-2">
                            <input type="number" name="override_interest_rate" class="form-control"
                                   value="{{$loan->override_interest_rate}}"
                                   id="override_interest_rate">
                        </div>
                    </div>
                </div>
                <div class="form-group" id="interest_rate_div">
                    <label for="interest_rate"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="interest_rate" class="form-control"
                               min="{{$loan->loan_product->minimum_interest_rate}}"
                               max="{{$loan->loan_product->maximum_interest_rate}}"
                               value="{{$loan->interest_rate}}"
                               required id="interest_rate">
                    </div>
                  <!-- Interest Rate Type Dropdown -->
<label for="interest_rate_type" class="control-label col-md-2 text-left">
    % {{ trans_choice('general.per', 1) }}
</label>
<div class="col-md-2">
    <select name="interest_rate_type" id="interest_rate_type" class="form-control" required>
        <option value="day" @if(old('interest_rate_type', $loan->interest_rate_type) == 'day') selected @endif>
            {{ trans_choice('general.day', 1) }}
        </option>
        <option value="week" @if(old('interest_rate_type', $loan->interest_rate_type) == 'week') selected @endif>
            {{ trans_choice('general.week', 1) }}
        </option>
        <option value="month" @if(old('interest_rate_type', $loan->interest_rate_type) == 'month') selected @endif>
            {{ trans_choice('general.month', 1) }}
        </option>
        <option value="year" @if(old('interest_rate_type', $loan->interest_rate_type) == 'year') selected @endif>
            {{ trans_choice('general.year', 1) }}
        </option>
    </select>
</div>


                </div>
                <div class="form-group">
                    <label for="expected_disbursement_date"
                           class="control-label col-md-2">{{trans_choice('general.disbursement',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The date that the loan account is expected to be disbursed"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="expected_disbursement_date" class="form-control date-picker"
                               value="{{$loan->expected_disbursement_date}}"
                               required id="expected_disbursement_date">
                    </div>
                    <label for="expected_first_repayment_date"
                           class="control-label col-md-2">{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="May be entered to override the date the system would schedule"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="expected_first_repayment_date" class="form-control date-picker"
                               value="{{$loan->expected_first_repayment_date}}"
                               id="expected_first_repayment_date">
                    </div>

                </div>

                <h3>{{trans_choice('general.charge',2)}}</h3>
                <hr>
                <div class="form-group">
                    <label for="charges_dropdown"
                           class="control-label col-md-2">{{trans_choice('general.charge',1)}}</label>
                    <div class="col-md-3">
                        <select name="charges_dropdown" class="form-control select2" id="charges_dropdown">
                            <option></option>
                            @foreach(\App\Models\Charge::all() as $key) 
                               
                            <option value="{{$key->id}}">{{$key->name}}</option>
                               
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="add_charge"
                                class="btn btn-info">{{trans_choice('general.add',1)}}</button>
                    </div>
                </div>
                <div class="row" id="charges_div">
                    <div class="col-md-12">
                        <div style="display: none;" id="saved_charges">
                        </div>
                        <table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ trans_choice('general.name', 1) }}</th>
            <th style="display: none;">{{ trans_choice('general.type', 1) }}</th>
            <th style="display: none;">{{ trans_choice('general.amount', 1) }}</th>
            <th>Computed Amount</th>
            <th style="display: none;">{{ trans_choice('general.collected', 1) }} {{ trans_choice('general.on', 1) }}</th>
            <th style="display: none;">{{ trans_choice('general.date', 1) }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="charges_table">
    @foreach ($loan->charges as $loanCharge)
        @php
            $charge = $loanCharge->charge;
            $computed_amount = $loanCharge->amount;
        @endphp
        @if (!empty($charge))
            <tr id="row{{ $charge->id }}" data-charge-type="{{ $charge->charge_type }}">
                <!-- Name Column -->
                <td>{{ $charge->name }}</td>

                <!-- Hidden Charge ID -->
                <td style="display: none;">
                    <input type="hidden" name="charges[]" value="{{ $charge->id }}">
                </td>

                <!-- Hidden Type -->
                <td style="display: none;">
                    {{ trans_choice('general.' . $charge->charge_option, 1) }}
                </td>

                <!-- Computed Amount -->
                <td>
                    @if ($charge->override == 1)
                        <input type="number" step="0.01"
                               class="form-control computed_amount_input editable"
                               name="charge_amount[{{ $charge->id }}]"
                               value="{{ number_format($computed_amount, 2, '.', '') }}"
                               required>
                    @else
                        <input type="hidden" name="charge_amount[{{ $charge->id }}]" value="{{ $computed_amount }}">
                        <input type="text" class="form-control computed_amount"
                               value="{{ number_format($computed_amount, 2) }}" readonly>
                    @endif
                </td>

                <!-- Hidden Date Field -->
                <td style="display: none;">
                    @if ($charge->charge_type === 'specified_due_date')
                        <input type="date" name="charge_date[{{ $charge->id }}]"
                               class="form-control"
                               value="{{ $loanCharge->collected_on_date }}">
                    @else
                        <input type="hidden" name="charge_date[{{ $charge->id }}]" value="">
                    @endif
                </td>

                <!-- Action -->
                <td>
                    <button type="button" class="btn btn-danger btn-xs" onclick="delete_charge(this)" data-id="{{ $charge->id }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @endif
    @endforeach
</tbody>


</table>

                    </div>
                </div>
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','loans')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name}} @endif</textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="select")
                                    <select class="form-control touchspin" name="custom_field_{{$key->id}}"
                                            @if($key->required==1) required @endif>
                                        @if($key->required!=1)
                                            <option value=""></option>
                                        @else
                                            <option value="" disabled selected>Select...</option>
                                        @endif
                                        @foreach(explode(',',$key->select_values) as $v)
                                            @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()))
                                                @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name==$v)
                                                    <option selected>{{$v}}</option>
                                                @else
                                                    <option>{{$v}}</option>
                                                @endif
                                            @else
                                                <option>{{$v}}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()))
                                                    @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()->name==$v)
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif checked>
                                                    @else
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                @else
                                                    <input type="radio" name="custom_field_{{$key->id}}"
                                                           id="{{$key->id}}" value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                @endif

                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
                                    @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan->id)->where('category','loans')->first()))
                                        <?php $c = unserialize(\App\Models\CustomFieldMeta::where('custom_field_id',
                                            $key->id)->where('parent_id', $loan->id)->where('category',
                                            'loans')->first()->name); ?>

                                        @foreach(explode(',',$key->checkbox_values) as $v)
                                            <div class="checkbox">
                                                <label>
                                                    @if(array_key_exists($v,$c))
                                                        @if($c[$v]==$v)
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif checked>
                                                        @else
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif>
                                                        @endif
                                                    @else
                                                        <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                               id="{{$key->id}}"
                                                               value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach(explode(',',$key->checkbox_values) as $v)
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                           id="{{$key->id}}"
                                                           value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#currency_id').change(function (e) {
            var id = $('#currency_id').val();
            var url = "{!!  url('loan/product')  !!}/" + id + "/get_currency_charges";
            var items = "";
            items += "<option></option>";
            $.getJSON(url, function (data) {
                $.each(data, function (index, item) {
                    items += "<option value='" + item.id + "'>" + item.name + "</option>";
                });
                $("#charges_dropdown").html(items);
            });
        });
        $('#add_charge').click(function () {
    var id = $('#charges_dropdown').val();
    var principal = parseFloat($('#principal').val()) || 0;

    if (id === "") {
        alert("Please select a charge item");
        return;
    }

    if ($('#row' + id).length > 0) {
        alert("This charge is already added.");
        return;
    }

    $.ajax({
        type: 'GET',
        url: "{{ url('loan/product') }}/" + id + "/get_charge_detail",
        dataType: "json",
        success: function (data) {
            var computed_amount = 0;

            // Calculate based on charge option
            if (data.charge_option.toLowerCase() === "flat") {
                computed_amount = parseFloat(data.amount).toFixed(2);
            } else {
                computed_amount = ((parseFloat(data.amount) * principal) / 100).toFixed(2);
            }

            // Construct charge row
            var row = `
                <tr id="row${id}" data-charge-type="${data.charge_type}">
                    <td>${data.name}</td>
                    <td style="display: none;">${data.charge_option}</td>
                    <td>
                        <input type="hidden" name="charges[]" value="${id}">
                        ${
                            data.name.toLowerCase() === "ddac"
                                ? `<input type="number" class="form-control computed_amount_input editable" name="charge_amount[${data.id}]" value="${computed_amount}" step="any" required>`
                                : `<input type="hidden" name="charge_amount[${data.id}]" value="${computed_amount}">
                                   <input type="text" class="form-control computed_amount" value="${computed_amount}" readonly>`
                        }
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs" data-id="${id}" onclick="delete_charge(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#charges_table').append(row);

            // If editable, recalculate when changed
            if (data.name.toLowerCase() === "ddac") {
                $('#row' + id + ' .editable').on('input', function () {
                    calculateTotalLoanPayout();
                });
            }

            calculateTotalLoanPayout();
        },
        error: function () {
            alert("An error occurred, please try again.");
        }
    });
});

// 🔁 Recalculate charges when principal changes
$('#principal').on('input', function () {
    recalculateCharges();
    calculateTotalLoanPayout();
});

// 🔄 Recalculate editable charges (like DDAC)
function recalculateCharges() {
    var principal = parseFloat($('#principal').val()) || 0;

    $('.computed_amount_input').each(function () {
        var $row = $(this).closest('tr');
        var chargeOption = $row.find('td:eq(1)').text().trim().toLowerCase();
        var chargeBase = parseFloat($(this).attr('data-base')) || parseFloat($(this).val()) || 0;

        var updatedAmount = 0;
        if (chargeOption === "flat") {
            updatedAmount = chargeBase.toFixed(2);
        } else {
            updatedAmount = ((chargeBase * principal) / 100).toFixed(2);
        }

        $(this).val(updatedAmount);
    });
}

// 💵 Calculate net disbursable loan amount
function calculateTotalLoanPayout() {
    var principal = parseFloat($('#principal').val()) || 0;
    var totalCharges = 0;

    $('.computed_amount, .editable').each(function () {
        var chargeAmount = parseFloat($(this).val()) || 0;
        var chargeType = $(this).closest('tr').data('charge-type');

        if (chargeType === "disbursement") {
            totalCharges += chargeAmount;
        }
    });

    var totalLoanPayout = principal - totalCharges;
    $('#total_loan_payout').val(totalLoanPayout.toFixed(2));
}

// 🗑️ Remove charge row
function delete_charge(button) {
    var id = $(button).data('id');
    $('#row' + id).remove();
    calculateTotalLoanPayout();
}

        function delete_charge(e) {
            swal({
                title: 'Are you sure?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok',
                cancelButtonText: 'Cancel'
            }).then(function () {
                $('#charge' + $(e).attr("data-id")).remove();
                $('#row' + $(e).attr("data-id")).remove();

            })
        }
        if ($("#override_interest").val() == 0) {
            $("#override_interest_rate_div").hide();
            $("#interest_rate_div").show();
            $("#override_interest_rate").removeAttr("required");
            $("#interest_rate").attr("required","required");
        }else{
            $("#override_interest_rate_div").show();
            $("#override_interest_rate").attr("required","required");
            $("#interest_rate").removeAttr("required");
            $("#interest_rate_div").hide();
        }
        $("#override_interest").change(function () {
            if ($("#override_interest").val() == 0) {
                $("#override_interest_rate_div").hide();
                $("#interest_rate_div").show();
                $("#override_interest_rate").removeAttr("required");
                $("#interest_rate").attr("required","required");
            }else{
                $("#override_interest_rate_div").show();
                $("#override_interest_rate").attr("required","required");
                $("#interest_rate").removeAttr("required");
                $("#interest_rate_div").hide();
            }
        })
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }
            }, highlight: function (element) {
                $(element).closest('.form-group div').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group div').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>

    
@endsection
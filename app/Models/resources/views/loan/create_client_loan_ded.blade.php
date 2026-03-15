@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
@endsection
@section('content')
<style>
    /* Center the entire div */
    .payout-wrapper {
        display: flex;
        flex-direction: column;   /* Stack label and input vertically */
        align-items: center;      /* Center horizontally */
        justify-content: center;  /* Center vertically */
        margin-top: 20px;         /* Add space from top */
    }

    /* Style for the Total Loan Payout input */
    .highlighted-payout {
        font-size: 28px;          /* Bigger text */
        font-weight: bold;        /* Bold text */
        color: white;             /* Green text */
        background-color: #800000;/* Light green background */
        text-align: center;       /* Center text inside the input */
        border: 2px solid maroon;  /* Green border */
        padding: 10px;            /* Padding inside the input */
        width: 300px;             /* Set width */
        border-radius: 5px;       /* Rounded corners */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    }

    /* Style the label */
    .payout-wrapper label {
        font-size: 24px;          /* Bigger label */
        font-weight: bold;        /* Bold label */
        color: maroon;             /* Green label */
        margin-bottom: 10px;      /* Space between label and input */
    }
</style>



    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/create_client_loan/'.$client->id.'/'.$loan_product->id.'/store')}}"
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
                    <input type="text" name="loan_officer_id" class="form-control"
                     value="{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}"
                               required id="loan_officer_id" readonly>
                        
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
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
          
                    <label for="created_date"
                           class="control-label col-md-2">{{trans_choice('general.application',1)}} {{trans_choice('general.date',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="The date the loan account application was received"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="created_date_display" id="created_date_display"
       class="form-control date-picker"
       value="{{ session('loan_defaults.expected_disbursement_date') }}" required>

<input type="hidden" name="created_date" id="created_date" value="{{ date('Y-m-d') }}">

                    </div>
                </div>
                {{-- Example: show carried balance --}}
@if($carried_balance > 0)
    <div class="alert alert-info">
        <strong>Note:</strong> This loan is a deduction of <strong>ZMW {{ number_format($carried_balance, 2) }}</strong> from Loan ID: {{ $previous_loan_id }}.
    </div>
@endif              
                <div class="form-group">
                    <label for="principal"
                           class="control-label col-md-2">{{trans_choice('general.principal',1)}}
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="principal" class="form-control"
                               min="{{$loan_product->minimum_principal}}" max="{{$loan_product->maximum_principal}}"
                               value="{{ session('loan_defaults.principal') }}"
                               required id="principal">
                    </div>
                    <label for="loan_term"
                           class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="loan_term" class="form-control"
                               min="{{$loan_product->minimum_loan_term}}" max="{{$loan_product->maximum_loan_term}}"
                               value="{{ session('loan_defaults.loan_term') }}"
                               required id="loan_term">
                    </div>
                    <div class="col-md-2">
                    <select name="loan_term_type" class="form-control" id="loan_term_type" required readonly>
    <option value="months" selected>{{trans_choice('general.month',2)}}</option>
    <option value="days" disabled @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
    <option value="weeks" disabled @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
    <option value="years" disabled @if($loan_product->repayment_frequency_type=="years") selected @endif>{{trans_choice('general.year',2)}}</option>
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
                               value="{{$loan_product->repayment_frequency}}"
                               required id="repayment_frequency" readonly>
                    </div>
                    <div class="col-md-2">
                        <select name="repayment_frequency_type" class="form-control " id="repayment_frequency_type" readonly
                                required>
                            <option value="days"
                                    @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan_product->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
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
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>
                    <div id="override_interest_rate_div" style="display: none">
                        <label for="override_interest_rate"
                               class="control-label col-md-2">{{trans_choice('general.override',1)}} {{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                        </label>
                        <div class="col-md-2">
                            <input type="number" name="override_interest_rate" class="form-control"
                                   value="{{$loan_product->default_interest_rate}}"
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
                               min="{{$loan_product->minimum_interest_rate}}"
                               max="{{$loan_product->maximum_interest_rate}}"
                               value="{{ session('loan_defaults.interest_rate') }}"
                               required id="interest_rate" step="any">
                    </div>
                      <!-- Interest Rate Type Dropdown -->
    <label for="interest_rate_type" class="control-label col-md-2 text-left">
        % {{ trans_choice('general.per', 1) }}
    </label>
    <div class="col-md-2">
        <select name="interest_rate_type" id="interest_rate_type" class="form-control" required>
            <option value="day" @if(old('interest_rate_type', $loan_product->interest_rate_type) == 'day') selected @endif>
                {{ trans_choice('general.day', 1) }}
            </option>
            <option value="week" @if(old('interest_rate_type', $loan_product->interest_rate_type) == 'week') selected @endif>
                {{ trans_choice('general.week', 1) }}
            </option>
            <option value="month" @if(old('interest_rate_type', $loan_product->interest_rate_type) == 'month') selected @endif>
                {{ trans_choice('general.month', 1) }}
            </option>
            <option value="year" @if(old('interest_rate_type', $loan_product->interest_rate_type) == 'year') selected @endif>
                {{ trans_choice('general.year', 1) }}
            </option>
        </select>
    </div>
</div>

                
                @php
    $loanDefaults = session('loan_defaults', []);
@endphp

<div class="form-group">
    <label for="expected_disbursement_date"
           class="control-label col-md-2">{{ trans_choice('general.disbursement', 1) }} {{ trans_choice('general.on', 1) }}
        <i class="fa fa-question-circle" data-toggle="tooltip"
           data-title="The date that the loan account is expected to be disbursed"></i>
    </label>
    <div class="col-md-3">
        <input type="text" name="expected_disbursement_date" class="form-control date-picker"
               id="expected_disbursement_date"
               value="{{ old('expected_disbursement_date', $loanDefaults['expected_disbursement_date'] ?? date('Y-m-d')) }}"
               onchange="calculateDate()">
    </div>

    <label for="expected_first_repayment_date"
           class="control-label col-md-2">{{ trans_choice('general.first', 1) }} {{ trans_choice('general.repayment', 1) }} {{ trans_choice('general.on', 1) }}
        <i class="fa fa-question-circle" data-toggle="tooltip"
           data-title="May be entered to override the date the system would schedule"></i>
    </label>
    <div class="col-md-3">
        <input type="text" name="expected_first_repayment_date" class="form-control date-picker"
               id="expected_first_repayment_date"
               value="{{ old('expected_first_repayment_date', $loanDefaults['expected_first_repayment_date'] ?? '') }}">
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
        @foreach (\App\Models\Charge::all() as $key)
            @if (!empty($key->charge))
                <!-- Table Row -->
                <tr id="row{{ $key->id }}" data-charge-type="{{ $key->charge_type }}">
                    <!-- Name Column -->
                    <td>{{ $key->name }}</td>
                    <td>
        <!-- Add hidden input to submit charge ID -->
        <input type="hidden" name="charges[]" value="{{ $key->id }}">
    </td>
                    <td>{{ $key->charge_type }}</td>

                    <!-- Type Column (Hidden) -->
                    <td style="display: none;">
                        @if ($key->charge->charge_option == "flat")
                            {{ trans_choice('general.flat', 1) }}
                        @elseif ($key->charge->charge_option == "percentage")
                            % {{ trans_choice('general.percentage', 1) }}
                        @endif
                    </td>

                    <!-- Amount Column (Editable for 'insurance') -->
                    <td>
                        @php
                            $computed_amount = $key->charge->charge_option === "flat" 
                                ? $key->amount 
                                : ($key->amount * $loan->applied_amount) / 100;
                        @endphp

                        @if (strtolower($key->name) === "DDAC")
                            <input type="number" class="form-control computed_amount_input editable" name="charge_amount[{{ $key->charge->id }}]" value="{{ number_format($computed_amount, 2) }}" required>
                        @else
                            <input type="hidden" name="charge_amount[{{ $key->charge->id }}]" value="{{ $computed_amount }}">
                            <input type="text" class="form-control computed_amount" value="{{ number_format($computed_amount, 2) }}" readonly>
                        @endif
                    </td>

                    <!-- Actions Column -->
                    <td>
                        <button type="button" class="btn btn-danger btn-xs" onclick="delete_charge(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>


<!-- Total Loan Payout Display -->
<!-- Fully Centered Total Loan Payout Display -->
<div class="hidden">
    <label for="total_loan_payout">Total Loan Payout</label>
    <input type="text" 
           id="total_loan_payout" 
           class="highlighted-payout" 
           value="0.00" 
           readonly>
</div>



                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','loans')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif></textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
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
                                            <option>{{$v}}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}"
                                                       value="{{$v}}"
                                                       @if($key->required==1) required @endif>
                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
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
                            </div>
                        </div>
                    @endforeach
                @endif
            
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
    </div>
@endsection
@section('footer-scripts')
<script>
// ✅ Calculate the next repayment date
function calculateDate() {
    var selectedDate = new Date(document.getElementById("expected_disbursement_date").value);

    if (!isNaN(selectedDate.getTime())) {
        var newDate = new Date(selectedDate);
        newDate.setDate(selectedDate.getDate() + 30);

        var formattedDate = newDate.toISOString().split('T')[0];
        document.getElementById("expected_first_repayment_date").value = formattedDate;
    } else {
        alert("Please select a valid date.");
    }
}

// ✅ Update charges dropdown based on currency
$('#currency_id').change(function (e) {
    var id = $('#currency_id').val();
    var url = "{!!  url('loan/product')  !!}/" + id + "/get_currency_charges";
    var items = "<option></option>";

    $.getJSON(url, function (data) {
        $.each(data, function (index, item) {
            items += "<option value='" + item.id + "'>" + item.name + "</option>";
        });
        $("#charges_dropdown").html(items);
    });
});

$('#add_charge').click(function () {
    if ($('#charges_dropdown').val() === "") {
        alert("Please select a charge item");
        return;
    }

    var id = $('#charges_dropdown').val();
    var principal = parseFloat($('#principal').val()) || 0;

    // Fetch charge details via AJAX
    $.ajax({
        type: 'GET',
        url: "{{ url('loan/product/') }}" + "/" + id + "/get_charge_detail",
        dataType: "json",
        success: function (data) {
            var computed_amount = 0;

            // Check charge type and calculate the computed amount
            if (data.charge_option.toLowerCase() === "flat") {
                computed_amount = parseFloat(data.amount).toFixed(2);
            } else {
                computed_amount = ((parseFloat(data.amount) * principal) / 100).toFixed(2);
            }

            // Prevent duplicate rows
            if ($('#row' + id).length > 0) {
                alert("This charge is already added.");
                return;
            }

            // Create a new row
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

            // Append the row to the table
            $('#charges_table').append(row);

            // Add event listener for editable insurance fields
            if (data.name.toLowerCase() === "ddac") {
                $('#row' + id + ' .editable').on('input', function () {
                    calculateTotalLoanPayout();
                });
            }

            // Recalculate totals
            calculateTotalLoanPayout();
        },
        error: function () {
            alert("An error occurred, please try again.");
        }
    });
});






// 🔥 Recalculate Total Loan Payout when Principal changes
$('#principal').on('input', function () {
    recalculateCharges();
    calculateTotalLoanPayout();
});

// Recalculate individual charges
function recalculateCharges() {
    var principal = parseFloat($('#principal').val()) || 0;

    $('.computed_amount_input').each(function () {
        var chargeOption = $(this).closest('tr').find('td:eq(1)').text().trim().toLowerCase();
        var chargeValue = parseFloat($(this).val()) || 0;

        var updatedAmount = 0;

        if (chargeOption === "flat") {
            updatedAmount = chargeValue.toFixed(2); // Flat charges remain fixed
        } else {
            updatedAmount = ((chargeValue * principal) / 100).toFixed(2); // Percentage charges based on principal
        }

        $(this).val(updatedAmount);
    });
}

// Recalculate Total Loan Payout
function calculateTotalLoanPayout() {
    var principal = parseFloat($('#principal').val()) || 0;
    var totalCharges = 0;

    // Loop through charges and sum amounts
    $('.computed_amount, .editable').each(function () {
        var chargeAmount = parseFloat($(this).val()) || 0;
        var chargeType = $(this).closest('tr').data('charge-type');

        // Include only charges with chargeType = 'disbursement'
        if (chargeType === "disbursement") {
            totalCharges += chargeAmount;
        }
    });

    // Calculate total payout
    var totalLoanPayout = principal - totalCharges;

    // Update the Total Loan Payout field
    $('#total_loan_payout').val(totalLoanPayout.toFixed(2));
}

// Recalculate charges when principal changes
$('#principal').on('input', function () {
    recalculateCharges();
    calculateTotalLoanPayout();
});



// ✅ Delete charge and recalculate total payout
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

        // 🔥 Recalculate after deletion
        calculateTotalLoanPayout();
    });
}
</script>

    
@endsection
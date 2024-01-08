@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/create_group_loan/'.$group->id.'/'.$loan_product->id.'/store')}}"
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
                                            @if($group->staff_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
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
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fund_id"
                           class="control-label col-md-2">{{trans_choice('general.fund',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="The original source of your funds (for example a grant)."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="fund_id" class="form-control select2" id="fund_id">
                            <option></option>
                            @foreach(\App\Models\Fund::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($loan_product->fund_id==$key->id) selected @endif>{{$key->name}}</option>
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
                               value="{{date("Y-m-d")}}"
                               required id="created_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="external_id"
                           class="control-label col-md-2">{{trans_choice('general.external_id',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="external_id" class="form-control"
                               value="{{old('external_id')}}"
                               id="external_id">
                    </div>
                </div>
                <div class="form-group">
                    <label for="principal"
                           class="control-label col-md-2">{{trans_choice('general.principal',1)}}
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="principal" class="form-control"
                               min="{{$loan_product->minimum_principal}}" max="{{$loan_product->maximum_principal}}"
                               value="{{$loan_product->default_principal}}"
                               required id="principal">
                    </div>
                    <label for="loan_term"
                           class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}
                    </label>
                    <div class="col-md-2">
                        <!-- <input type="number" name="loan_term" class="form-control"
                               min="{{$loan_product->minimum_loan_term}}" max="{{$loan_product->maximum_loan_term}}"
                               value="{{$loan_product->default_loan_term}}"
                               required id="loan_term"> -->

                        <input type="number" name="loan_term" class="form-control"
                               min=1 max=1
                               value=1
                               required id="loan_term">
                    </div>
                    <div class="col-md-2">
                        <select name="loan_term_type" class="form-control " id="loan_term_type"
                                required>
                            <!-- <option value="days"
                                    @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan_product->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                            <option value="years"
                                    @if($loan_product->repayment_frequency_type=="years") selected @endif>{{trans_choice('general.year',2)}}</option> -->
                            <option value="months"
                                    @if($loan_product->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="repayment_frequency"
                           class="control-label col-md-2">{{trans_choice('general.repayment',1)}}
                        {{trans_choice('general.every',1)}}
                    </label>
                    <div class="col-md-2">
                        <!-- <input type="number" name="repayment_frequency" class="form-control" min="0"
                               value="{{$loan_product->repayment_frequency}}"
                               required id="repayment_frequency"> -->

                        <input type="number" name="repayment_frequency" class="form-control" min=1 max=1
                                value=1
                               required id="repayment_frequency">
                    </div>
                    <div class="col-md-2">
                        <select name="repayment_frequency_type" class="form-control " id="repayment_frequency_type"
                                required>
                            <!-- <option value="days"
                                    @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option> -->
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
                               value="{{$loan_product->default_interest_rate}}"
                               required id="interest_rate">
                    </div>
                    <label for="interest_rate_type"
                           class="control-label col-md-2 text-left">% {{trans_choice('general.per',1)}}
                        @if($loan_product->interest_rate_type=="month")
                            {{trans_choice('general.month',1)}}
                        @endif
                        @if($loan_product->interest_rate_type=="year")
                            {{trans_choice('general.year',1)}}
                        @endif
                    </label>

                </div>
                <div class="form-group">
                    <label for="expected_disbursement_date"
                           class="control-label col-md-2">{{trans_choice('general.disbursement',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The date that the loan account is expected to be disbursed"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="expected_disbursement_date" class="form-control date-picker"
                               placeholder=""
                               required id="expected_disbursement_date">
                    </div>
                    <label for="expected_first_repayment_date"
                           class="control-label col-md-2">{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="May be entered to override the date the system would schedule"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="expected_first_repayment_date" class="form-control date-picker"
                               placeholder="" required
                               id="expected_first_repayment_date">
                    </div>

                </div>
                <h3>{{trans_choice('general.group',1)}} {{trans_choice('general.member',1)}} {{trans_choice('general.allocation',1)}}</h3>
                @foreach($group->clients as $key)
                    <div class="form-group">
                        <label for="client[{{$key->client_id}}]"
                               class="control-label col-md-2">
                            @if(!empty($key->client))
                                @if($key->client->client_type=="individual")
                                    {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                @else
                                    {{$key->client->full_name}}
                                @endif
                            @endif
                        </label>
                        <div class="col-md-3">
                            <input type="number" name="client[{{$key->client_id}}]" class="form-control client_amount"
                                   placeholder=""
                                   required id="client_{{$key->id}}" onblur="update_client_total(this)">
                        </div>
                    </div>
                @endforeach
                <div class="form-group">
                    <label for="client_total_amount"
                           class="control-label col-md-2">
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="client_total_amount" class="form-control"
                               required id="client_total_amount" min="{{$loan_product->default_principal}}"
                               max="{{$loan_product->default_principal}}" readonly>
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
                            @foreach(\App\Models\LoanProductCharge::where('loan_product_id',$loan_product->id)->get() as $key)
                                @if(!empty($key->charge))
                                    @if($key->charge->charge_type=="specified_due_date")
                                        <option value="{{$key->charge_id}}">{{$key->charge->name}}</option>
                                    @endif
                                @endif
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
                                <th>{{trans_choice('general.name',1)}}</th>
                                <th>{{trans_choice('general.type',1)}}</th>
                                <th>{{trans_choice('general.amount',1)}}</th>
                                <th>{{trans_choice('general.collected',1)}} {{trans_choice('general.on',1)}}</th>
                                <th>{{trans_choice('general.date',1)}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="charges_table">
                            @foreach($loan_product->charges as $key)
                                @if(!empty($key->charge))
                                    @if($key->charge->charge_type=="disbursement" || $key->charge->charge_type=="installment_fee")
                                        <input type="hidden" name="charges[]" id="charge{{$key->charge_id}}"
                                               value="{{$key->charge_id}}">
                                        <tr id="row{{$key->charge->id}}">
                                            <td>{{ $key->charge->name }}</td>
                                            <td>
                                                @if($key->charge->charge_option=="flat")
                                                    {{trans_choice('general.flat',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="installment_principal_due")
                                                    % {{trans_choice('general.installment_principal_due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="installment_principal_interest_due")
                                                    % {{trans_choice('general.installment_principal_interest_due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="installment_interest_due")
                                                    % {{trans_choice('general.installment_interest_due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="total_due")
                                                    % {{trans_choice('general.total_due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="original_principal")
                                                    % {{trans_choice('general.original_principal',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="percentage")
                                                    % {{trans_choice('general.percentage',1)}} {{trans_choice('general.of',1)}} {{trans_choice('general.amount',1)}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->override==1)
                                                    <input type="number" class="form-control"
                                                           name="charge_amount[{{$key->charge->id}}]"
                                                           value="{{$key->charge->amount}}" required>
                                                @else
                                                    <input type="hidden" class="form-control"
                                                           name="charge_amount[{{$key->charge->id}}]"
                                                           value="{{$key->charge->amount}}">
                                                    {{$key->charge->amount}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->charge_type=='disbursement')
                                                    {{trans_choice('general.disbursement',1)}}
                                                @endif
                                                @if($key->charge->charge_type=='specified_due_date')
                                                    {{trans_choice('general.specified_due_date',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='installment_fee')
                                                    {{trans_choice('general.installment_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='overdue_installment_fee')
                                                    {{trans_choice('general.overdue_installment_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='loan_rescheduling_fee')
                                                    {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='overdue_maturity')
                                                    {{trans_choice('general.overdue_maturity',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='savings_activation')
                                                    {{trans_choice('general.savings_activation',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='withdrawal_fee')
                                                    {{trans_choice('general.withdrawal_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='monthly_fee')
                                                    {{trans_choice('general.monthly_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='annual_fee')
                                                    {{trans_choice('general.annual_fee',2)}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->charge_type=='specified_due_date')
                                                    <input type="text" class="form-control date-picker"
                                                           name="charge_date[{{$key->charge->id}}]"
                                                           value="">
                                                @else
                                                    <input type="hidden" class="form-control"
                                                           name="charge_date[{{$key->charge->id}}]"
                                                           value="">
                                                @endif
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endif
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
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}" value="{{$v}}"
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
                                                <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]" id="{{$key->id}}"
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
        function update_client_total(e) {
            var principal = $("#principal").val();
            var amount = parseInt($(e).val());
            var total = 0;
            $(".client_amount").each(function () {
                total = total + parseInt($(this).val());
            })
            $("#client_total_amount").val(total)
        }
        $("#principal").blur(function (e) {
            $("#client_total_amount").attr('min', $("#principal").val()).attr('max', $("#principal").val());
        });
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
        $('#add_charge').click(function (e) {
            if ($('#charges_dropdown').val() == "") {
                alert("Please select an item")
            } else {
                //try to build table
                var id = $('#charges_dropdown').val();
                $.ajax({
                    type: 'GET',
                    url: "{{url('loan/product/')}}" + "/" + id + "/get_charge_detail",
                    dataType: "json",
                    success: function (data) {
                        var to_append = '<tr id="row' + id + '"><td>' + data.name + '</td><td>' + data.charge_option + '</td>';
                        if (data.override == "1") {
                            to_append = to_append + '<td> <input type="number" class="form-control" name="charge_amount[' + data.id + ']" value="' + data.amount + '" required></td>';
                        } else {
                            to_append = to_append + '<td> <input type="hidden" class="form-control" name="charge_amount[' + data.id + ']" value="' + data.amount + '" >' + data.amount + '</td>';
                        }
                        to_append = to_append + '<td>' + data.collected_on + '</td>';

                        to_append = to_append + '<td> <input type="text" class="form-control date-picker" name="charge_date[' + data.id + ']" value="" required></td>';
                        to_append = to_append + '<td><button type="button" class="btn btn-danger btn-xs" data-id="' + id + '" onclick="delete_charge(this)"><i class="fa fa-trash"></i></button></td>';
                        $('#charges_table').append(to_append);
                        $('#saved_charges').append('<input name="charges[]" id="charge' + id + '" value="' + id + '">');
                    },
                    error: function (data) {
                        swal({
                            title: 'Error',
                            text: 'An Error occurred, please try again',
                            type: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            timer: 2000
                        })
                    }
                });
            }
        });
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
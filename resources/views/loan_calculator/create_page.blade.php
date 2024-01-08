@extends('layouts.master')
@section('title')
    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/calculator/'.$loan_product->id.'/show')}}"
              class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">

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
                        <input type="number" name="loan_term" class="form-control"
                               min="{{$loan_product->minimum_loan_term}}" max="{{$loan_product->maximum_loan_term}}"
                               value="{{$loan_product->default_loan_term}}"
                               required id="loan_term">
                    </div>
                    <div class="col-md-2">
                        <select name="loan_term_type" class="form-control " id="loan_term_type"
                                required>
                            <option value="days"
                                    @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan_product->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                            <option value="years"
                                    @if($loan_product->repayment_frequency_type=="years") selected @endif>{{trans_choice('general.year',2)}}</option>
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
                               required id="repayment_frequency">
                    </div>
                    <div class="col-md-2">
                        <select name="repayment_frequency_type" class="form-control " id="repayment_frequency_type"
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
                               value="{{date("Y-m-d")}}"
                               required id="expected_disbursement_date">
                    </div>
                    <label for="expected_first_repayment_date"
                           class="control-label col-md-2">{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="May be entered to override the date the system would schedule"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="expected_first_repayment_date" class="form-control date-picker"
                               value="{{date("Y-m-d")}}" required
                               id="expected_first_repayment_date">
                    </div>

                </div>


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
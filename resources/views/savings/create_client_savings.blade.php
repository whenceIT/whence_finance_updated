@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.savings',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.savings',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('savings/create_client_savings/'.$client->id.'/'.$savings_product->id.'/store')}}"
              class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="field_officer_id"
                           class="control-label col-md-2">
                        {{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title=""></i>
                    </label>
                    <div class="col-md-3">
                        <select name="field_officer_id" class="form-control select2" id="field_officer_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($client->staff_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <label for="created_date"
                           class="control-label col-md-2">{{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title=""></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="created_date" class="form-control date-picker"
                               value="{{date("Y-m-d")}}"
                               required id="created_date">
                    </div>
                </div>

                <div class="form-group">
                    <label for="external_id"
                           class="control-label col-md-2">{{trans_choice('general.external_id',1)}}
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="external_id" class="form-control"
                                id="external_id">
                    </div>
                    <label for="interest_rate"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="interest_rate" class="form-control"
                              required id="interest_rate" value="{{$savings_product->interest_rate}}">
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
                            @foreach(\App\Models\SavingsProductCharge::where('savings_product_id',$savings_product->id)->get() as $key)
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
                            @foreach($savings_product->charges as $key)
                                @if(!empty($key->charge))
                                    @if($key->charge->charge_type=="savings_activation" || $key->charge->charge_type=="withdrawal_fee" || $key->charge->charge_type=="annual_fee" || $key->charge->charge_type=="monthly_fee")
                                        <input type="hidden" name="charges[]" id="charge{{$key->charge_id}}"
                                               value="{{$key->charge_id}}">
                                        <tr id="row{{$key->charge->id}}">
                                            <td>{{ $key->charge->name }}</td>
                                            <td>
                                                @if($key->charge->charge_option=="flat")
                                                    {{trans_choice('general.flat',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="percentage")
                                                     {{trans_choice('general.percentage',1)}}
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
                                                @if($key->charge->charge_type=='savings_activation')
                                                    {{trans_choice('general.savings_activation',1)}}
                                                @endif
                                                @if($key->charge->charge_type=='specified_due_date')
                                                    {{trans_choice('general.specified_due_date',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='withdrawal_fee')
                                                    {{trans_choice('general.withdrawal_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='annual_fee')
                                                    {{trans_choice('general.annual_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='monthly_fee')
                                                    {{trans_choice('general.monthly_fee',2)}}
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
                    @foreach(\App\Models\CustomField::where('category','savings')->get() as $key)
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
        $('#currency_id').change(function (e) {
            var id = $('#currency_id').val();
            var url = "{!!  url('savings/product')  !!}/" + id + "/get_currency_charges";
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
                    url: "{{url('savings/product/')}}" + "/" + id + "/get_charge_detail",
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
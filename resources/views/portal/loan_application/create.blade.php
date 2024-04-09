@extends('layouts.master')
@section('title')
    {{ trans_choice('general.apply',1) }} {{ trans_choice('general.loan',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.apply',1) }} {{ trans_choice('general.loan',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('portal/loan_application/store')}}"
              class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group" id="">
                    <label for="type"
                           class="control-label col-md-2">{{trans_choice('general.type',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="type" class="form-control " id="type"
                                required>
                            <option></option>
                            <option value="client">{{trans_choice('general.client',1)}}</option>
                            <option value="group">{{trans_choice('general.group',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="clients_div" style="display: none">
                    <label for="client_id"
                           class="control-label col-md-2">{{trans_choice('general.client',1)}}kbk</label>
                    <div class="col-md-3">
                        <select name="client_id" class="form-control select2" id="client_id">
                            <option></option>
                            @foreach(\App\Models\Client::where('status', 'active')->whereIn('id',$client_ids)->get() as $key)
                                <option value="{{$key->id}}">
                                    @if($key->client_type=="individual")
                                        {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                        {{$key->account_no}}
                                    @else
                                        {{$key->full_name}} ({{$key->account_no}}
                                        )
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" id="groups_div" style="display: none">
                    <label for="group_id"
                           class="control-label col-md-2">{{trans_choice('general.group',1)}}</label>
                    <div class="col-md-3">
                        <select name="group_id" class="form-control select2" id="group_id">
                            <option></option>
                            @foreach(\App\Models\Group::where('status', 'active')->whereIn('id',$group_ids)->get() as $key)
                                <option value="{{$key->id}}">
                                    {{$key->name}}({{$key->account_no}} )
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="loan_product_id"
                           class="control-label col-md-2">{{trans_choice('general.product',1)}}</label>
                    <div class="col-md-3">
                        <select name="loan_product_id" class="form-control select2" id="loan_product_id" required>
                            <option></option>
                            @foreach(\App\Models\LoanProduct::get() as $key)
                                <option value="{{$key->id}}">
                                    {{$key->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="loan_purpose_id"
                               class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}
                            <i class="fa fa-question-circle " data-toggle="tooltip"
                               data-title="Provides an indication of how the funds provided through the loan will be directed "></i>
                        </label>
                        <div class="col-md-3">
                            <select name="loan_purpose_id" class="form-control select2" id="loan_purpose_id">
                                <option></option>
                                @foreach(\App\Models\LoanPurpose::all() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="amount"
                               class="control-label col-md-2">{{trans_choice('general.amount',1)}}
                        </label>
                        <div class="col-md-3">
                            <input type="number" name="amount" class="form-control"
                                   required id="amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes"
                               class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                        <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3">{{old('notes')}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
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
        $('#type').change(function (e) {
            if ($("#type").val() == "client") {
                $("#clients_div").show();
                $("#groups_div").hide();
                $("#groups_id").removeAttr("required");
                $("#client_id").attr("required", "required");
            }
            if ($("#type").val() == "group") {
                $("#clients_div").hide();
                $("#groups_div").show();
                $("#client_id").removeAttr("required");
                $("#group_id").attr("required", "required");
            }
        });
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
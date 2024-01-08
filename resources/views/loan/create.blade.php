@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}t6fty</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>

        <div class="box-body form-horizontal">
            <div class="form-group" id="">
                <label for="type"
                       class="control-label col-md-3">{{trans_choice('general.type',1)}}
                </label>
                <div class="col-md-5">
                    <select name="type" class="form-control " id="type"
                            required>
                        <option></option>
                        <option value="client">{{trans_choice('general.client',1)}}</option>
                        <option value="group">{{trans_choice('general.group',1)}}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">

            </div>
            <div class="form-group" id="clients_div" style="display: none">

                <label for="client_id"
                       class="control-label col-md-3">{{trans_choice('general.client',1)}}</label>
                <div class="col-md-5">
                    <select name="client_id" class="form-control select2" id="client_id">
                        <option></option>
                        @foreach(\App\Models\Client::where('status', 'active')->where('office_id',$userBranch)->where('blacklisted', 0)->get() as $key)
                            <option value="{{$key->id}}">
                                @if($key->client_type=="individual")
                                    {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                    ({{$key->account_no}})({{$key->nrc_number}})
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
                       class="control-label col-md-3">{{trans_choice('general.group',1)}}</label>
                <div class="col-md-5">
                    <select name="group_id" class="form-control select2" id="group_id">
                        <option></option>
                        @foreach(\App\Models\Group::where('status', 'active')->get() as $key)
                            <option value="{{$key->id}}">
                                {{$key->name}}({{$key->account_no}} )
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group" id="">
                <label for="loan_product_id"
                       class="control-label col-md-3">{{trans_choice('general.product',1)}}</label>
                <div class="col-md-5">
                    <select name="loan_product_id" class="form-control select2" id="loan_product_id">
                        <option></option>
                        @foreach(\App\Models\LoanProduct::get() as $key)
                            <option value="{{$key->id}}">
                                {{$key->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for=""
                       class="control-label col-md-3"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary" id="next" onClick="this.disabled=true;">{{trans_choice('general.next',1)}}</button>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#type').change(function (e) {
            if ($("#type").val() == "client") {
                $("#clients_div").show();
                $("#groups_div").hide();
            }
            if ($("#type").val() == "group") {
                $("#clients_div").hide();
                $("#groups_div").show();
            }
        });
        $("#next").click(function (e) {
            var type = $("#type").val();
            var group_id = $("#group_id").val();
            var client_id = $("#client_id").val();
            var loan_product_id = $("#loan_product_id").val();
            if (type == "") {
                alert("Please select type");
            } else {
                if (type == "client" && client_id != "" && loan_product_id != "") {
                    document.location = "{{url('loan/create_client_loan/')}}/" + client_id + "/" + loan_product_id;
                } else if (type == "group" && group_id != "" && loan_product_id != "") {
                    document.location = "{{url('loan/create_group_loan/')}}/" + group_id + "/" + loan_product_id;
                } else {
                    alert("Select client or Product");
                }
            }

        })
    </script>
@endsection
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
        <div class="alert alert-info" style="margin:10px;">
            <strong>Info:</strong> Please select the loan type, client/group, and product to proceed to the next step.
        </div>

        <div class="box-body form-horizontal">
            <div class="form-group" id="">
                <label for="type"
                       class="control-label col-md-3">{{trans_choice('general.type',1)}}
                </label>
                <div class="col-md-5">
                    <select name="type" class="form-control" id="type"
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
                       class="control-label col-md-3">{{trans_choice('general.client',1)}}
                </label>
                <div class="col-md-5">
                    <select name="client_id" class="form-control select2" id="client_id">
                        <option></option>
                        @foreach($clients as $client)
                            @if($role->role_id == 1 || ($role->role_id == 6 && $client->office->province_id == Sentinel::getUser()->province_id) || (in_array($role->role_id, [3,4]) && $client->office_id == Sentinel::getUser()->office_id))
                                <option value="{{ $client->id }}">
                                    @if($client->client_type == "individual")
                                        {{ $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name . ' (' . $client->account_no . ')(' . $client->nrc_number . ')' }}
                                    @else
                                        {{ $client->full_name . ' (' . $client->account_no . ')' }}
                                    @endif
                                </option>
                            @endif
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
                        @foreach($groups as $group)
                            @if($role->role_id == 1 || ($role->role_id == 6 && $group->office->province_id == Sentinel::getUser()->province_id) || (in_array($role->role_id, [3,4]) && $group->office_id == Sentinel::getUser()->office_id))
                                <option value="{{ $group->id }}">{{ $group->name . '(' . $group->account_no . ')' }}</option>
                            @endif
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
                        @foreach($loan_products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>



            <div class="form-group">
                <div class="col-md-12 text-right">
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

        });

        // Initialize select2 for selects
        $('#client_id').select2({
            placeholder: 'Select a client...'
        });
        $('#group_id').select2({
            placeholder: 'Select a group...'
        });
        $('#loan_product_id').select2({
            placeholder: 'Select a loan product...'
        });
    </script>
@endsection

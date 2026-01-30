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
                       class="control-label col-md-3">{{trans_choice('general.client',1)}}</label>
                <div class="col-md-5">
                    <select name="client_id" class="form-control select2" id="client_id">
                          <option></option>
                        @if($role->role_id == '3')
                        @foreach(\App\Models\Client::where('status', 'active')->where('staff_id',$userId)->where('blacklisted', 0)->get() as $key)
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
                        @elseif($role->role_id == '4')
                        @foreach(\App\Models\Client::where('status', 'active')->where('office_id', $userBranch)->where('blacklisted', 0)->get() as $key)
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

                        @elseif($role->role_id == '6')
                        @foreach($province_clients as $key)
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
                        @else
                        @foreach($clients as $key)
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
                        @endif
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

      @if($launchNewCarryOver)
        <div class="modal fade" id="broughtForwardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ url('user/create_carry_over') }}">
                @csrf

                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Carry Over</h4>
                </div>

                <div class="modal-body">
                    <p>
                        Please enter your <strong>Carry Over (from last cycle)</strong> amount to continue.
                    </p>

                    

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="brought_f"
                               class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmCarryOverModal">
    Save & Continue
</button>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmCarryOverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">Confirm Carry Over</h4>
            </div>

            <div class="modal-body">
                <p>
                    By clicking <strong>Confirm</strong>, you acknowledge that the information you have entered is
                    accurate and correct.
                </p>

                <p>
                    You further understand that if the amount entered affects your target and ultimately your
                    salary negatively  <strong>you and only you will be responsible</strong> for the consequences.
                </p>

                <p class="text-danger">
                    Please ensure the amount entered is correct before proceeding.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-danger" id="confirmSubmitCarryOver">
                    Confirm & Submit
                </button>
            </div>

        </div>
    </div>
</div>


        @endif

              @if($pendingApproval)
<div class="modal fade" id="pendingApprovalModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-info">
                <h4 class="modal-title">Carry Over</h4>
            </div>

            <div class="modal-body text-center">
                <h4 class="text-info">
                    Pending Manager Approval
                </h4>
                <p>
                    Your carry over request has been submitted and is awaiting manager approval.
                </p>
            </div>

        </div>
    </div>
</div>
@endif  

@endsection
@section('footer-scripts')
    <script>

        
  $('#pendingApprovalModal').modal('show');

          document.getElementById('confirmSubmitCarryOver').addEventListener('click', function () {
        document.querySelector('#broughtForwardModal form').submit();
    });


                  $(document).ready(function () {
        $('#broughtForwardModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

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


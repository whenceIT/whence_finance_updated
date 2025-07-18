@extends('layouts.master')
@section('title')
Payroll Pending Approval
@endsection
@section('content')
<div>
@foreach($branches_pending as $branch)
<a href="{{url('payroll/'.$branch->id.'/payroll_pending_approval')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-user-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$branch->name}}</span>
</div>
</div>
</div>
</a>
@endforeach
</div>
@endsection
@section('footer-scripts')
<script>
    $('#type').change(function(e) {
        if ($("#type").val() == "client") {
            $("#clients_div").show();
            $("#groups_div").hide();
        }
        if ($("#type").val() == "group") {
            $("#clients_div").hide();
            $("#groups_div").show();
        }
    });
    $("#next").click(function(e) {
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

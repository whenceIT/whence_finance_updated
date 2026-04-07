@extends('layouts.master')
@section('title')
Edit Payroll
@endsection
@section('content')

<?php
$months = date('m');
$dates = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$year = date('Y');
use App\Models\PayrollMeta;
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Payroll</h3>

        <div class="box-tools pull-right">
            <button onclick="window.history.back()" class="btn btn-info btn-sm">
                {{ trans_choice('general.cancel',1) }}
            </button>
        </div>

    </div>

    <form method="post" action="{{url('payroll/'.$payroll->id.'/save_edit_new_payroll')}}" class="form-horizontal"
        enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="box-body">
            <div class="form-group">
                <label for="loan_officer_id"
                    class="control-label col-md-2">
                    Staff Name
                </label>

           
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" 
                        required readonly id="name" value="{{$payroll->employee_name}}">
                </div>
                  

             


            </div>


            <div class="form-group">
                <label for="loan_officer_id"
                    class="control-label col-md-2">
                    Date
                </label>

                <div class="col-md-4">
                <input type="text" name="payroll_date" class="form-control month-picker"
       value="{{ \Carbon\Carbon::parse($payroll->payroll_date)->format('F Y') }}">

                </div>


            </div>


            <div class="form-group">
                <label for="loan_officer_id"
                    class="control-label col-md-2">
                    Office
                </label>

                <div class="col-md-4">
                    <select name="office_id" class="form-control select2" id="office_id" required>
                        @foreach(\App\Models\Office::where('id',$payroll->office_id)->get() as $key)
                        <option value="{{$key->id}}" selected>{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>


            </div>

            <div class="form-group">
                <h3 class="col-md-4">Additions</h3>
            </div>


            @foreach(\App\Models\PayrollTemplateMeta::where('type','addition')->get() as $key)
            <?php
            $payroll_field = PayrollMeta::where('payroll_id',$payroll->id)->where('payroll_template_meta_id',$key->id)->first();
            ?>

            <div class="form-group">

                <label for="principal"
                    class="control-label col-md-2">{{$key->name}}
                </label>

                <div class="col-md-4">
                    <input type="number" name="{{$key->id}}" class="form-control" step="0.01"
                        required id="{{$key->id}}" value="{{$payroll_field->value}}" onkeyup="sum();">
                </div>

            </div>

            @endforeach



            <div class="form-group">
                <h3 class="col-md-4">Deductions</h3>
            </div>

            @foreach(\App\Models\PayrollTemplateMeta::where('type','deduction')->get() as $key)
            <?php
            $payroll_field = PayrollMeta::where('payroll_id',$payroll->id)->where('payroll_template_meta_id',$key->id)->first();
            ?>
            <div class="form-group">

                <label for="principal"
                    class="control-label col-md-2">{{$key->name}}
                </label>

                <div class="col-md-4">
                    <input type="number" name="{{$key->id}}" class="form-control" step="0.01"
                        required id="{{$key->id}}" value="{{$payroll_field->value}}">
                </div>

            </div>

            @endforeach



        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
        </div>

    </form>
</div>
@endsection
@section('footer-scripts')
<script>

    $('.month-picker').datepicker({
    format: "MM yyyy",
    viewMode: "months",
    minViewMode: "months",
    autoclose: true
});


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


	        function sum() {

var basic_pay = document.getElementById('1').value;

document.getElementById('4').value = basic_pay * 0.05;
document.getElementById('6').value = basic_pay * 0.01;


if(basic_pay <= 5100){
    document.getElementById('5').value = 0;

}else if(basic_pay <= 7100){
    document.getElementById('5').value =
        (basic_pay - 5100) * 0.20;

}else if(basic_pay <= 9200){
    document.getElementById('5').value =
        ((basic_pay - 7100) * 0.30) +
        ((7100 - 5100) * 0.20);

}else{
    document.getElementById('5').value =
        ((basic_pay - 9200) * 0.37) +
        ((9200 - 7100) * 0.30) +
        ((7100 - 5100) * 0.20);
}

}
</script>
@endsection

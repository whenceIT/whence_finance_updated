@extends('layouts.master')
@section('title')
My Payslip
@endsection
@section('content')


<div style="padding-top: 10px;">

<div class="box box-primary">
   <div class="box-header with-border">
      <div class="box-tools pull-right"> 

      </div>
    </div>
    <div class="box-body">
    <div>
    <?php

use App\Models\PayrollMeta;
use App\Models\PayrollTemplateMeta;

         $todaysDate = date('m'); 

    ?>
    <!-- <a href="{{ url('payroll/create_payroll') }}" class="btn btn-info btn-sm">
                       Create
                    </a>

                    <a href="{{ url('payroll/download_payroll_excel_report') }}" class="btn btn-info btn-sm">
                       Download Report
                    </a> -->
    </div>


        <div class="table-responsive">
            <table id="view-repayments" class="table table-bordered table-condensed table-striped table-hover no-footer">
            <thead>
                    <tr style="" role="row">
                        <th>
                           Staff
                        </th>
                        @foreach($payroll_fields as $payroll_field)
                        <th>{{$payroll_field->name}}</th>
                        @endforeach
                        <th>
                            Net Pay
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payroll_list as $payroll)
                    <?php
                   $payroll_info = PayrollMeta::where('payroll_id',$payroll->id)->get();

                    ?>
                    <?php 
                    $basic_pay = 0;
                    $allowances = 0;
                    $salary_deductions = 0;
                    $charges = 0;
                    $NAPSA = 0;
                    $NHIMA = 0;
                    $gross_pay = 0;
                    $net_pay = 0;
                    $additions = 0;
                    $deductions = 0;

                    foreach($payroll_info as $info){
                        $payroll_field = PayrollTemplateMeta::where('id',$info->payroll_template_meta_id)->first();
                        if($payroll_field->type == 'addition'){
                            $additions = $additions + $info->value;
                        }else{
                            $deductions = $deductions + $info->value;
                        }
                    }

                    $net_pay =  $additions - $deductions;


                   
                    ?>
                    <tr>
                        <td>{{$payroll->employee_name}} </td>
                        @foreach($payroll_info as $info)
                        <td>{{number_format($info->value,2)}}</td>
                        @endforeach
                     
                      
                        <td>{{number_format($net_pay,2)}}</td>
                        <td>{{date("M, Y", strtotime($payroll->payroll_date))}}</td>
                        <td>{{($payroll->status)}}</td>
                        <td>
                            <div>
                                <a type="button" class="btn bg-blue"
                                                   href="{{url('payroll/'.$payroll->id.'/payslip')}}" data-toggle="tooltip" title="{{trans_choice('general.view',1)}}">
                                                  Generate payslip
                                </a>
                            </div>
                        </td>
                    </tr>

                    @endforeach
                    </tbody>
                 
            </table>
        </div>
    </div>
    <p></p>
</div>

</div>

@endsection
@section('footer-scripts')
@endsection

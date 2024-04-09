@extends('layouts.master')
@section('title')Payroll
@endsection
@section('content')
<div class="box box-primary">
   <div class="box-header with-border">
      <div class="box-tools pull-right"> 

      </div>
    </div>
    <div class="box-body">
    <div>
    <?php
         $todaysDate = date('m'); 
    ?>
    <a href="{{ url('payroll/create_payroll') }}" class="btn btn-info btn-sm">
                       Create
                    </a>

                    <a href="{{ url('payroll/download_payroll_excel_report') }}" class="btn btn-info btn-sm">
                       Download Report
                    </a>
    </div>
        <div class="table-responsive">
            <table id="view-repayments" class="table table-bordered table-condensed table-striped table-hover no-footer">
            <thead>
                    <tr style="" role="row">
                        <th>
                           Staff
                        </th>
                        <th>
                            Gross Pay
                        </th>
                        <th>
                           NAPSA
                        </th>
                        <th>
                            NHIMA
                        </th>
                        <th>
                            PAYE
                        </th>
                        <th>
                            Allowances
                        </th>
                        <th>
                            Advance Deductions
                        </th>
                        <th>
                           Charges
                        </th>
                        <th>
                            Net Pay
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payroll_list as $payroll)
                    <?php 
                    $basic_pay = 0;
                    $allowances = 0;
                    $salary_deductions = 0;
                    $charges = 0;
                    $NAPSA = 0;
                    $NHIMA = 0;
                    $gross_pay = 0;
                    $net_pay = 0;

                    $basic_pay = $payroll->basic_pay;
                    $allowances = $payroll->allowances;
                    $salary_deductions = $payroll->salary_deductions;
                    $charges = $payroll->charges;
                    $NAPSA = $basic_pay * 0.05;
                    $NHIMA = $basic_pay * 0.01;
                    $gross_pay = $basic_pay + $allowances - $salary_deductions - $charges;
                    if($basic_pay <= 5100){
                       $PAYE = 0;
                    }else if($basic_pay <= 7100){
                       $PAYE = ($basic_pay - 5100)*0.20;
                    }else if($basic_pay <= 9200){
                        $PAYE = (($basic_pay - 7100)*0.30) + ((7100 - 5100)*0.20);
                    }else{
                        $PAYE = ($basic_pay - 9200)*0.37 + (9200 - 7100)*0.30 + (7100 - 5100)*0.20;
                    }
                    $net_pay = $gross_pay - $NAPSA - $NHIMA - $PAYE;

                    $user =  \App\Models\User::where('id',$payroll->user_id)->first();
                    ?>
                    <tr>
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        <td>{{number_format($payroll->basic_pay,2)}}</td>
                        <td>{{number_format($NAPSA,2)}}</td>
                        <td>{{number_format($NHIMA,2)}}</td>
                        <td>{{number_format($PAYE,2)}}</td>
                        <td>{{number_format($payroll->allowances,2)}}</td>
                        <td>{{number_format($payroll->salary_deductions,2)}}</td>
                        <td>{{number_format($payroll->charges,2)}}</td>
                        <td>{{number_format($net_pay,2)}}</td>
                        <td>{{date("M, Y",strtotime($payroll->created_at))}}</td>
                        <td>
                            <div>
                                <a type="button" class="btn-xs bg-navy"
                                                   href="{{url('payroll/'.$payroll->id.'/edit_payroll')}}" data-toggle="tooltip" title="{{trans_choice('general.view',1)}}">
                                <i class="fa fa-pencil-square" aria-hidden="true"></i>
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
@endsection
@section('footer-scripts')
@endsection
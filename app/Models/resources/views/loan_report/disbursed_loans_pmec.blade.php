@extends('layouts.master')
@section('title')
    {{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}}
@endsection
@section('content')
    <style>

        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 1985pt
        }

        .style-1 {
            color: white;
            padding-left: 10pt;
            font-size: 14pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
            background-color: #339933
        }

        .style-10 {
            color: black;
            padding-left: 2pt;
            padding-right: 2pt;
            font-size: 10pt;
            font-family: "Arial Narrow";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-11 {
            color: black;
            padding-left: 2pt;
            font-size: 9pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;

        }

        .style-12 {
            color: black;
            padding-left: 2pt;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-13 {
            color: black;
            padding-left: 2pt;
            font-size: 9pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-14 {
            color: black;
            padding-left: 2pt;
            padding-right: 2pt;
            font-size: 9pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;

        }

        .style-15 {
            border-top: 2pt solid black
        }

        .style-16 {
            border-top: 2pt solid black;
            border-bottom: 2pt solid black
        }

        .style-17 {
            color: white;
            padding-left: 10pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
            background-color: #339933
        }

        .style-18 {
            color: black;
            padding-left: 2pt;
            font-size: 9pt;
            font-family: "Arial Narrow";
            font-weight: bold;
            font-style: normal;
            text-decoration: underline;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-19 {
            color: black;
            font-size: 10pt;
            font-family: serif;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-2 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-20 {
            width: 50px;
            height: 50px
        }

        .style-3 {
            color: black;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-4 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-5 {
            color: #2f2c35;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-6 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
        }

        .style-7 {
            color: black;
            padding-left: 2pt;
            font-size: 10pt;
            font-family: "Arial Narrow";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
            background-color: #cccccc
        }

        .style-8 {
            color: black;
            font-size: 10pt;
            font-family: "Arial Narrow";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
            background-color: #cccccc
        }

        .style-9 {
            color: black;
            padding-left: 2pt;
            font-size: 10pt;
            font-family: "Arial Narrow";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border: none;
            background-color: #cccccc
        }


    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="start_date"
                           class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="start_date" class="form-control date-picker"
                               value="{{$start_date}}"
                               required id="start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date"
                           class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="end_date" class="form-control date-picker"
                               value="{{$end_date}}"
                               required id="end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="loan_officer_id"
                           class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="loan_officer_id" class="form-control select2" id="loan_officer_id" required>
                            <option value="0">{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status"
                           class="control-label col-md-2">{{trans_choice('general.status',1)}}</label>
                    <div class="col-md-3">
                        <select name="status" class="form-control select2" id="status" required>
                            <option value="0"
                                    @if($status=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            <option value="disbursed"
                                    @if($status=="active") selected @endif>{{trans_choice('general.active',1)}}</option>
                            <option value="closed"
                                    @if($status=="active") selected @endif>{{trans_choice('general.closed',1)}}</option>
                            <option value="written_off"
                                    @if($status=="active") selected @endif>{{trans_choice('general.written_off',1)}}</option>
                            <option value="rescheduled"
                                    @if($status=="active") selected @endif>{{trans_choice('general.rescheduled',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="loan_product_id"
                           class="control-label col-md-2">{{trans_choice('general.product',1)}}</label>
                    <div class="col-md-3">
                        <select name="loan_product_id" class="form-control select2" id="loan_product_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\LoanProduct::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($loan_product_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for=""
                           class="control-label col-md-2"></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>

                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/disbursed_loans/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id.'&status='.$status)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/disbursed_loans/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id.'&status='.$status)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/disbursed_loans/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id.'&status='.$status)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.panel-body -->

    </div>

    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-primary">
            <div class="box-body table-responsive">

            <table class="table">
  <thead>
    <tr>
           
      <th >#</th>
      <th>NRC</th>
      <th >{{trans_choice('general.employee',1)}} #</th>
      <th >{{trans_choice('general.client',1)}} {{trans_choice('general.name',1)}}</th>
      <th >{{trans_choice('general.gender',2)}}</th>
      <th >{{trans_choice('general.product',1)}}</th>
      <th>{{trans_choice('general.amount',1)}}</th>
      <th>{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</th>
      <th >{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</th>
      <th>{{trans_choice('general.installment',2)}}</th>
      <th >Payee code</th>
      <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}</th>
      <th>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</td>
    </tr>
  </thead>
  <tbody>
    
  <?php

$total_due = 0;
$total_principal = 0;
$total_principal_paid = 0;
$total_principal_outstanding = 0;
$total_interest = 0;
$total_interest_paid = 0;
$total_interest_outstanding = 0;
$total_fees = 0;
$total_fees_paid = 0;
$total_fees_outstanding = 0;
$total_penalty = 0;
$total_penalty_paid = 0;
$total_penalty_outstanding = 0;
$total_outstanding = 0;
$total_amount = 0;
$total_arrears = 0;
$total_loans = 0;
?>
@foreach($data as $key)
    <?php
    $amount = 0;
    $outstanding = 0;
    $principal = 0;
    $principal_paid = 0;
    $interest = 0;
    $interest_paid = 0;
    $fees = 0;
    $fees_paid = 0;
    $penalty = 0;
    $penalty_paid = 0;
    $amount_in_arrears = 0;
    $days_in_arrears = 0;
    $balance = 0;
    $percentage = 0;
    $late_count = 0;
    $timely_repayments = 0;
    $installments = 0;
    $total_repayments_due = 0;
    foreach ($key->repayment_schedules as $schedule) {
        $installments++;
        if (strtotime($schedule->due_date) < strtotime($end_date)) {
            $total_repayments_due++;
            $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
        }
        if (!empty($schedule->from_date)) {
            if (strtotime($schedule->due_date) > strtotime($schedule->from_date) && strtotime($schedule->due_date) < strtotime($end_date)) {
                $timely_repayments = $timely_repayments + 1;
            }
        }
        $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
        $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
        $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
        $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;

        $principal_paid = $principal_paid + $schedule->principal_paid;
        $interest_paid = $interest_paid + $schedule->interest_paid;
        $fees_paid = $fees_paid + $schedule->fees_paid;
        $penalty_paid = $penalty_paid + $schedule->penalty_paid;
        $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
        if ($amount_in_arrears > 0) {
            $late_count++;
            if ($late_count == 1) {
                $overdue_date = $schedule->due_date;
            }
        }
    }

    if ($amount_in_arrears > 0) {
        $date1 = new DateTime($overdue_date);
        $date2 = new DateTime($end_date);
        $days_in_arrears = $date2->diff($date1)->format("%a");
    }
    if ($total_repayments_due > 0) {
        $percentage = round($timely_repayments * 100 / $total_repayments_due, 2);
    }
    $amount = $principal + $penalty + $interest + $fees;
    $total_amount = $total_amount + $amount;
    $total_outstanding = $total_outstanding + $balance;
    $total_due = $total_due + $amount_in_arrears;
    $total_principal = $total_principal + $principal;
    $total_interest = $total_interest + $interest;
    $total_fees = $total_fees + $fees;

    $total_penalty = $total_penalty + $penalty;
    $total_principal_paid = $total_principal_paid + $principal_paid;
    $total_interest_paid = $total_interest_paid + $interest_paid;
    $total_fees_paid = $total_fees_paid + $fees_paid;
    $total_penalty_paid = $total_penalty_paid + $penalty_paid;

    $total_principal_outstanding = $total_principal_outstanding + $principal - $principal_paid;
    $total_interest_outstanding = $total_interest_outstanding + $interest - $interest_paid;
    $total_fees_outstanding = $total_fees_outstanding + $fees - $fees_paid;
    $total_penalty_outstanding = $total_penalty_outstanding + $penalty - $penalty_paid;
    $total_amount = $total_amount + $key->principal;
    $total_arrears = $total_arrears + $amount_in_arrears;
    //select appropriate schedules


    ?>
    <tr>
      <th scope="row">
        @if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        {{$key->client->id}}
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->id}}
                                    @endif
                                @endif</th>
      <td>{{$key->external_id}}</td>
      <td>{{$key->emp_id}}</td>
      <td>@if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                        @endif
                                        @if($key->client->client_type=="business")
                                            {{$key->client->full_name}}
                                        @endif
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->name}}
                                    @endif
                                @endif</td>
                                <td>@if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            @if($key->client->gender=="male")
                                                {{trans_choice('general.male',1)}}
                                            @endif
                                            @if($key->client->gender=="female")
                                                {{trans_choice('general.female',1)}}
                                            @endif
                                            @if($key->client->gender=="other")
                                                {{trans_choice('general.other',1)}}
                                            @endif
                                            @if($key->client->gender=="unspecified")
                                                {{trans_choice('general.unspecified',1)}}
                                            @endif
                                        @endif
                                    @endif
                                @endif</td>
                                <td>@if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif</td>
                                <td class="primary">{{number_format($key->principal) }}</td>
                                <td>{{$key->first_repayment_date }}</td>
                                <td>{{$key->expected_maturity_date}}</td>
                                <td>{{number_format($schedule->principal + $schedule->interest, $key->decimals)}}</td>
                                <td>{{$key->pay_id}}</td>
                                <td>@if(!empty($key->loan_purpose))
                                    {{$key->loan_purpose->name}}
                                @endif</td>
                                <td>   <?php
                                $disbursement_detail = \App\Models\LoanTransaction::where('transaction_type', 'disbursement')->where('reversed', 0)->orderBy('date', 'asc')->first();
                                if (!empty($disbursement_detail)) {
                                    if (!empty($disbursement_detail->payment_detail)) {
                                        if (!empty($disbursement_detail->payment_detail->type)) {
                                            echo $disbursement_detail->payment_detail->type->name;
                                        }
                                    }
                                }
                                ?></td>
    </tr>
   
    
    @endforeach
    
  </tbody>
  <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_principal,2)}} </td>
                        <th></th>
                        <th></th>
                        <th> </td>
                        
                    </tr>
</table>




                <!-- <table  class="style-0">

                    <tbody>
                    <tr style="height: 33pt">
                        <td colspan="40" valign="middle"
                            class="style-1"> {{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}} {{trans_choice('general.report',1)}}</td>

                    </tr>
                    <tr style="height: 7pt">
                        <td colspan="40"></td>

                    </tr>
                    <tr style="height: 13pt">
                        <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
                        <td colspan="2" valign="middle" class="style-3">{{$start_date}}</td>
                        <td colspan="6"></td>
                        <td colspan="4" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="5" valign="middle" class="style-5">{{date("Y-m-d H:i:s")}}</td>
                        <td colspan="20"></td>
                    </tr>
                    <tr style="height: 1pt">
                        <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
                        <td colspan="2" valign="middle" class="style-3">{{$end_date}}</td>
                        <td colspan="35"></td>

                    </tr>


          
                    <?php

                    $total_due = 0;
                    $total_principal = 0;
                    $total_principal_paid = 0;
                    $total_principal_outstanding = 0;
                    $total_interest = 0;
                    $total_interest_paid = 0;
                    $total_interest_outstanding = 0;
                    $total_fees = 0;
                    $total_fees_paid = 0;
                    $total_fees_outstanding = 0;
                    $total_penalty = 0;
                    $total_penalty_paid = 0;
                    $total_penalty_outstanding = 0;
                    $total_outstanding = 0;
                    $total_amount = 0;
                    $total_arrears = 0;
                    $total_loans = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $amount = 0;
                        $outstanding = 0;
                        $principal = 0;
                        $principal_paid = 0;
                        $interest = 0;
                        $interest_paid = 0;
                        $fees = 0;
                        $fees_paid = 0;
                        $penalty = 0;
                        $penalty_paid = 0;
                        $amount_in_arrears = 0;
                        $days_in_arrears = 0;
                        $balance = 0;
                        $percentage = 0;
                        $late_count = 0;
                        $timely_repayments = 0;
                        $installments = 0;
                        $total_repayments_due = 0;
                        foreach ($key->repayment_schedules as $schedule) {
                            $installments++;
                            if (strtotime($schedule->due_date) < strtotime($end_date)) {
                                $total_repayments_due++;
                                $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                            }
                            if (!empty($schedule->from_date)) {
                                if (strtotime($schedule->due_date) > strtotime($schedule->from_date) && strtotime($schedule->due_date) < strtotime($end_date)) {
                                    $timely_repayments = $timely_repayments + 1;
                                }
                            }
                            $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
                            $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
                            $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
                            $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;

                            $principal_paid = $principal_paid + $schedule->principal_paid;
                            $interest_paid = $interest_paid + $schedule->interest_paid;
                            $fees_paid = $fees_paid + $schedule->fees_paid;
                            $penalty_paid = $penalty_paid + $schedule->penalty_paid;
                            $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                            if ($amount_in_arrears > 0) {
                                $late_count++;
                                if ($late_count == 1) {
                                    $overdue_date = $schedule->due_date;
                                }
                            }
                        }

                        if ($amount_in_arrears > 0) {
                            $date1 = new DateTime($overdue_date);
                            $date2 = new DateTime($end_date);
                            $days_in_arrears = $date2->diff($date1)->format("%a");
                        }
                        if ($total_repayments_due > 0) {
                            $percentage = round($timely_repayments * 100 / $total_repayments_due, 2);
                        }
                        $amount = $principal + $penalty + $interest + $fees;
                        $total_amount = $total_amount + $amount;
                        $total_outstanding = $total_outstanding + $balance;
                        $total_due = $total_due + $amount_in_arrears;
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;

                        $total_penalty = $total_penalty + $penalty;
                        $total_principal_paid = $total_principal_paid + $principal_paid;
                        $total_interest_paid = $total_interest_paid + $interest_paid;
                        $total_fees_paid = $total_fees_paid + $fees_paid;
                        $total_penalty_paid = $total_penalty_paid + $penalty_paid;

                        $total_principal_outstanding = $total_principal_outstanding + $principal - $principal_paid;
                        $total_interest_outstanding = $total_interest_outstanding + $interest - $interest_paid;
                        $total_fees_outstanding = $total_fees_outstanding + $fees - $fees_paid;
                        $total_penalty_outstanding = $total_penalty_outstanding + $penalty - $penalty_paid;
                        $total_amount = $total_amount + $key->principal;
                        $total_arrears = $total_arrears + $amount_in_arrears;
                        //select appropriate schedules


                        ?>
                        <tr style="height: 12pt">
                            <td valign="top" class="style-11">
                                @if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        {{$key->client->id}}
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->id}}
                                    @endif
                                @endif
                            </td>
                            <td  >{{$key->external_id}}</td>
                            <td >{{$key->external_id}}</td>
                            <td  valign="top" >
                                @if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                        @endif
                                        @if($key->client->client_type=="business")
                                            {{$key->client->full_name}}
                                        @endif
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->name}}
                                    @endif
                                @endif
                            </td>
                            

                            <td valign="top">
                                @if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            @if($key->client->gender=="male")
                                                {{trans_choice('general.male',1)}}
                                            @endif
                                            @if($key->client->gender=="female")
                                                {{trans_choice('general.female',1)}}
                                            @endif
                                            @if($key->client->gender=="other")
                                                {{trans_choice('general.other',1)}}
                                            @endif
                                            @if($key->client->gender=="unspecified")
                                                {{trans_choice('general.unspecified',1)}}
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            </td>
                           
                            
                            <td valign="top" >
                                @if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif
                            </td>
                            <td  valign="top">{{number_format($principal,2)}}</td>
                            
                           

                        </tr>
                    @endforeach


                    <tr style="height: 1pt">
                        <td colspan="9"></td>
                        <td colspan="2">{{number_format($total_principal,2)}} </td>
                        <td>{{number_format($total_interest,2)}} </td>
                        <td>{{number_format($total_fees,2)}} </td>
                        <td>{{number_format($total_penalty,2)}} </td>
                        <td colspan="2">{{number_format($total_amount,2)}} </td>
                        <td>{{number_format($total_principal_outstanding,2)}} </td>
                        <td>{{number_format($total_interest_outstanding,2)}} </td>
                        <td>{{number_format($total_fees_outstanding,2)}} </td>
                        <td>{{number_format($total_penalty_outstanding,2)}} </td>
                        <td colspan="2">{{number_format($total_outstanding,2)}} </td>
                        <td colspan="15"></td>
                        <td colspan="2">{{number_format($total_arrears,2)}} </td>
                        <td></td>
                    </tr>

                    <tr style="height: 20pt">
                        <td colspan="40" valign="middle" class="style-17">Report Definitions</td>
                        <td>
                        </td>
                    </tr>
                    <tr style="height: 10pt">
                        <td colspan="40"></td>
                    </tr>
                    <tr style="height: 30pt">
                        <td colspan="2" valign="top" class="style-18">TRP</td>
                        <td colspan="20" valign="top" class="style-11">The Timely Repayment Percentage (TRP) displays
                            the number of loan installments that have been paid on time, as a percentage of the total
                            number of loaninstallments that are due.
                        </td>
                        <td colspan="18"></td>
                    </tr>

                    </tbody>
                </table> -->

            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("body").addClass('sidebar-xs sidebar-collapse');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection

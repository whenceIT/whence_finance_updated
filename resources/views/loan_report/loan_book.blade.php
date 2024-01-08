@extends('layouts.master')
@section('title')
    {{trans_choice('general.loan',1)}} {{trans_choice('general.book',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            {{trans_choice('general.loan',1)}} {{trans_choice('general.book',2)}} {{trans_choice('general.report',2)}}
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
                                    <a href="{{url('report/loan_report/age_analysis/pdf?end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/age_analysis/excel?end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/age_analysis/csv?end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>

    </div>
    <!-- /.box -->
    @if(!empty($end_date))
        <div class="box box-primary">
            <div class="panel-body table-responsive ">

                <table class="table table-condensed table-hover " style="font-size: 12px">
                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="19" valign="middle"
                            class="style-1"> {{trans_choice('general.loan',1)}} {{trans_choice('general.book',1)}} {{trans_choice('general.report',1)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.date',1)}} :</td>
                        <td valign="middle" class="style-3">{{$end_date}}</td>
                        <td colspan="2" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="3" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
                        <td colspan="12"></td>
                    </tr>
                    <tr class="">
                        <th>{{trans_choice('general.client',1)}} ID</th>
                        <th >{{trans_choice('general.client',1)}} {{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.office',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.principal',1)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                        <th>{{trans_choice('general.fund',1)}}</th>
                        <th>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
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
                        $total_total_clients = 0;
                        $total_clients = \App\Models\Loan::where('status', 'active')->where('office_id', $key->id)->count();
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
                        $total_total_clients = $total_total_clients + $total_clients;
                        //select appropriate schedules
                        


                        ?>
                            <tr>
                                <td>
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
                                <td>
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
                                <td>
                                    @if(!empty($key->office))
                                    {{$key->office->name}}
                                @endif
                                </td>
                                <td>
                                    @if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif
                                </td>
                                                           
                                <td>{{number_format($principal - $principal_paid,$key->decimals)}}</td>
                                <td>{{number_format($balance,$key->decimals)}}</td>
                                <td> @if(!empty($key->loan_officer))
                                    {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                                @endif
                            </td>
                                <td> @if(!empty($key->fund))
                                    {{$key->fund->name}}
                                @endif
                            </td>
                                <td>{{$key->expected_maturity_date}} </td>
                             </tr>
                       
                        
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4"></th>
                        <th>{{number_format($total_principal,2)}}</th>
                        <th>{{number_format($total_outstanding,2)}}</th>
                        
                        <th colspan="3"></th>                                            
                        <th colspan="3"></th>
                    </tr>
                    <tr style="height: 20pt">
                        <td colspan="40" valign="middle" class="style-17">{{ $total_total_clients }}</td>
                        <td colspan="40" valign="middle" class="style-17">Report Definitions</td>
                        <td>
                        </td>
                    </tr>
                    <tr style="height: 10pt">
                        <td colspan="40"></td>
                    </tr>
                    <tr style="height: 30pt">
                        <td colspan="2" valign="top" class="style-18">Loan Book</td>
                        <td colspan="20" valign="top" class="style-11">The Loan Book shows a display of Active Loans.
                        </td>
                        <td colspan="18"></td>
                    </tr>
                    </tfoot>
                    
                </table>
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

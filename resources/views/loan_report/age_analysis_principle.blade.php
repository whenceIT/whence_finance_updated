@extends('layouts.master')
@section('title')
{{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} {{trans_choice('general.report',1)}} by {{trans_choice('general.principal',1)}} {{trans_choice('general.balance',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
            {{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} {{trans_choice('general.report',1)}} by {{trans_choice('general.principal',1)}} {{trans_choice('general.balance',2)}}
                @if(!empty($end_date))
                    as at: <b> {{$end_date}}</b>
                @endif
            </h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
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
                                    <a href="{{url('report/loan_report/age_analysis_principle/excel?end_date='.$end_date.'&office_id='.$office_id)}}"
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
                            class="style-1"> {{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} {{trans_choice('general.report',1)}} by {{trans_choice('general.principal',1)}} {{trans_choice('general.balance',2)}}</td>
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
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                        <th>{{trans_choice('general.office',1)}}</th>
                        <th>{{trans_choice('general.client',1)}}</th>
                        <th>{{trans_choice('general.phone',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.fund',1)}}</th>
                        <th> Current</th>
                        <th>{{trans_choice('general.below',1)}} 30 {{trans_choice('general.day',2)}}</th>
                        <th>30 - 89 {{trans_choice('general.day',2)}}</th>
                        <th>90 - 119 {{trans_choice('general.day',2)}}</th>
                        <th>120 - 179 {{trans_choice('general.day',2)}}</th>
                        <th>{{trans_choice('general.over',1)}} 179 {{trans_choice('general.day',2)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                        
                    </tr>


                    <?php
                    $total_outstanding = 0;
                    $total_due = 0;
                    $total_principal = 0;
                    $total_interest = 0;
                    $total_fees = 0;
                    $total_penalty = 0;
                    $total_amount = 0;
                    $loans_in_arrears = 0;
                    $total_on_time_loans = 0;
                    $total_on_time_amount = 0;
                    $total_p_30_loans = 0;
                    $total_p_30_amount = 0;
                    $total_p_60_loans = 0;
                    $total_p_60_amount = 0;
                    $total_p_90_loans = 0;
                    $total_p_90_amount = 0;
                    $total_p_180_loans = 0;
                    $total_p_180_amount = 0;
                    $total_p_180_plus_loans = 0;
                    $total_p_180_plus_amount = 0;
                    $total_p_loans = 0;
                    $total_p_amount = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $due = 0;
                        $amount = 0;
                        $outstanding = 0;
                        $principal = 0;
                        $interest = 0;
                        $fees = 0;
                        $penalty = 0;
                        $amount_in_arrears = 0;
                        $days_in_arrears = 0;
                        $on_time_loans = 0;
                        $on_time_amount = 0;
                        $p_30_loans = 0;
                        $p_30_amount = 0;
                        $p_60_loans = 0;
                        $p_60_amount = 0;
                        $p_90_loans = 0;
                        $p_90_amount = 0;
                        $p_180_loans = 0;
                        $p_180_amount = 0;
                        $p_180_plus_loans = 0;
                        $p_180_plus_amount = 0;
                        $p_loans = 0;
                        $p_amount = 0;
                        

                        if (strtotime($key->expected_maturity_date) < strtotime($end_date)) {
                            $remaining_days = 0;
                        } else {
                            $date1 = new DateTime($key->expected_maturity_date);
                            $date2 = new DateTime($end_date);
                            $remaining_days = $date2->diff($date1)->format("%a");
                        }

                        $days_since_last_payment = 0;
                        $balance = 0;
                        $percentage = 0;
                        $late_count = 0;
                        $schedule_principal = 0;
                        foreach ($key->repayment_schedules as $schedule) {
                            if (strtotime($schedule->due_date) < strtotime($end_date)) {
                                $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid));
                            }
                            $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid;
                            $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
                            $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
                            $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;
                            $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid));
                            if ($amount_in_arrears > 0) {
                                $late_count++;
                                if ($late_count == 1) {
                                    $overdue_date = $schedule->due_date;
                                }
                            }

                            
                        }

                        if ($amount_in_arrears > 0) {
                            $loans_in_arrears = $loans_in_arrears + 1;
                            $total_outstanding = $total_outstanding + $balance;
                            $total_due = $total_due + $amount_in_arrears;
                            $total_principal = $total_principal + $principal;
                            $total_interest = $total_interest + $interest;
                            $total_fees = $total_fees + $fees;
                            $total_penalty = $total_penalty + $penalty;
                            $total_amount = $total_amount + $key->principal;
                            $date1 = new DateTime($overdue_date);
                            $date2 = new DateTime($end_date);
                            $days_in_arrears = $date2->diff($date1)->format("%a");
                            if ($days_in_arrears < 30) {
                                    $p_30_loans = $p_30_loans + 1;
                                    $p_30_amount = $p_30_amount + $principal;
                                }
                                if ($days_in_arrears > 30 && $days_in_arrears <= 60) {
                                    $p_60_loans = $p_60_loans + 1;
                                    $p_60_amount = $p_60_amount + $principal;
                                }
                                if ($days_in_arrears > 60 && $days_in_arrears <= 90) {
                                    $p_90_loans = $p_90_loans + 1;
                                    $p_90_amount = $p_90_amount + $principal;
                                }
                                if ($days_in_arrears > 90 && $days_in_arrears <= 180) {
                                    $p_180_loans = $p_180_loans + 1;
                                    $p_180_amount = $p_180_amount + $principal;
                                }
                                if ($days_in_arrears > 180) {
                                    $p_180_plus_loans = $p_180_plus_loans + 1;
                                    $p_180_plus_amount = $p_180_plus_amount + $principal;
                                }

                            } else {
                                $on_time_loans = $on_time_loans + 1;
                                $on_time_amount = $on_time_amount + $principal;
                            }

                        
                        $p_amount =$on_time_amount + $p_30_amount + $p_60_amount + $p_90_amount + $p_180_amount + $p_180_plus_amount;
                        $p_loans = $p_30_loans + $p_60_loans + $p_90_loans + $p_180_loans + $p_180_plus_loans;
                        $total_on_time_loans = $total_on_time_loans + $on_time_loans;
                        $total_on_time_amount = $total_on_time_amount + $on_time_amount;
                        $total_p_30_loans = $total_p_30_loans + $p_30_loans;
                        $total_p_30_amount = $total_p_30_amount + $p_30_amount;
                        $total_p_60_loans = $total_p_60_loans + $p_60_loans;
                        $total_p_60_amount = $total_p_60_amount + $p_60_amount;
                        $total_p_90_loans = $total_p_90_loans + $p_90_loans;
                        $total_p_90_amount = $total_p_90_amount + $p_90_amount;
                        $total_p_180_loans = $total_p_180_loans + $p_180_loans;
                        $total_p_180_amount = $total_p_180_amount + $p_180_amount;
                        $total_p_180_plus_loans = $total_p_180_plus_loans + $p_180_plus_loans;
                        $total_p_180_plus_amount = $total_p_180_plus_amount + $p_180_plus_amount;
                        $total_p_loans = $total_p_loans + $p_loans;
                        $total_p_amount = $total_p_amount + $p_amount;

                            $transaction = \App\Models\LoanTransaction::where('loan_id',
                                $key->id)->where('transaction_type',
                                'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                            if (!empty($transaction)) {
                                $date2 = new DateTime($transaction->date);
                                $date1 = new DateTime($end_date);
                                $days_since_last_payment = $date2->diff($date1)->format("%r%a");
                            } else {
                                $days_since_last_payment = 0;
                            }
                        

                        //select appropriate schedules


                        ?>
                        @if($days_in_arrears >= 0)
                            <tr>
                                <td>
                                    @if(!empty($key->loan_officer))
                                        {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($key->office))
                                        {{$key->office->name}}
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
                                    @if(!empty($key->client))
                                        {{$key->client->mobile}}
                                    @endif
                                    @if(!empty($key->group))
                                        {{$key->group->mobile}}
                                    @endif
                                </td>
                                <td>{{$key->id}}</td>
                                <td>
                                    @if(!empty($key->loan_product))
                                        {{$key->loan_product->name}}
                                    @endif
                                </td>
                                <td>
                                <?php
                                $disbursement_detail = \App\Models\LoanTransaction::where('transaction_type', 'disbursement')->where('reversed', 0)->orderBy('date', 'asc')->first();
                                if (!empty($disbursement_detail)) {
                                    if (!empty($disbursement_detail->payment_detail)) {
                                        if (!empty($disbursement_detail->payment_detail->type)) {
                                            echo $disbursement_detail->payment_detail->type->name;
                                        }
                                    }
                                }
                                ?>
                                </td>
                                <td>
                                 {{number_format($on_time_amount,2)}}
                                </td>
                                <td>{{number_format($p_30_amount,2)}}</td>
                                <td>{{number_format($p_60_amount,2)}}</td>
                                <td>{{number_format($p_90_amount,2)}}</td>
                                <td>{{number_format($p_180_amount,2)}}</td>
                                <td>{{number_format($p_180_plus_amount,2)}}</td>
                                <td>{{number_format($p_amount,2)}}</td>
                               
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th></th>
                        <th>{{number_format($total_on_time_amount,2)}}</th>
                        <th>{{number_format($total_p_30_amount,2)}}</th>
                        <th>{{number_format($total_p_60_amount,2)}}</th>
                        <th>{{number_format($total_p_90_amount,2)}}</th>
                        <th>{{number_format($total_p_180_amount,2)}}</th>
                        <th>{{number_format($total_p_180_plus_amount,2)}}</th>
                        <th>{{number_format($total_p_amount,2)}}</th>
                        <th colspan="3"></th>                                            
                        <th colspan="3"></th>
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

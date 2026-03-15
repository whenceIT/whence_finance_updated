@extends('layouts.master')
@section('title')
    {{trans_choice('general.savings',2)}} {{trans_choice('general.transaction',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}
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
                                    <a href="{{url('report/savings_report/savings_transactions/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_transactions/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_transactions/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->

    </div>

    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-primary">
            <div class="box-body table-responsive ">
                <table class="table  table-condensed table-hover">
                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="11" valign="middle"
                            class="style-1"> {{trans_choice('general.savings',1)}}  {{trans_choice('general.transaction',2)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
                        <td valign="middle" class="style-3">{{$start_date}}</td>
                        <td colspan="2" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="3" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
                        <td colspan="4"></td>
                    </tr>
                    <tr style="height: 45pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
                        <td valign="middle" class="style-3">{{$end_date}}</td>
                        <td colspan="9"></td>
                    </tr>
                    <tr class="">
                        <td>{{trans_choice('general.id',1)}}</td>
                        <td>{{trans_choice('general.client',1)}}</td>
                        <td>{{trans_choice('general.savings',1)}}#</td>
                        <td>{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}} </td>
                        <td>{{trans_choice('general.type',1)}}</td>
                        <td>{{trans_choice('general.debit',1)}}</td>
                        <td>{{trans_choice('general.credit',1)}}</td>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{trans_choice('general.channel',1)}}</td>
                    </tr>
                    <?php
                    $total_debit = 0;
                    $total_credit = 0;
                    $decimals = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        if (!empty($key->savings)) {
                            $decimals = $key->savings->decimals;
                        } else {
                            $decimals = 0;
                        }

                        $total_debit = $total_debit + $key->debit;
                        $total_credit = $total_credit + $key->credit;


                        ?>
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                @if(!empty($key->savings))
                                    @if($key->savings->client_type=="client")
                                        @if(!empty($key->savings->client))
                                            @if($key->savings->client->client_type=="individual")
                                                {{$key->savings->client->first_name}} {{$key->savings->client->middle_name}} {{$key->savings->client->last_name}}
                                            @endif
                                            @if($key->savings->client->client_type=="business")
                                                {{$key->savings->client->full_name}}
                                            @endif
                                        @endif
                                    @endif
                                    @if($key->savings->client_type=="group")
                                        @if(!empty($key->savings->group))
                                            {{$key->savings->group->name}}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>{{$key->savings->id}}</td>
                            <td>
                                @if(!empty($key->savings))
                                    @if(!empty($key->savings->field_officer))
                                        {{$key->savings->field_officer->first_name}}  {{$key->savings->field_officer->last_name}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($key->transaction_type=='deposit')
                                    {{trans_choice('general.deposit',1)}}
                                @endif
                                @if($key->transaction_type=='withdrawal')
                                    {{trans_choice('general.withdrawal',1)}}
                                @endif
                                @if($key->transaction_type=='bank_fees')
                                    {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                                @endif
                                @if($key->transaction_type=='specified_due_date_fee')
                                    {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                                @endif
                                @if($key->transaction_type=='interest')
                                    {{trans_choice('general.interest',1)}}
                                @endif
                                @if($key->transaction_type=='dividend')
                                    {{trans_choice('general.dividend',1)}}
                                @endif
                                @if($key->transaction_type=='guarantee_restored')
                                    {{trans_choice('general.guarantee_restored',2)}}
                                @endif
                                @if($key->transaction_type=='fees_payment')
                                    {{trans_choice('general.fee',2)}} {{trans_choice('general.payment',1)}}
                                @endif
                                @if($key->transaction_type=='transfer_loan')
                                    {{trans_choice('general.transfer',1)}} {{trans_choice('general.loan',1)}}
                                @endif
                                @if($key->transaction_type=='transfer_savings')
                                    {{trans_choice('general.transfer',1)}} {{trans_choice('general.savings',2)}}
                                @endif
                                @if($key->reversed==1)
                                    @if($key->reversal_type=="user")
                                        <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                    @if($key->reversal_type=="system")
                                        <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                @endif
                            </td>
                            <td>{{number_format($key->debit,$decimals)}}</td>
                            <td>{{number_format($key->credit,$decimals)}}</td>
                            <td>{{$key->date}}</td>
                            <td>
                                @if(!empty($key->payment_detail))
                                    @if(!empty($key->payment_detail->type))
                                        {{$key->payment_detail->type->name}}
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>{{number_format($total_debit,$decimals)}}</b></td>
                        <td><b>{{number_format($total_credit,$decimals)}}</b></td>
                        <td colspan="2"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection

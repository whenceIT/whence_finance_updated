@extends('layouts.master')
@section('title')
    {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}
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
                                    <a href="{{url('report/loan_report/repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/repayments_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/repayments_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
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
                            class="style-1"> {{trans_choice('general.loan',1)}}  {{trans_choice('general.repayment',2)}}</td>
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
                        <td>{{trans_choice('general.loan',1)}}#</td>
                        <td>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}} </td>
                        <td>{{trans_choice('general.principal',1)}}</td>
                        <td>{{trans_choice('general.interest',1)}}</td>
                        <td>{{trans_choice('general.fee',2)}}</td>
                        <td>{{trans_choice('general.penalty',2)}}</td>
                        <td>{{trans_choice('general.total',1)}}</td>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{trans_choice('general.channel',1)}}</td>
                    </tr>
                    <?php
                    $total_principal = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    $decimals = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        if (!empty($key->loan)) {
                            $decimals = $key->loan->decimals;
                        } else {
                            $decimals = 0;
                        }
                        $principal = $key->principal_derived;
                        $interest = $key->interest_derived;
                        $fees = $key->fees_derived;
                        $penalty = $key->penalty_derived;
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_penalty = $total_penalty + $penalty;

                        ?>
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                @if(!empty($key->loan))
                                    @if($key->loan->client_type=="client")
                                        @if(!empty($key->loan->client))
                                            @if($key->loan->client->client_type=="individual")
                                                {{$key->loan->client->first_name}} {{$key->loan->client->middle_name}} {{$key->loan->client->last_name}}
                                            @endif
                                            @if($key->loan->client->client_type=="business")
                                                {{$key->loan->client->full_name}}
                                            @endif
                                        @endif
                                    @endif
                                    @if($key->loan->client_type=="group")
                                        @if(!empty($key->loan->group))
                                            {{$key->loan->group->name}}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>{{$key->loan->id}}</td>
                            <td>
                                @if(!empty($key->loan))
                                    @if(!empty($key->loan->loan_officer))
                                        {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                                    @endif
                                @endif
                            </td>
                            <td>{{number_format($principal,$decimals)}}</td>
                            <td>{{number_format($interest,$decimals)}}</td>
                            <td>{{number_format($fees,$decimals)}}</td>
                            <td>{{number_format($penalty,$decimals)}}</td>
                            <td>{{number_format($principal+$interest+$fees+$penalty,$decimals)}}</td>
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
                        <td colspan="4"></td>
                        <td><b>{{number_format($total_principal,$decimals)}}</b></td>
                        <td><b>{{number_format($total_interest,$decimals)}} YOU'RE HERE</b></td>
                        <td><b>{{number_format($total_fees,$decimals)}}</b></td>
                        <td><b>{{number_format($total_penalty,$decimals)}}</b></td>
                        <td>
                            <b>{{number_format($total_principal+$total_interest+$total_fees+$total_penalty,$decimals)}}</b>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection

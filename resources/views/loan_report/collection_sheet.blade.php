@extends('layouts.master')
@section('title')
    {{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 1556pt
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
           
            background-color: #339933
        }

        .style-10 {
            width: 50px;
            height: 50px
        }

        .style-11 {
            border-top: 1pt solid black
        }

        .style-2 {
            color: black;
            padding-right: 1pt;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-3 {
            color: black;
            padding-right: 1pt;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-4 {
            background-color: #cccccc
        }

        .style-5 {
            color: black;
            font-size: 10pt;
            font-family: serif;
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
            background-color: #cccccc
        }

        .style-6 {
            color: black;
            font-size: 10pt;
            font-family: serif;
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            white-space: pre-wrap
        }

        .style-7 {
            color: black;
            font-size: 10pt;
            font-family: serif;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            border-bottom: 1pt solid #999999
        }

        .style-8 {
            border-bottom: 1pt solid #999999
        }

        .style-9 {
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

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}
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
                                    <a href="{{url('report/loan_report/collection_sheet/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/collection_sheet/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/collection_sheet/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_officer_id='.$loan_officer_id)}}"
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

                <table class="table  table-condensed table-hover">

                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="12" valign="middle"
                            class="style-1"> {{trans_choice('general.collection',1)}}  {{trans_choice('general.sheet',1)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
                        <td valign="middle" class="style-3">{{$start_date}}</td>
                        <td colspan="2" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="3" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
                        <td colspan="5"></td>
                    </tr>
                    <tr style="height: 45pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
                        <td valign="middle" class="style-3">{{$end_date}}</td>
                        <td colspan="10"></td>
                    </tr>
                    <tr class="">
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                        <th>{{trans_choice('general.office',1)}}</th>
                        <th>{{trans_choice('general.client',1)}}</th>
                        <th>{{trans_choice('general.phone',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.outstanding',1)}}</th>
                        <th>{{trans_choice('general.due',1)}}</th>
                        <th>{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.expected',1)}}  {{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.actual',1)}}  {{trans_choice('general.repayment',1)}}</th>

                    </tr>
                    <?php
                    $total_outstanding = 0;
                    $total_due = 0;
                    $total_expected = 0;
                    $total_actual = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $allocation = \App\Helpers\GeneralHelper::loan_arrears($key->loan_id, $key->due_date);
                        $due = $allocation["amount"];
                        $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->loan_id);
                        $expected = $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->penalty - $key->penalty_waived - $key->penalty_written_off + $key->fees - $key->fees_waived - $key->fees_written_off;
                        $actual = $key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid;
                        $total_outstanding = $total_outstanding + $balance;
                        $total_due = $total_due + $due;
                        $total_expected = $total_expected + $expected;
                        $total_actual = $total_actual + $actual;
                        ?>

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
                            <td><a href="{{url('loan/'.$key->id.'/show')}}">{{$key->loan_id}}</a></td>
                            <td>
                                @if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif
                            </td>
                            <td>{{$key->expected_maturity_date}}</td>
                            <td>{{number_format($balance,$key->decimals)}}</td>
                            <td>{{number_format($due,$key->decimals)}}</td>
                            <td>{{$key->due_date}}</td>
                            <td>{{number_format($expected,$key->decimals)}}</td>
                            <td>{{number_format($actual,$key->decimals)}}</td>


                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{number_format($total_outstanding,2)}}</b></td>
                        <td><b>{{number_format($total_due,2)}}</b></td>
                        <td></td>
                        <td><b>{{number_format($total_expected,2)}}</b></td>
                        <td><b>{{number_format($total_actual,2)}}</b></td>
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

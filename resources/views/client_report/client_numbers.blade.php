@extends('layouts.master')
@section('title')
    {{trans_choice('general.client',1)}} {{trans_choice('general.number',2)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 676pt
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
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: italic;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-11 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 1pt solid black
        }

        .style-12 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 1pt solid black
        }

        .style-13 {
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

        .style-14 {
            width: 50px;
            height: 50px
        }

        .style-15 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: serif;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-16 {
            color: #2f2c35;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-2 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-3 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-4 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-5 {
            color: white;
            padding-left: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-6 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-7 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-8 {
            border-top: 1pt solid black
        }

        .style-9 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: italic;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.client',1)}} {{trans_choice('general.number',2)}}
                @if(!empty($end_date))
                    as at: <b> {{$end_date}}</b>
                @endif
            </h3>

            <div class="heading-elements">

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
                                    <a href="{{url('report/client_report/client_numbers/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/client_report/client_numbers/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/client_report/client_numbers/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
    @if(!empty($end_date))
        <div class="box box-primary">
            <div class="box-body table-responsive">

                <table cellspacing="0" cellpadding="0" class="style-0">

                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="12" valign="middle"
                            class="style-1">  {{trans_choice('general.client',2)}} {{trans_choice('general.report',1)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td colspan="3" valign="middle"
                            class="style-4">{{trans_choice('general.from',1)}} {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }}  {{trans_choice('general.to',1)}} {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</td>
                        <td>
                        <td colspan="9" valign="middle" class="style-3">
                        </td>
                    </tr>
                    <tr style="height: 20pt">
                        <td colspan="2" valign="middle" class="style-5">{{trans_choice('general.office',1)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-5">{{trans_choice('general.registered',1)}} {{trans_choice('general.prospect',1)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-5">{{trans_choice('general.total',1)}} {{trans_choice('general.client',2)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-5">{{trans_choice('general.total',1)}} {{trans_choice('general.funded',1)}} {{trans_choice('general.client',2)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-5">{{trans_choice('general.new',1)}} {{trans_choice('general.client',2)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-5">{{trans_choice('general.repeat',1)}} {{trans_choice('general.client',2)}}</td>
                    </tr>
                    <?php
                    $total_registered_prospects = 0;
                    $total_total_clients = 0;
                    $total_new_clients = 0;
                    $total_funded_clients = 0;
                    $total_repeat_clients = 0;
                    ?>
                    @foreach(\App\Models\Office::all() as $key)
                        <?php
                        $dr = 0;
                        $cr = 0;
                        $balance = 0;
                        $registered_prospects = \App\Models\Client::where('status', 'active')->where('office_id', $key->id)->whereNOTIn('id', function ($query) {
                            $query->select('client_id')->from('loans');
                        })->count();
                        $total_clients = \App\Models\Client::where('status', 'active')->where('office_id', $key->id)->count();
                        $new_clients = \App\Models\Client::where('status', 'active')->where('office_id', $key->id)->whereIn('id', function ($query) use ($start_date, $end_date) {
                            $query->select('client_id')->from('loans')->whereBetween('disbursement_date', [$start_date, $end_date]);
                        })->whereNotIn('id', function ($query) use ($start_date, $end_date) {
                            $query->select('client_id')->from('loans')->where('disbursement_date', '<', $start_date);
                        })->count();
                        $repeat_clients = \App\Models\Client::where('status', 'active')->where('office_id', $key->id)->whereIn('id', function ($query) use ($start_date, $end_date) {
                            $query->select('client_id')->from('loans')->whereBetween('disbursement_date', [$start_date, $end_date]);
                        })->whereIn('id', function ($query) use ($start_date, $end_date) {
                            $query->select('client_id')->from('loans')->where('disbursement_date', '<', $start_date);
                        })->count();
                        $funded_clients = \App\Models\Client::where('status', 'active')->where('office_id', $key->id)->whereIn('id', function ($query) use ($start_date, $end_date) {
                            $query->select('l.client_id')->from('loans as l')->join("loan_repayment_schedules as lr", "l.id", '=', "lr.loan_id")->where('disbursement_date','<=', $end_date)->groupBy("l.id")->havingRaw('(COALESCE(SUM(lr.principal),0)+COALESCE(SUM(lr.interest),0)+COALESCE(SUM(lr.fees),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) >0')->distinct();
                        })->count();
                        $total_registered_prospects = $total_registered_prospects + $registered_prospects;
                        $total_total_clients = $total_total_clients + $total_clients;
                        $total_new_clients = $total_new_clients + $new_clients;
                        $total_repeat_clients = $total_repeat_clients + $repeat_clients;
                        $total_funded_clients = $total_funded_clients + $funded_clients;

                        ?>
                        <tr style="height: 15pt">
                            <td colspan="2" valign="middle" class="style-3">{{ $key->name }}</td>
                            <td colspan="2" valign="middle" class="style-3">{{ $registered_prospects }}</td>
                            <td colspan="2" valign="middle" class="style-3">{{ $total_clients }}</td>
                            <td colspan="2" valign="middle" class="style-3">{{ $funded_clients }}</td>
                            <td colspan="2" valign="middle" class="style-3">{{ $new_clients }}</td>
                            <td colspan="2" valign="middle" class="style-3">{{ $repeat_clients }}</td>
                        </tr>
                    @endforeach
                    <tr style="height: 2pt">
                        <td class="style-8" colspan="2"></td>
                        <td class="style-8" colspan="2">{{ $total_registered_prospects }}</td>
                        <td class="style-8" colspan="2">{{ $total_total_clients }}</td>
                        <td class="style-8" colspan="2">{{ $total_funded_clients }}</td>
                        <td class="style-8" colspan="2">{{ $total_new_clients }}</td>
                        <td class="style-8" colspan="2">{{ $total_repeat_clients }}</td>
                    </tr>
                    </tbody>

                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                //$("body").addClass('sidebar-xs');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection

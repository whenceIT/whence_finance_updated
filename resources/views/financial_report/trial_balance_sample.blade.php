@extends('layouts.master')
@section('title')
    {{trans_choice('general.trial_balance',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.trial_balance',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
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
                                    <a href="{{url('report/financial_report/trial_balance/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
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
        <div class="panel panel-white">
            <div class="panel-body table-responsive no-padding">

                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr class="bg-green">
                        <th>{{trans_choice('general.gl_code',1)}}</th>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.opening',1)}} {{trans_choice('general.balance',1)}}</th>
                        <th>{{trans_choice('general.debit',1)}}</th>
                        <th>{{trans_choice('general.credit',1)}}</th>
                        <th>{{trans_choice('general.closing',1)}} {{trans_choice('general.balance',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_debit_balance = 0;
                    $total_credit_balance = 0;
                    $total_opening_balance = 0;
                    $total_closing_balance = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $dr = 0;
                        $cr = 0;
                        $opening_cr = 0;
                        $opening_dr = 0;
                        $debit_balance = 0;
                        $credit_balance = 0;
                        $opening_balance = 0;
                        $closing_balance = 0;
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                            if ($office_id != 0) {
                                $query->where('id', '=', $office_id);
                            }
                        })->whereBetween('date',
                            [$start_date, $end_date])->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                        }
                        $opening_journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                            if ($office_id != 0) {
                                $query->where('id', '=', $office_id);
                            }
                        })->where('date', '<', $start_date)->get();
                        foreach ($opening_journals as $journal) {
                            $opening_cr = $opening_cr + $journal->credit;
                            $opening_dr = $opening_dr + $journal->debit;
                        }
                        if ($key->account_type == "asset" || $key->account_type == "expense") {
                            //debit balance
                            $debit_balance = $dr - $cr;
                            $opening_balance = $opening_dr - $opening_cr;
                            $closing_balance = $opening_balance + $debit_balance;
                        }
                        if ($key->account_type == "liability" || $key->account_type == "equity" || $key->account_type == "income") {
                            //debit balance
                            $credit_balance = $cr - $dr;
                            $opening_balance = $opening_cr - $opening_dr;
                            $closing_balance = $opening_balance + $debit_balance;
                        }
                        
                        $opening_balance =  $opening_dr-$opening_cr;
                        $closing_balance = $opening_balance + $debit_balance;
                        $total_debit_balance = $total_debit_balance + $debit_balance;
                        $total_credit_balance = $total_credit_balance + $credit_balance;
                        $total_opening_balance = $total_opening_balance + $opening_balance;
                        $total_closing_balance = $total_closing_balance + $closing_balance;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>{{ number_format($opening_balance,2) }}</td>
                            <td>{{ number_format($debit_balance,2) }}</td>
                            <td>{{ number_format($credit_balance,2) }}</td>
                            <td>{{ number_format($closing_balance,2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2"><b>{{trans_choice('general.total',1)}}</b></td>
                        <th>{{number_format($total_opening_balance,2)}}</th>
                        <th>{{number_format($total_debit_balance,2)}}</th>
                        <th>{{number_format($total_credit_balance,2)}}</th>
                        <th>{{number_format($total_closing_balance,2)}}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection

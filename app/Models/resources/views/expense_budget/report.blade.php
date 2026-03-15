@extends('layouts.master')
@section('title')
    {{trans_choice('general.budget',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 1315pt
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
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #f2f1f1
        }

        .style-11 {
            border-top: 2pt solid black
        }

        .style-12 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-bottom: 2pt solid black
        }

        .style-13 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-bottom: 2pt solid black
        }

        .style-14 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-bottom: 2pt solid black;
            background-color: #cccccc
        }

        .style-15 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-bottom: 2pt solid black;
            background-color: #f2f1f1
        }

        .style-16 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-17 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

        .style-18 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #f2f1f1
        }

        .style-19 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black
        }

        .style-2 {
            color: black;
            padding-right: 5pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-20 {
            color: black;
            padding-right: 2pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black
        }

        .style-21 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            background-color: #cccccc
        }

        .style-16 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-bottom: 2pt solid black;
            background-color: #f2f1f1
        }

        .style-23 {
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

        .style-24 {
            width: 50px;
            height: 50px
        }

        .style-3 {
            color: #2f2c35;
            padding-right: 5pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-4 {
            color: #2f2c35;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-5 {
            color: black;
            padding-right: 2pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }
        .style-5b {
            color: black;
            padding-right: 2pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }
        .style-6 {
            color: black;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-7 {
            color: black;
            padding-right: 2pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
           
        }

        .style-8 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-9 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.budget',1)}} {{trans_choice('general.report',1)}}
                @if(!empty($year))
                    for year: <b> {{$year}}</b>
                @endif
            </h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="year"
                           class="control-label col-md-2">{{trans_choice('general.year',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="year" class="form-control year-picker"
                               value="{{$year}}"
                               required id="year" data-max="{{date("Y")}}">
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
                                    <a href="{{url('expense/budget/report/pdf?year='.$year.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('expense/budget/report/excel?year='.$year.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('expense/budget/report/csv?year='.$year.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
            @php
    $expenseTypes = \App\Models\ExpenseType::all();
@endphp



        </div>

    </div>
    <!-- /.box -->
    @if(!empty($year))
        <div class="box box-primary">
            <div class="panel-body table-responsive ">
                <table cellspacing="0" cellpadding="0" class="style-0">

                    <tbody>
                    <tr style="height: 25pt" class="style-1">
                        <td colspan="29"> {{trans_choice('general.budget',1)}} {{trans_choice('general.report',1)}}</td>
                    </tr>
                    <tr style="height: 13pt">
                        <td colspan="4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="3"> {{date("Y-m-d H:i:s")}}</td>
                        <td colspan="22"></td>
                    </tr>
                    <tr style="height: 23pt">
                        <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.office',1)}} :</td>
                        <td colspan="3" valign="middle" class="style-7">
                            @if($office_id!=0)
                                {{\App\Models\Office::find($office_id)->name}}
                            @endif
                        </td>
                        <td colspan="23"></td>
                    </tr>
                    <tr style="height: 23pt">
                        <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.year',1)}} :</td>
                        <td colspan="3" valign="middle" class="style-7">{{$year}}</td>
                        <td colspan="23"></td>
                    </tr>
                    <tr style="height: 13pt">
                        <td valign="middle" class="style-16">#</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.type',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.january',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.february',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.march',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.april',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.may',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.june',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.july',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.august',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.september',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.october',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.november',1)}}</td>
                        <td valign="middle" class="style-16" colspan="2">{{trans_choice('general.december',1)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                    </tr>
                    <tr style="height: 13pt">
                        <td valign="middle" class="style-16" colspan="3"></td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td class="style-16">{{trans_choice('general.budget',1)}}</td>
                        <td class="style-16">{{trans_choice('general.expense',2)}}</td>
                        <td valign="middle" class="style-16" colspan="2"></td>
                    </tr>
                    <?php
                    $total_year_budget = 0;
                    $total_year_expenses = 0;
                    $total_jan_budget = 0;
                    $total_jan_expenses = 0;
                    $total_feb_budget = 0;
                    $total_feb_expenses = 0;
                    $total_mar_budget = 0;
                    $total_mar_expenses = 0;
                    $total_apr_budget = 0;
                    $total_apr_expenses = 0;
                    $total_may_budget = 0;
                    $total_may_expenses = 0;
                    $total_jun_budget = 0;
                    $total_jun_expenses = 0;
                    $total_jul_budget = 0;
                    $total_jul_expenses = 0;
                    $total_aug_budget = 0;
                    $total_aug_expenses = 0;
                    $total_sep_budget = 0;
                    $total_sep_expenses = 0;
                    $total_oct_budget = 0;
                    $total_oct_expenses = 0;
                    $total_nov_budget = 0;
                    $total_nov_expenses = 0;
                    $total_dec_budget = 0;
                    $total_dec_expenses = 0;
                    $total_budget = 0;
                    $total_expenses = 0;
                    $count = 0;
                    ?>
                   @php $count = 1; @endphp
@foreach($data as $expense_type_id => $entries)
    @php
        $expense_type_name = $entries->first()->type->name ?? 'Unknown';
        $monthly_budgets = [];
        $monthly_expenses = [];

        // Init monthly budget & expense placeholders (Jan to Dec)
        foreach (range(1, 12) as $m) {
            $month = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthly_budgets[$month] = $entries->where('month', $month)->sum('amount');

            $monthly_expenses[$month] = \App\Models\Expense::query()
    ->where('year', $year)
    ->where('month', $month)
    ->where('expense_type_id', $expense_type_id)
    ->when($office_id, fn($q) => $office_id != 0 ? $q->where('office_id', $office_id) : $q)
    ->sum('amount');

        }

        $year_budget = array_sum($monthly_budgets);
        $year_expenses = array_sum($monthly_expenses);

        $total_budget += $year_budget;
        $total_expenses += $year_expenses;
    @endphp

    <tr style="height: 13pt">
        <td class="style-5">{{ $count++ }}</td>
        <td class="style-5" colspan="2">{{ $expense_type_name }}</td>

        @foreach(range(1, 12) as $m)
    @php
        $month = str_pad($m, 2, '0', STR_PAD_LEFT);
        $budget = $monthly_budgets[$month];
        $expense = $monthly_expenses[$month];

        // Add to monthly totals
        switch ($month) {
            case '01':
                $total_jan_budget += $budget;
                $total_jan_expenses += $expense;
                break;
            case '02':
                $total_feb_budget += $budget;
                $total_feb_expenses += $expense;
                break;
            case '03':
                $total_mar_budget += $budget;
                $total_mar_expenses += $expense;
                break;
            case '04':
                $total_apr_budget += $budget;
                $total_apr_expenses += $expense;
                break;
            case '05':
                $total_may_budget += $budget;
                $total_may_expenses += $expense;
                break;
            case '06':
                $total_jun_budget += $budget;
                $total_jun_expenses += $expense;
                break;
            case '07':
                $total_jul_budget += $budget;
                $total_jul_expenses += $expense;
                break;
            case '08':
                $total_aug_budget += $budget;
                $total_aug_expenses += $expense;
                break;
            case '09':
                $total_sep_budget += $budget;
                $total_sep_expenses += $expense;
                break;
            case '10':
                $total_oct_budget += $budget;
                $total_oct_expenses += $expense;
                break;
            case '11':
                $total_nov_budget += $budget;
                $total_nov_expenses += $expense;
                break;
            case '12':
                $total_dec_budget += $budget;
                $total_dec_expenses += $expense;
                break;
        }
    @endphp
    <td class="style-5b">{{ number_format($budget, 2) }}</td>
    <td class="style-5b">{{ number_format($expense, 2) }}</td>
@endforeach


        <td class="style-5">{{ number_format($year_budget, 2) }}</td>
        <td class="style-5" @if($year_expenses > $year_budget) style="color: red" @endif>
            {{ number_format($year_expenses, 2) }}
        </td>
    </tr>
@endforeach

                    <tr style="height: 13pt">
                        <td class="style-16" colspan="3"></td>
                        <td class="style-16">{{number_format($total_jan_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_jan_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_feb_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_feb_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_mar_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_mar_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_apr_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_apr_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_may_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_may_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_jun_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_jun_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_jul_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_jul_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_aug_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_aug_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_sep_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_sep_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_oct_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_oct_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_nov_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_nov_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_dec_budget,2)}}</td>
                        <td class="style-16">{{number_format($total_dec_expenses,2)}}</td>
                        <td class="style-16">{{number_format($total_budget,2)}}</td>
                        @if($total_year_expenses>$total_year_budget)
                            <td class="style-16" style="color: red">{{number_format($total_expenses,2)}}</td>
                        @else
                            <td class="style-16">{{number_format($total_expenses,2)}}</td>
                        @endif

                    </tr>
                    </tbody>
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

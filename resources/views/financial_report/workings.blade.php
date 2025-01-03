@extends('layouts.master')
@section('title')
    Workings
@endsection
@section('content')
<style>
    .style-0 {
        empty-cells: show;
        table-layout: fixed;
        width: 1400px;
    }

    table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
<?php
$years = [];
$year_index = [];
$period_value = ['01','02','03','04','05','06','07','08','09','10','11','12'];
$period_names = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$period_index = [0,1,2,3,4,5,6,7,8,9,10,11];
$current_year = date('Y-m-d');
for($x=0; $x<10; $x++){
    if($x != 0){
    $current_year = date('Y-m',strtotime($current_year. ' - 1 year'));
    }
    array_push($years,$current_year);
    array_push($year_index,$x);

}


?>
<div class="box box-primary">
<div class="box-header with-border">
            <h3 class="box-title">
             Workings
            </h3>
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="end_date"
                           class="control-label col-md-2">Year</label>

                           <div class="col-md-3">
        <select name="year" class="form-control select2" id="year" required>
                            <option></option>
                            @foreach($year_index as $year)
                                <option value="{{date('Y',strtotime($years[$year]))}}">{{date('Y',strtotime($years[$year]))}}</option>
                            @endforeach
                        </select>
        </div>
        </div>
   
       <div class="form-group">
       <label for="end_date"
        class="control-label col-md-2">Start Period</label>

        <div class="col-md-3">
        <select name="start_month" class="form-control select2" id="start_month" required>
                            <option></option>
                            @foreach($period_index as $period)
                                <option value="{{$period_value[$period]}}">{{$period_names[$period]}}</option>
                            @endforeach
                        </select>


        </div>

       </div>


       <div class="form-group">
       <label for="end_date"
        class="control-label col-md-2">End Period</label>

        <div class="col-md-3">
        <select name="end_month" class="form-control select2" id="end_month" required>
                            <option></option>
                            @foreach($period_index as $period)
                                <option value="{{$period_value[$period]}}">{{$period_names[$period]}}</option>
                            @endforeach
                        </select>


        </div>

       </div>
       




       <div class="form-group">
       <label for=""
       class="control-label col-md-2"></label>
       <div class="col-md-3">
        <button type="submit" class="btn btn-success">Go!
        </button>
        </div>
</div>

                    

  

            </form>

        </div>
</div>
<?php
$total = 0;
$total2 = 0;
$revenue_total = 0;
$cost_total = 0;
$expense_total = 0;
$revenues = [];
$expense_type_list = [];
$distribution_expense_type_list = [];
$expense_type_totals = [];
$monthly_expense_total = [];
$array_of_totals = [];
$distribution_array_of_totals = [];
$expense_index = [];
$distribution_expense_index = [];
$num = 0;
$dnum = 0;
$out = 0;
$in = 0;
$unrecovered_montly_total = 0;
$monthly_unrecovered_amounts = [];
$unrecovered_total = 0;
$first_total = 0;
$cost_of_sales = [];
$gross_profits = [];
$monthly_totals = [];
$distribution_monthly_totals = [];
$final_expense_total = 0;
$final_distribution_expense_total = 0;
$expenses_totals = [];
$final_diferences = [];
$difference = [];
$distribution_expenses_totals = [];
$months = ['January','February','March','April','May','June','July','August','September','October','November','December',];
$month_dates = [$year_use.'-'.'01',$year_use.'-'.'02',$year_use.'-'.'03',$year_use.'-'.'04',$year_use.'-'.'05',$year_use.'-'.'06',$year_use.'-'.'07',$year_use.'-'.'08',$year_use.'-'.'09',$year_use.'-'.'10',$year_use.'-'.'11',$year_use.'-'.'12'];
$todaysDate = date('Y-m-d');
$random = date('m',strtotime($todaysDate));
    foreach($month_dates as $date){
        foreach($loans as $loan){
            if(date('Y-m',strtotime($loan->created_date)) == $date){
                foreach($loan->transactions as $transaction){
                    if($transaction->transaction_type == 'disbursement'){
                                $interest = $transaction->debit*0.4;
                             $total = $total + $transaction->debit + $interest;
                               $total2 = $total2 + $transaction->debit;
                            }

                     if($transaction->transaction_type == 'interest_initial'){
                 $total = $total + $transaction->debit/0.4 + $transaction->debit;
                 $total2 = $total2 + $transaction->debit/0.4;
             }


                }
            }
        }

     
            array_push($gross_profits,($total - $total2));
            array_push($revenues,$total);
            array_push($cost_of_sales,$total2);

        $total = 0;
        $total2 = 0;


 
    }

    foreach($revenues as $revenue){
        $revenue_total = $revenue_total + $revenue;
    }

    foreach($cost_of_sales as $sale){
        $cost_total = $cost_total + $sale;
    }

    $r_n_c_total = $revenue_total - $cost_total;


  foreach($expense_types as $expense_type){
    foreach($month_dates as $date){
            foreach($expenses as $expense){
                if($expense->expense_type_id == $expense_type->id && date('Y-m',strtotime($expense->date)) == $date){
                    $expense_total = $expense_total + $expense->amount;
                }
            }

            array_push($monthly_expense_total,$expense_total);
            $expense_total = 0;
        }
      if($expense_type->distribution_cost !== 1){
        array_push($expense_type_list,$expense_type->name);
        array_push($expense_index,$num);
        $num =  $num + 1;
        array_push($array_of_totals,$monthly_expense_total);
      }else{
        array_push($distribution_expense_type_list,$expense_type->name);
        array_push($distribution_expense_index,$dnum);
        $dnum = $dnum + 1;  
        array_push($distribution_array_of_totals,$monthly_expense_total);
      }
      //  array_push($expense_index,$num);
      $monthly_expense_total = []; 
   
           
        }

$s = 0;


for($x=0; $x<12; $x++){
    foreach($array_of_totals as $totals){
         
            $s = $s + $totals[$x];
        }
        array_push($monthly_totals,$s);
        $s = 0;


    foreach($distribution_array_of_totals as $totals){
             
            $s = $s + $totals[$x];
        }
        array_push($distribution_monthly_totals,$s);
        $s = 0;
}




$s2 = 0;    

foreach($array_of_totals as $totals){
    foreach($totals as $total){
        $s2 = $s2 + $total;
    }
    array_push($expenses_totals,$s2);
    $s2 = 0;
}

foreach($distribution_array_of_totals as $totals){
    foreach($totals as $total){
        $s2 = $s2 + $total;
    }
    array_push($distribution_expenses_totals,$s2);
    $s2 = 0;
}





foreach($expenses_totals as $item){
    $final_expense_total = $final_expense_total + $item;
}

foreach($distribution_expenses_totals as $item){
    $final_distribution_expense_total = $final_distribution_expense_total + $item;
}


for($x=0; $x<12; $x++){
    $difference = $gross_profits[$x] - $monthly_totals[$x] - $distribution_monthly_totals[$x];
    array_push($final_diferences,$difference); 
    $difference = 0;
}


      foreach($month_dates as $date){
    foreach($unrecovered_loans as $loan){
        if(date('Y-m',strtotime($loan->first_repayment_date)) == $date){
            foreach($loan->transactions as $transaction){
                if($transaction->transaction_type == 'disbursement'){
                    $original_balance = $transaction->debit + ($transaction->debit*0.4);
                }
                $out = $out + $transaction->debit;
                $in = $in + $transaction->credit;
            }
            $current_balance = $out - $in;
            if($current_balance < $original_balance){
                $unrecovered_montly_total = $unrecovered_montly_total + $current_balance;
            }
            $out = 0;
            $in = 0;
            $current_balance = 0;
        }
    }
    array_push($monthly_unrecovered_amounts,$unrecovered_montly_total);
     $unrecovered_total = $unrecovered_total + $unrecovered_montly_total;
    $unrecovered_montly_total = 0;
}

$first_total = $unrecovered_total * pow(1.05,7);


	//TABLE 2
$table_2_row_1 = ['Revenue Breakdown','','','Penalty 5%/7 days',''];
$table_2_row_2 = ['Revenue Components','','(A)','INFLATED @ 10.95% (B)','B-A'];
$table_2_row_3 = ['Unrecoverable debts','',$expenses_totals[7],$expenses_totals[7]*1.1095,($expenses_totals[7]*1.1095)-($expenses_totals[7])];
$table_2_row_4 = ['Sub-total (C = B-A)','','','',($expenses_totals[7]*1.1095)-($expenses_totals[7])];
$table_2_row_5 = ['Revenue without penalties (D)','','','',$revenue_total];
$table_2_row_6 = ['Revenue with penalties subject to defaulted amounts ( C + D )','','','',($revenue_total + ($expenses_totals[7]*1.1095)-($expenses_totals[7]))]

?>
@if(!empty($year_use))

<div class="panel panel-white">

<a href="{{ url('report/financial_report/statement_of_comp_income') }}" style="margin: 10px;">

   <span class="label label-primary" style="font-size: 15px;">STATEMENT OF COMP. INCOME</span>
</a>
  
    <div class="panel-body table-responsive">
    <p style="font-weight: bold;">{{$start_date}} to {{$end_date}} Financial Statements Workings</p>
        <table class="style-0 ">
            <tbody>
                <!-- First Line -->
                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold"></td>
                </tr>

               <!-- Second Line -->
                <tr>
                    <td colspan="5"></td>
                    @foreach($months as $month)
                    <td colspan="2">{{$month}}</td>
                    @endforeach
                    <td colspan="2">Totals</td>
                </tr>
             
                <tr>
                    <td colspan="5">Revenue (Working 1)</td>
                    @foreach($revenues as $revenue)
                    <td colspan="2" style="color: blue;">{{number_format($revenue,2)}}</td>
                    @endforeach
                    <td colspan="2" style="color: blue;">{{number_format($revenue_total,2)}}</td>
                    
                </tr>

                <tr>
                    <td colspan="5">Cost of sales (Working 3)</td>
                    @foreach($cost_of_sales as $sale)
                    <td colspan="2" style="color: orange;">{{number_format($sale,2)}}</td>
                    @endforeach
                    <td colspan="2" style="color: orange;">{{number_format($cost_total,2)}}</td>
                </tr>

                <tr>
                    <td colspan="5">Gross Profit</td>
                    @foreach($gross_profits as $gross_profit)
                    <td colspan="2" style="color: green;">{{number_format($gross_profit,2)}}</td>
                    @endforeach
                    <td colspan="2" style="color: green;">{{number_format($r_n_c_total,2)}}</td>
                </tr>

                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>

                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold">Administrative Expenses</td>
                </tr>
                @foreach($expense_index as $index)
                <tr>
                    <td colspan="5">{{$expense_type_list[$index]}}</td>

                    @foreach($array_of_totals[$index] as $total)
                    <td colspan="2">{{$total}}</td>
                    @endforeach


              <td colspan="2">{{$expenses_totals[$index]}}</td>


                </tr>
                @endforeach  


                <tr>
                    <td colspan="5">Total</td>
                    @foreach($monthly_totals as $mt)
                    <td colspan="2">{{$mt}}</td>
                    @endforeach
                    <td colspan="2">{{$final_expense_total}}</td>

                </tr>

                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>


                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold">Distribution Cost</td>
                </tr>
                @foreach($distribution_expense_index as $index)
                <tr>
                    <td colspan="5">{{$distribution_expense_type_list[$index]}}</td>

                    @foreach($distribution_array_of_totals[$index] as $total)
                    <td colspan="2">{{$total}}</td>
                    @endforeach


              <td colspan="2">{{$distribution_expenses_totals[$index]}}</td>


                </tr>
                @endforeach  


                <tr>
                    <td colspan="5">Total</td>
                    @foreach($distribution_monthly_totals as $mt)
                    <td colspan="2">{{$mt}}</td>
                    @endforeach
                    <td colspan="2">{{$final_distribution_expense_total}}</td>

                </tr>

                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>

                <tr>
                   <td colspan="5">Total</td>
                    @foreach($final_diferences as $differences)
                    <td colspan="2">{{number_format($differences,2)}}</td>
                    @endforeach
                    <td colspan="2">{{number_format($r_n_c_total - $final_expense_total - $final_distribution_expense_total,2)}}</td>
                </tr>


 <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>
                <tr>
                    <td colspan="31" valign="middle" style="font-weight:bold">Receivables</td>
                </tr>
                <tr>
                    <td colspan="5">Unrecovered Balance after part-payment</td>
                    @foreach($monthly_unrecovered_amounts as $amount)
                    <td colspan="2">{{number_format($amount,2)}}</td>
                    @endforeach
		</tr>


  <tr>
                    <td colspan="5">TOTAL {{number_format($first_total,2)}}</td>
                    @foreach($months as $month)
                    <td colspan="2"></td>
                    @endforeach
                    <td colspan="2">{{number_format($first_total * 1.228,2)}}</td>
                </tr>

 
            </tbody>
        </table>



    </div>





  <div class="panel-body table-responsive">
    <table class="style-0 margin-top:100px;">
            <tbody>
                <tr>
                    @foreach($table_2_row_1 as $row)
                    <td colspan="2" style="font-weight:bold">{{$row}}</td>
                    @endforeach
                </tr>
                <tr>
                @foreach($table_2_row_2 as $row)
                    <td colspan="2" style="font-weight:bold">{{$row}}</td>
                @endforeach
                </tr>

                <tr>
                @foreach($table_2_row_3 as $row)
                    <td colspan="2">{{$row}}</td>
                @endforeach
                </tr>

                <tr>
                    <td colspan="2" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>

                <tr>
                @foreach($table_2_row_4 as $row)
                    <td colspan="2" style="font-weight:bold">{{$row}}</td>
                @endforeach
                </tr>

                <tr>
                    <td colspan="2" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>

                <tr>
                @foreach($table_2_row_5 as $row)
                    <td colspan="2" style="font-weight:bold">{{$row}}</td>
                @endforeach
                </tr>

                <tr>
                    <td colspan="2" valign="middle" style="font-weight:bold; height: 20px"></td>
                </tr>

                <tr>
                @foreach($table_2_row_6 as $row)
                    <td colspan="2" style="font-weight:bold">{{$row}}</td>
                @endforeach
                </tr>

            </tbody>
        </table>
    </div>


</div>
@endif
@endsection

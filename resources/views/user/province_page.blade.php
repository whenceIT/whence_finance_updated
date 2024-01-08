@extends('layouts.master')
@section('title')
{{$province->name}}
@endsection
@section('content')
<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">

<a href="{{ url('loan/collections') }}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Collections</span>
</a>

<a href="javascript:;" onmousedown="toggleMyStaff('mydiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">Branches</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>



</div>
<div id='mydivon' style="display:block">
<div class="row">
<?php
$use = date('Y-m-');
$todaysDate = date('Y-m-d');
$newTodaysDate =  date('Y-m-d', strtotime($todaysDate. ' + 3 months'));
$branchtargetDate = $use.'24';
$branchtargetDate = date('Y-m-d',strtotime($branchtargetDate));

if($todaysDate > $branchtargetDate){
    $branchtargetDate = date('Y-m-d',strtotime($branchtargetDate. ' + 1 months'));
}
$branchcompareDate = date('Y-m-d',strtotime($branchtargetDate. ' - 1 months'));
$branchzeroDate = date('Y-m-d', strtotime($branchtargetDate. ' - 3 months'));
$branchfirstDate = date('Y-m-d', strtotime($branchtargetDate. ' - 2 months'));
$provincefirstDate = date('Y-m-d', strtotime($branchtargetDate. ' - 2 months'));
$branchsecondDate = date('Y-m-d', strtotime($branchtargetDate. ' - 1 months'));


//BRANCH CYCLE OPENING UNCOLLECTED VARIABLES
$MoneyGivenOut = 0;
$MoneyCollected = 0;
$charges = 0;
$cycle_opening_uncollected_amount = 0;


//BRANCH COLLECTED TOTAL CALCULATIONS
$collected_total = 0;
$full_payments = 0;
$part_payments = 0;
$reloan_payments = 0;
$reloan_amount = 0;

// BRANCH COLLECTED TOTAL CALCULATIONS 1 MONTH AGO
$collected_total_1_months = 0;
$reloan_amount_1_months = 0;
$full_payments_1_months  = 0;
$part_payments_1_months  = 0;
$reloan_payments_1_months  = 0;

//BRANCH COLLECTED TOTAL CALCULATIONS 2 MONTHS AGO
$collected_total_2_months = 0;
$reloan_amount_2_months = 0;
$full_payments_2_months  = 0;
$part_payments_2_months  = 0;
$reloan_payments_2_months  = 0;

//GIVEN OUT AMOUNTS
$given_out_total = 0;
$new_loans_given_out = 0;
$reloans_given_out = 0;

//COUNTS
$bar_chart_count = 0;
$collections_count_fullpayment = 0;
$collections_count_reloan = 0;
$collections_count_partpayment = 0;
$givenout_count_newloan = 0;
$givenout_count_reloan = 0;
$trans_id = 0;
$trans_id_int = 0;

// foreach($province_loans as $province_loan){
//     foreach($province_loan->transactions as $transaction){
//         array_push($province_transactions, $transaction);
//     }
// }
?>

<!-- CALCULATION LOAN BALANCES FOR BRANCH CYCLE OPENING UNCOLLECTED -->
@foreach($province_loans as $loan)
<?php
$MoneyCollected = 0;
$MoneyGivenOut = 0;
$charges = 0;
$balance = 0;
?>
@foreach($loan->transactions as $transaction)

<?php

if($transaction->date <= $branchcompareDate){
    $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
}

if($transaction->transaction_type != 'interest_waiver' && $transaction->date <= $branchcompareDate){
    $MoneyCollected = $MoneyCollected + $transaction->credit;
}


if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $branchcompareDate){
    $charges = $charges + $transaction->debit;
}



?>
@endforeach
<?php 
$balance = ($MoneyGivenOut - $MoneyCollected - $charges);
if($balance < 0){
    $balance = 0;
}
$cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
if($cycle_opening_uncollected_amount == 0){
    $cycle_opening_uncollected_amount = 1;
}
?>
@endforeach

<?php 
$collections = [];
$given_out = [];
$target_dates = [];
while($bar_chart_count < 12){


    $branchtargetDateAlgo = date('Y-m-d',strtotime($branchtargetDate. ' - '. $bar_chart_count.'months'));
    $branchcompareDateAlgo = date('Y-m-d',strtotime($branchcompareDate. ' - '. $bar_chart_count.'months'));
    $reloan_payments = 0;
    $reloan_amount = 0;
    $full_payments = 0;
    $part_payments = 0;
    $new_loans_given_out = 0;
    $reloans_given_out = 0;
    $interest = 0;
    $principal = 0;
   // {{date("jS M, Y", strtotime($compareDate))}} 
    array_push($target_dates,date("jS M, Y",strtotime($branchtargetDateAlgo)));

    foreach($province_transactions as $transaction){
        if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $full_payments = $full_payments + $transaction->credit;
            if($bar_chart_count == 4){
                $collections_count_fullpayment = $collections_count_fullpayment + 1;
            }
        }
        
        if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $part_payments = $part_payments + $transaction->credit;
            if($bar_chart_count == 4){
                $collections_count_partpayment = $collections_count_partpayment + 1;
            }
        }
        
        if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
        
            $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
            $interest = $transaction->credit/0.4;
            $reloan_payments = $reloan_payments + $reloan_amount + $interest; 
            if($bar_chart_count == 4){
                $collections_count_reloan = $collections_count_reloan + 1;
            }
        }

        if($transaction->transaction_type == 'disbursement' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $new_loans_given_out = $new_loans_given_out + $transaction->debit;
            if($bar_chart_count == 4){
                $givenout_count_newloan = $givenout_count_newloan + 1;
            }
        }
        
        if($transaction->transaction_type == 'interest' && $transaction->date > $branchcompareDateAlgo  && $transaction->date <= $branchtargetDateAlgo){
            $principal = $transaction->debit/0.4;
            $reloans_given_out = $reloans_given_out + $principal;
            if($bar_chart_count == 4){
                $givenout_count_reloan = $givenout_count_reloan + 1;
            }
        }
    }
    $collected_total = $reloan_payments + $part_payments + $full_payments;
    $given_out_total = $new_loans_given_out + $reloans_given_out;
    array_push($collections,$collected_total);
    array_push($given_out,$given_out_total);
    // $collections = array_reverse($collections);
    // $given_out = array_reverse($given_out);
    $bar_chart_count = $bar_chart_count + 1;


}
?>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Province cycle opening uncollected amount</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($cycle_opening_uncollected_amount,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Province total cycle collected amount (TCC)</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($collections[0],2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Province total cycle given out</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($given_out[0] ,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>





<canvas id='provincegraph'></canvas>

<div class="row" style="padding-top: 20px;">



<div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Money collected</h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                        <canvas id="myChart"></canvas>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Money given out</h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                        <canvas id="myOtherChart"></canvas>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

</div>

</div>

<div>

</div>


</div>







<div id='mydivoff' style="display:none">
@foreach($province_branches as $branch)
<a href="{{url('user/'.$branch->id.'/branch_page')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-user-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$branch->name}}</span>
</div>
</div>
</div>
</a>
@endforeach
</div>
@endsection
@section('footer-scripts')
<script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/gauge.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/funnel.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}"
            type="text/javascript"></script>
            <script>
                function toggleMyStaff(divid){
    varon = divid + 'on';
    varoff = divid + 'off';
 
    if(document.getElementById(varon).style.display == 'block')
    {
    document.getElementById(varon).style.display = 'none';
    document.getElementById(varoff).style.display = 'block';
    }
   
    else
    {  
    document.getElementById(varoff).style.display = 'none';
    document.getElementById(varon).style.display = 'block'
    }
}    

const provincemonths = ["January","February","March","April","May","June","July","August","September","October","November","December"];
let provincetargetDateName = provincemonths[new Date('{{$branchtargetDate}}').getMonth()]
//var provinceCycleDate = document.getElementById('branchCycle').innerHTML = '24th ' + provincetargetDateName
  let provincefirstDateName = provincemonths[new Date('{{$branchfirstDate}}').getMonth()];
  let provincesecondDateName = provincemonths[new Date('{{$branchsecondDate}}').getMonth()]

  var collections =  
    <?php echo json_encode($collections); ?>; 
    var given_out =  
    <?php echo json_encode($given_out); ?>; 
    console.log(collections.reverse())
    console.log(given_out.reverse())

    var dates =  
    <?php echo json_encode($target_dates); ?>; 
    console.log(dates.reverse())
    
const chrt = document.getElementById('provincegraph');
var chartId = new Chart(chrt, {
         type: 'bar',
         data: {
            labels: dates,
            datasets: [{
               label: "Collections as at end of cycle date",
               data: collections,
               borderWidth: 1,
            },
            {
               label: "Given out as at end of cycle date",
               data: given_out,
               borderWidth: 1,
            },
        ],
         },
         options: {
            scales: {
      y: {
        beginAtZero: true
      }
    }
         },
      });


var xValues = ["Full and Part Payments", "Reloans"];
var yValues = ['{{$collections_count_partpayment + $collections_count_fullpayment}}','{{$collections_count_reloan}}'];
var barColors = [
  "#F77FBE",
  "#87CEEB",
];
var otherxValues = ["New Loans", "Reloans"];
var otheryValues = ['{{$givenout_count_newloan}}','{{$givenout_count_reloan}}'];

new Chart("myChart", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "World Wide Wine Production 2018"
    }
  }
});

new Chart("myOtherChart", {
  type: "pie",
  data: {
    labels: otherxValues,
    datasets: [{
      backgroundColor: barColors,
      data: otheryValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "World Wide Wine Production 2018"
    }
  }
});
            </script>
@endsection
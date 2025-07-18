<?php
use App\Models\LoanTransaction;
?>
@extends('layouts.master')
@section('title')
    Dashboard
@endsection

@section('content')
<!-- What Admins see -->
@if($role->role_id == '1')
<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">
 



<a href="{{ url('loan/collections') }}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Collections</span>
</a>

<a href="javascript:;" onmousedown="toggleMyStaff('mydiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">Provinces</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>


<a href="javascript:;" onmousedown="toggleCollections('mydiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">Total Collections breakdown</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>

<a href="{{ url('user/daily_figures')}}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Daily figures</span>
</a>

</div>

<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">
<p style="font-weight: bold;">Data based on loans created in the last 12 months</p>
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
$cycle_opening_uncollected_amount = 0.0001;


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
$reloans_given_out_not_exp = 0;
$new_loans_given_out_not_exp = 0;
$given_out_total_not_exp = 0;

//COUNTS
$bar_chart_count = 0;
$collections_count_fullpayment = 0;
$collections_count_reloan = 0;
$collections_count_partpayment = 0;
$givenout_count_newloan = 0;
$givenout_count_reloan = 0;
$given_out_count_total = 0;
$trans_id = 0;
$trans_id_int = 0;

//COLLECTIONS TODAY
$full_payments_today = 0;
$part_payments_today = 0;
$reloan_payments_today = 0;
$add = 0;
$he = [];

$trans = []
// foreach($province_loans as $province_loan){
//     foreach($province_loan->transactions as $transaction){
//         array_push($province_transactions, $transaction);
//     }
// }
?>

<!-- CALCULATION LOAN BALANCES FOR BRANCH CYCLE OPENING UNCOLLECTED -->
@foreach($allLoans as $loan)
<?php
$MoneyCollected = 0;
$MoneyGivenOut = 0;
$charges = 0;
$balance = 0;
?>

@foreach($loan->transactions as $transaction)

<?php
    if($transaction->date <= $branchcompareDate && $transaction->transaction_type != 'specified_due_date_fee'){
        $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
    }
    
    if($transaction->date <= $branchcompareDate){
        $MoneyCollected = $MoneyCollected + $transaction->credit;
    }
    
    
    // if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $branchcompareDate){
    //     $charges = $charges + $transaction->debit;
    // }
?>

@endforeach

<?php 
$balance = ($MoneyGivenOut - $MoneyCollected);
// if($balance < 0){
//     $balance = 0;
// }
$cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
// if($cycle_opening_uncollected_amount == 0){
//     $cycle_opening_uncollected_amount = 1;
// }
?>
@endforeach



<?php 
$collections = [];
$given_out = [];
$given_out_not_exp = [];
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
    $disbursement_interest = 0;
    $interest_today = 0;
    $reloan_amount_today = 0;
    $principal = 0;
    $reloans_given_out_not_exp = 0;
    $new_loans_given_out_not_exp = 0;
    $full_payments_today = 0;
    $part_payments_today = 0;
    $reloan_payments_today = 0;
   // {{date("jS M, Y", strtotime($compareDate))}} 
    array_push($target_dates,date("jS M, Y",strtotime($branchtargetDateAlgo)));

    foreach($allTransactions as $transaction){
        
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
            $reloan_payments = $reloan_payments + $reloan_amount ; 
            array_push($trans,$transaction);
            if($bar_chart_count == 4){
                $collections_count_reloan = $collections_count_reloan + 1;
            }
        }

        if($transaction->transaction_type == 'disbursement' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $disbursement_interest = $transaction->debit/0.4;
            $new_loans_given_out = $new_loans_given_out + $transaction->debit + $disbursement_interest;
            $new_loans_given_out_not_exp = $new_loans_given_out_not_exp + $transaction->debit;

            if($bar_chart_count == 4){
                $givenout_count_newloan = $givenout_count_newloan + 1;
            }
        }
        
        if($transaction->transaction_type == 'interest' && $transaction->date > $branchcompareDateAlgo  && $transaction->date <= $branchtargetDateAlgo){
            $principal = $transaction->debit/0.4;
            $reloans_given_out = $reloans_given_out + $principal + $transaction->debit;
            $reloans_given_out_not_exp = $reloans_given_out_not_exp + $principal;
            if($bar_chart_count == 4){
                $givenout_count_reloan = $givenout_count_reloan + 1;
            }
        }


        if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date == $todaysDate){
            $full_payments_today = $full_payments_today + $transaction->credit;
        }
        
        if($transaction->payment_apply_to == 'part_payment' && $transaction->date == $todaysDate){
            $part_payments_today = $part_payments_today + $transaction->credit;
        }


        if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date == $todaysDate){
            $reloan_amount_today = $transaction->credit; //+ ($transaction->credit/0.4);
            $interest_today = $transaction->credit/0.4;
            $reloan_payments_today = $reloan_payments_today + $reloan_amount_today; 
            
        }



    }
    $given_out_total_not_exp = $new_loans_given_out_not_exp + $reloans_given_out_not_exp;
    $collected_total = $reloan_payments + $part_payments + $full_payments;
    $given_out_total = $new_loans_given_out + $reloans_given_out;
    array_push($given_out_not_exp,$given_out_total_not_exp);
    array_push($collections,$collected_total);
    array_push($given_out,$given_out_total);
    $bar_chart_count = $bar_chart_count + 1;

}
?>

<?php
$total_given_out = 0;
$total_collected = 0;
$default_value = 0;
foreach($given_out as $given){
  $total_given_out = $total_given_out + $given;
}

foreach($collections as $collected){
  $total_collected = $total_collected + $collected;
}

$default_value = $total_given_out - $total_collected;
?>



<?php 
 foreach($allTransactions as $transaction){
    
    if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date == $todaysDate){
        $full_payments_today = $full_payments_today + $transaction->credit;
    }
    
    if($transaction->payment_apply_to == 'part_payment' && $transaction->date == $todaysDate){
        $part_payments_today = $part_payments_today + $transaction->credit;
    }


    if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date == $todaysDate){
        $reloan_amount_today = $transaction->credit; + ($transaction->credit/0.4);
        $interest_today = $transaction->credit/0.4;
        $reloan_payments_today = $reloan_payments_today + $reloan_amount_today; 
    }
 }


 $pdua = ($collections[0]/$cycle_opening_uncollected_amount)
?>

<p>GIVEN OUT: {{number_format($total_given_out,2)}}</p>
<p>COLLECTED: {{number_format($total_collected,2)}}</p>
<p>DEFAULTED: {{number_format($default_value,2)}}</p>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Total cycle opening uncollected amount</p>
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
<p style="font-weight: bold;">Total cycle collected amount (TCC)</p>
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
<p style="font-weight: bold;">Total cycle given out</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($given_out_not_exp[0] ,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Total collected today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($full_payments_today + $part_payments_today + $reloan_payments_today,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Part and Full payments collected today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($full_payments_today + $part_payments_today,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Reloans collected today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($reloan_payments_today,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


</div>


<div style="margin-bottom:30px; margin-top:30px;">
<p style="display: flex;
    align-items: center;
    justify-content: center; font-size:50px;">PDUA%</p>
<div style="display: flex;
    align-items: center;
    justify-content: center;">
    
<div class="gauge" style="width: 100%;
  max-width: 250px;
  font-size: 50px;
  color: #004033;">
    <div class="gauge__body" style=" width: 100%;
  height: 0;
  padding-bottom: 50%;
  background: #b4c0be;
  position: relative;
  border-top-left-radius: 100% 200%;
  border-top-right-radius: 100% 200%;
  overflow: hidden;">

@if(($pdua) < 0.75)
 <div class="gauge__fill" style=" position: absolute;
  top: 100%;
  left: 0;
  width: inherit;
  height: 100%;
  background: red;
  transform-origin: center top;
  transform: rotate(0.25turn);
  transition: transform 0.2s ease-out;"></div>

@elseif(($pdua) >= 0.90)
<div class="gauge__fill" style=" position: absolute;
  top: 100%;
  left: 0;
  width: inherit;
  height: 100%;
  background:#d4af37;
  transform-origin: center top;
  transform: rotate(0.25turn);
  transition: transform 0.2s ease-out;"></div>

@else
<div class="gauge__fill" style=" position: absolute;
  top: 100%;
  left: 0;
  width: inherit;
  height: 100%;
  background:green;
  transform-origin: center top;
  transform: rotate(0.25turn);
  transition: transform 0.2s ease-out;"></div>

@endif
    <div class="gauge__cover" style="width: 75%;
  height: 150%;
  background: #f7f7f7;
  border-radius: 50%;
  position: absolute;
  top: 25%;
  left: 50%;
  transform: translateX(-50%);

  /* Text */
  display: flex;
  align-items: center;
  justify-content: center;
  padding-bottom: 25%;
  box-sizing: border-box;"></div>

    </div>
</div>


</div>

<div style="display:flex; flex-direction:row; justify-content:space-between;
    align-items: center;
    justify-content: center;">
<div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
<div style="background-color: red;  height: 10px;
  width: 20px;">
</div>
<p style="text-align: center; font-weight:bold;">Poor</p>
</div>

<div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
<div style="background-color: green;  height: 10px;
  width: 20px;">
</div>
<p style="text-align: center; font-weight:bold;">Fair</p>
</div>
<div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
<div style="background-color: #d4af37;  height: 10px;
  width: 20px;">
</div>
<p style="text-align: center; font-weight:bold;">Good</p>
</div>

</div>
</div>


<canvas id='companygraph'></canvas>
<canvas id='companybargraph'></canvas>

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



<div>

</div>


</div>



<div id='mydivoff' style="display:none">
@foreach($provinces as $province)
<a href="{{url('user/'.$province->id.'/province_page')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-user-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$province->name}}</span>
</div>
</div>
</div>
</a>
@endforeach
</div>

<div id='mydivofff' style="display: none;">
<div class="box box-primary">
    <div id='mydivoff_new'  class="box-body table-responsive" >
    <div class="box-header with-border">
            <h3 class="box-title">Transactions as at start of cycle<a href="javascript:;" onmousedown="toggleLedger('mydiv');">
            <span style="font-size: 15px; padding-left: 10px;">COUA breakdown</span>
        </a></h3>
        </div>
        <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Name</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($allTransactions as $transaction)
            @if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDateAlgo &&  $transaction->date <= $branchtargetDateAlgo && $transaction->credit)
         <tr>
    <td></td>
    <td></td>
    <td></td>
    <td>{{number_format($transaction->credit,2)}}</td>
    <td></td>
    <td></td>
</tr>       
@endif
@endforeach
</tbody>
            </table>


        </div>
    </div>
</div>


@endif


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
    @if(!Sentinel::inRole('client'))
        <script>
         
  
            
        </script>
        @endif
@if($role->role_id == '1')
<script>

const gaugeElementAdmin = document.querySelector(".gauge");

function setGaugeValue(gauge, value) {
  if (value < 0 || value > 1) {
    return;
  }

  gauge.querySelector(".gauge__fill").style.transform = `rotate(${
    value / 2
  }turn)`;
  gauge.querySelector(".gauge__cover").textContent = `${Math.round(
    value * 100
  )}%`;
}

setGaugeValue(gaugeElementAdmin, '{{($pdua)}}');

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

function toggleCollections(divid){
    console.log('hello')
    varon = divid + 'on';
    varoff = divid + 'offf';
 
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


const companymonths = ["January","February","March","April","May","June","July","August","September","October","November","December"];
let companytargetDateName = companymonths[new Date('{{$branchtargetDate}}').getMonth()]
let companyfirstDateName = companymonths[new Date('{{$branchfirstDate}}').getMonth()];
let companysecondDateName = companymonths[new Date('{{$branchsecondDate}}').getMonth()]

var collections =  
    <?php echo json_encode($collections); ?>; 
    var given_out =  
    <?php echo json_encode($given_out); ?>; 
    console.log(collections.reverse() + 'hey')
    console.log(given_out.reverse())

    var dates =  
    <?php echo json_encode($target_dates); ?>; 
    console.log(dates.reverse())

    const allchrt = document.getElementById('companygraph');
    const newChart = document.getElementById('companybargraph')


	       var chartI = new Chart(newChart, {
         type: 'bar',
         data: {
            labels: dates,
            datasets: [{
               label: "collections as at end of each cycle",
               data: collections,
               borderWidth: 1,
            },
            {
               label: "given out as at end of each cycle",
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

var chartId = new Chart(allchrt, {
         type: 'line',
         data: {
            labels: dates,
            datasets: [{
               label: "Actual Collections",
               data: collections,
               borderWidth: 1,
            },
            {
               label: "Expected Collections",
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
 



var given_out_total = "{{($collections_count_reloan/(0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan))*100}}"
console.log(given_out_total)

var xValues = ["Monthly Full and Part Payments %", "Monthly Reloans %"];
var yValues = ['{{(($collections_count_partpayment + $collections_count_fullpayment)/(0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan))*100}}','{{($collections_count_reloan/(0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan))*100}}'];
var barColors = [
  "#F77FBE",
  "#87CEEB",
];
var otherxValues = ["Monthly New Loans %", "Monthly Reloans %"];
var otheryValues = ['{{($givenout_count_newloan/(0.001 + $givenout_count_newloan + $givenout_count_reloan))*100}}','{{($givenout_count_reloan/(0.001 + $givenout_count_newloan + $givenout_count_reloan))*100}}'];

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
    }
  }
});



</script>

@endif

@endsection

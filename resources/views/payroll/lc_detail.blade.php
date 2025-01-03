@extends('layouts.master')
@section('title')
    LC Information
@endsection
@section('content')
<h2 style="display: flex;
    align-items: center;
    justify-content: center;">{{ $user->first_name}} {{ $user->last_name}}</h2>
<?php

$todaysDate = date('Y-m-d');
$use = date('Y-m-');
$num = 24;
$targetDate = $use.$num;
$targetDate = date('Y-m-d',strtotime($targetDate));
if($todaysDate > $targetDate){
    $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
}
$compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));
$new_loans_cycle = 0;
$new_reloans_cycle = 0;
$target_total_cycle = 0;
$cycle_full_payments = 0;
$cycle_part_payments = 0;
$cycle_interest = 0;
$cycle_reloan_amount = 0;
$cycle_reloan_payments = 0;
$cycle_opening_uncollected_amount = 0;
$MoneyCollected = 0;
$MoneyGivenOut = 0;
$charges = 0;
$balance = 0;
$target_amount = 0;
$new_loans = 0;
$reloans = 0;
$target_count = 0;
$month_count = 0;
$new_loans_cycle = 0;
$new_reloans_cycle = 0;
$target_total_cycle = 0;
$cycle_reloan_payments = 0;
$cycle_full_payments = 0;
$cycle_part_payments = 0;
$cycle_collected_total = 0;
$MoneyCollected = 0;
$MoneyGivenOut = 0;
$charges = 0;
$balance = 0;
$targets = [];
$target_dates = [];
$target_count = 0;
$projected_salary = 4000;
foreach($loans as $object){
    foreach($object->transactions as $transaction){
        if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
            $new_loans_cycle = $new_loans_cycle + $transaction->debit;
        }

        if($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
            $principal = $transaction->debit/0.4;
            $new_reloans_cycle = $new_reloans_cycle + $principal;
        }
}

foreach($object->transactions as $transaction){
    if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
        $cycle_full_payments = $cycle_full_payments + $transaction->credit;
    }
    
    if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
        $cycle_part_payments = $cycle_part_payments + $transaction->credit;
    }
    
    if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
    
        $cycle_reloan_amount = $transaction->balance_bf; //+ ($transaction->credit/0.4);
        $cycle_interest = $transaction->credit/0.4;
        $cycle_reloan_payments = $cycle_reloan_payments + $cycle_reloan_amount;
    }
}


foreach($object->transactions as $transaction){
    if($transaction->date <= $compareDate && $transaction->transaction_type != 'specified_due_date_fee'){
        $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
    }
    
    if($transaction->date <= $compareDate){
        $MoneyCollected = $MoneyCollected + $transaction->credit;
    }
    
    
}

}

for($x=0; $x<24; $x++){
    $target = date('Y-m-d',strtotime($targetDate. ' - '. $x.'months'));
    $compare = date('Y-m-d',strtotime($compareDate. ' - '. $x.'months'));
    foreach($loans as $object){
        foreach($object->transactions as $transaction){
            if($transaction->transaction_type == 'disbursement' && $transaction->date > $compare && $transaction->date <= $target){
                $new_loans = $new_loans + $transaction->debit;
            }

            if($transaction->transaction_type == 'interest' && $transaction->date > $compare && $transaction->date <= $target){
                $principal = $transaction->debit/0.4;
                $reloans = $reloans + $principal;
            }
    }    
}

$target_amount = $new_loans + $reloans;
array_push($targets,$target_amount);
array_push($target_dates,date("jS M", strtotime($target)));
if($target_amount > 40000){
    $target_count = $target_count + 1;
}
// $target_count = $target_count + 1;
  $new_loans = 0;
  $reloans = 0;
  $target_amount = 0;
}



$balance = $MoneyGivenOut - $MoneyCollected;
$target_total_cycle = $new_loans_cycle + $new_reloans_cycle;
$cycle_collected_total = $cycle_full_payments + $cycle_part_payments + $cycle_reloan_payments;
$uncollected = $balance - $cycle_collected_total;
if($target_count >= 3){
    $projected_salary = 5000;
    if($target_total_cycle >= 40000 && $uncollected < 5000){
        $projected_salary = 7000;
    }
}

?>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">cycle opening uncollected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($balance,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">total cash collected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($cycle_collected_total)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">total cash still uncollected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($balance - $cycle_collected_total)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-purple">
<div class="inner">
<p style="font-weight: bold;">total loans given out</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($target_total_cycle)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Projected salary</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($projected_salary)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<canvas id='graph'></canvas>

@endsection
@section('footer-scripts')
<script>
const ctx = document.getElementById('graph');
var target_dates =  
    <?php echo json_encode($target_dates); ?>; 
    target_dates.reverse()

    var targets = 
    <?php echo json_encode($targets); ?>; 
    targets.reverse()

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: target_dates,
    datasets: [{
      label: 'LC targets in the last 12 months',
      data: targets,
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
</script>
@endsection

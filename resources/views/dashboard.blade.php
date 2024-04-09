@extends('layouts.master')
@section('title')
    Dashboard
@endsection

@section('content')
<!-- What Clients see -->
@if($role->role_id == '2')
<div>
    @if(!($clientLoan))
<div class="row">
<div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold; ">Name: {{$user->first_name}} {{$user->last_name}}</p>
    </div>
    <div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold;">Branch: {{$clientBranch->name}}</p>
    </div>
    <div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold;">Loan Consultant: {{$staff->first_name}} {{$staff->last_name}}</p>
    </div>

    <div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Outstanding balance</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>0.00</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Due date</p>
<div class="icon">
<i class="fa fa-calendar-o"></i>
</div>
<h3>-</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Paid</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>0.00</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
   
</div>
    @else
    <div class="row">
    <div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold; ">Name: {{$user->first_name}} {{$user->last_name}}</p>
    </div>
    <div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold;">Branch: {{$clientBranch->name}}</p>
    </div>
    <div class="col-lg-4 col-xs-12">
    <p style="text-align: center; font-weight:bold;">Loan Consultant: {{$staff->first_name}} {{$staff->last_name}}</p>
    </div>
<?php 
$balance = 0;;
$in = 0;
$out = 0;
?>
    @foreach($clientLoan->transactions as $transaction)
    <?php 
    if($transaction->transaction_type != 'specified_due_date_fee'){
        $out = $out + $transaction->debit;
    }

    $in = $in + $transaction->credit;
    ?>
    @endforeach
    <?php 
$balance = $out - $in;
?>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Outstanding balance</p>
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
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Due date</p>
<div class="icon">
<i class="fa fa-calendar-o"></i>
</div>
<h3>{{date("jS M, Y",strtotime($clientLoan->expected_first_repayment_date))}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Paid</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($in,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

    </div>

    @endif
  

</div>
@endif


<!-- What Loan Consultants see -->
@if($role->role_id == '3')


<div></div>

<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px;">


<a href="{{ url('loan/my_collections') }}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Collections</span>
</a>
    
<a href="javascript:;" onmousedown="toggleDiv('mydiv'); ">
<span class="label label-primary" style="font-size: 15px;">COUA and TCC breakdown</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>
</div>


<!-- Default Dashboard -->
<div id='mydivon' style="display:block">
@if($end == 'NCI')
<div style="display: flex;
    align-items: center;
    justify-content: center;">
<a href="{{ url('user/cycle') }}" class="btn btn-info btn-sm">
Set your cycle end date 
</a>
</div>
@else

<!-- first row -->
<div class="row">
<!-- cycle countdown -->

<?php
$use = date('Y-m-');
$todaysDate = date('Y-m-d');
$newTodaysDate =  date('Y-m-d', strtotime($todaysDate. ' + 3 months'));
$targetDate = $use.'24';
$targetDate = date('Y-m-d',strtotime($targetDate));
$cycle_opening_uncollected_amount_debit = 0;
$cycle_opening_uncollected_amount_credit = 0;
$disbursed_amount = 0;
$debit = 0;
$credit = 0;
$test = 0;
$testTwo = 0;
$testThree = 0;
$d_debit = 0;
$d_credit = 0;
$total_cycle_collected_amount = 0;
$num = 0;
$item = 0;
$charges = 0;
$out = 0;
$MoneyGivenOut = 0;
$MoneyCollected = 0;
$charges = 0;
$newout = 0;
$in = 0;
$added = 5;
/// Target varibales
$target_monthly = 0;
$target_reloan = 0;
$target_total = 0;
///
$reloanAmount = 0;
$ppAmount = 0;
$reloan_amount = 0;
$cycle_opening_uncollected_amount = 0;
$full_payments = 0;
$part_payments = 0;
$reloan_payments = 0;
/////// 1 month ago
$reloan_amount_1_months = 0;
$full_payments_1_months  = 0;
$part_payments_1_months  = 0;
$reloan_payments_1_months  = 0;
////// 2 months ago
$reloan_amount_2_months = 0;
$full_payments_2_months  = 0;
$part_payments_2_months  = 0;
$reloan_payments_2_months  = 0;
/////
$pre_reloan = 0;
$collected_total = 0;
$collected_total_1_months = 0;
$collected_total_2_months = 0;
$firstAmount = 0;
$secondAmount = 0;
if($todaysDate > $targetDate){
    $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
}
$compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));
$zeroDate = date('Y-m-d', strtotime($targetDate. ' - 3 months'));
$firstDate = date('Y-m-d', strtotime($targetDate. ' - 2 months'));
$secondDate = date('Y-m-d', strtotime($targetDate. ' - 1 months'));
$transID = 0;
$transAmount = 0;
$reloanID = 0;
$reloanTransAmount = 0;
$still_uncollected_today = 0;
?>

<!-- CALCULATION LOAN BALANCES FOR CYCLE OPENING UNCOLLECTED -->
@foreach($myLoans as $loan)
<?php
$MoneyCollected = 0;
$MoneyGivenOut = 0;
$charges = 0;
$balance = 0;
$OutIn = 0;
$out = 0;
$in = 0;
?>
@foreach($loan->transactions as $transaction)
<?php
if($transaction->date <= $compareDate && $transaction->transaction_type != 'specified_due_date_fee'){
    $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
}

if($transaction->date <= $compareDate){
    $MoneyCollected = $MoneyCollected + $transaction->credit;
}

if($transaction->transaction_type != 'specified_due_date_fee'){
    $out = $out + $transaction->debit;
}


$in = $in + $transaction->credit;

// if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $compareDate){
//     $charges = $charges + $transaction->debit;
// }

?>
@endforeach
<?php 

$balance = $MoneyGivenOut - $MoneyCollected;
$OutIn = $out - $in;
// if($balance < 0){
//     $balance = 0;
// }
$still_uncollected_today = $still_uncollected_today + $OutIn;
$cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
if($cycle_opening_uncollected_amount == 0){
    $cycle_opening_uncollected_amount = 1;
}
?>
@endforeach

<!-- CALCULATING CURRENT CYCLE COLLECTED USING TRANSACTIONS -->
@foreach($myTransactions as $transaction)
<?php

if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
    $full_payments = $full_payments + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
    $part_payments = $part_payments + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){

    $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments = $reloan_payments + $reloan_amount + $interest;  

}

//COLLECTIONS -1 MONTH
if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $zeroDate && $transaction->date <= $firstDate){
    $full_payments_1_months = $full_payments_1_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $zeroDate && $transaction->date <= $firstDate){
    $part_payments_1_months = $part_payments_1_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $zeroDate && $transaction->date <= $firstDate){

    $reloan_amount_1_months = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments_1_months = $reloan_payments_1_months + $reloan_amount_1_months + $interest;  
}

//COLLECTIONS -2 MONTH
if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $firstDate && $transaction->date <= $secondDate){
    $full_payments_2_months = $full_payments_2_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $firstDate && $transaction->date <= $secondDate){
    $part_payments_2_months = $part_payments_2_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $firstDate && $transaction->date <= $secondDate){

    $reloan_amount_2_months = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments_2_months = $reloan_payments_2_months + $reloan_amount_2_months + $interest;  
}

// if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
//     $target_monthly = $target_monthly + $transaction->debit;
//     $transID = $transaction->loan_id;
//     $transAmount = $transaction->debit;
// }

// if($transaction->transaction_type == 'interest' && $transaction->date > $firstDate && $transaction->date <= $compareDate){
//     $principal = $transaction->debit/0.4;
//     $target_reloan = $target_reloan + $principal;
//     $reloanID = $transaction->loan_id;
//     $reloanTransAmount = $transaction->debit;
// }

//$disbursed_amount = $disbursed_amount + $transaction::where('date','>=',$compareDate)->where('date','<=',$targetDate)->where('date','=',date('Y-m-d',strtotime($loan->first_repayment_date. '- 1 months')))->sum('debit');
?>
<!-- <p>{{$firstDate}}</p> <p>{{$compareDate}}</p> -->
<!-- <p>{{$reloanID}} {{$reloanTransAmount}} {{$transaction->date}}</p>
<p>{{$target_reloan}}</p> -->
<!-- <p>{{$transID}} {{$transAmount}}</p>
<p>{{$target_monthly}}</p> -->
<!-- <p>{{$target_reloan}}</p> -->
<!-- <p>{{$reloanID}} {{$reloanTransAmount}}</p> -->
@endforeach
<?php
$month_count = 3;
$total = 0;
$excess = 0;
$excess_array = [];
$start_amount = 0;
$total_array = [];
while($month_count != 0){
    $total = 0;
    $targetDate_new =  date('Y-m-d',strtotime($targetDate. ' - '. $month_count.'months'));
    $compareDate_new = date('Y-m-d',strtotime($compareDate. ' - '. $month_count.'months'));
    foreach($myLoans as $loan){
        foreach($loan->transactions as $transaction){
            $add_on_1 = 0;
            $add_on_2 = 0;
            if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate_new && $transaction->date <= $targetDate_new){
                $target_monthly = $target_monthly + $transaction->debit;
                $add_on_1 = $transaction->debit;
            }

            
          if($transaction->transaction_type == 'interest' && $transaction->date > $compareDate_new && $transaction->date <= $targetDate_new){
                 $principal = $transaction->debit/0.4;
                 $target_reloan = $target_reloan + $principal;
                 $add_on_2 = $transaction->debit/0.4;
             }

            if($total < 40000){
                $total = $total + $add_on_1 + $add_on_2 + $start_amount;
            }else{
                $excess = $excess + $add_on_1 + $add_on_2;
            }

            $start_amount = 0;
        }
    }
    //Add total to array
    array_push($excess_array,$excess);
    array_push($total_array,$total);
    $total = 0;
    $start_amount = $excess;
    $excess = 0;
    $month_count = $month_count - 1;
 }
?>
<!-- <p>{{$total_array[0]}}</p> -->
<!-- @foreach($total_array as $array_item)
<p>{{$array_item}}</p>
@endforeach

@foreach($excess_array as $excess_item)
<p>{{$excess_item}}</p>
@endforeach -->


@foreach($myOpenLoans as $myOpenLoan)
@foreach($myOpenLoan->transactions as $transaction)
<?php 
$status = 'no';
$transStatus = 'no';
if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
    $target_monthly = $target_monthly + $transaction->debit;
    $transID = $transaction->loan_id;
    $transAmount = $transaction->debit;
    $transStatus = 'yes';
}

//if($transaction->transaction_type == 'interest' && $transaction->date > $firstDate &&  $transaction->date < $compareDate){

if($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
    $principal = $transaction->debit/0.4;
    $target_reloan = $target_reloan + $principal;
    $reloanID = $transaction->loan_id;
    $reloanTransAmount = $transaction->debit/0.4;
    $status = 'yes';
}
?>

<?php 
$transID = 0;
$transAmount = 0;
$reloanID = 0;
$reloanTransAmount = 0;
?>
@endforeach

<!-- <p>{{$transID}} {{$transAmount}}</p>
<p>{{$target_monthly}}</p> -->
@endforeach

<?php 
$collected_total = $reloan_payments + $part_payments + $full_payments;
$collected_total_1_months = $reloan_payments_1_months + $part_payments_1_months + $full_payments_1_months;
$collected_total_2_months = $reloan_payments_2_months + $part_payments_2_months + $full_payments_2_months;
$target_total = $target_monthly + $target_reloan;
?>







<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Cycle opening uncollected amount (COUA)</p>
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
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Still Uncollected Today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($still_uncollected_today,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<!-- <div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Your cycle ends on</p>
<div class="icon">
<i class="fa fa-clock-o"></i>
</div>
<h3 id='target'></h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div> -->



<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Total cycle collected amount (TCC)</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($collected_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>




</div>
<!--second row-->
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

@if(($collected_total/$cycle_opening_uncollected_amount) < 0.75)
 <div class="gauge__fill" style=" position: absolute;
  top: 100%;
  left: 0;
  width: inherit;
  height: 100%;
  background: red;
  transform-origin: center top;
  transform: rotate(0.25turn);
  transition: transform 0.2s ease-out;"></div>

@elseif(($collected_total/$cycle_opening_uncollected_amount) >= 0.90)
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

<div style="margin-bottom:30px; margin-top:30px;">
<div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h2 class=" text-semibold">{{ trans_choice('general.monthly',1) }} {{ trans_choice('general.target',1) }}</h2>
                                    </div>
                                    <div class="progress" data-toggle="tooltip"
                                         title="You're currently at : {{number_format($target_total,2)}}">
<div class="progress-bar progress-bar-success progress-bar-striped active"
                                             style="width: {{($target_total/40000)*100}}% ">
@if($target_total > 40000)
<span>You've reached your target congratulations!!!</span>
@else
<span>{{($target_total/40000)*100}} {{ trans_choice('general.complete',1) }}</span>
@endif
</div>
</div>
                                </div>
                            </div>
                            </div>


<canvas id='graph'></canvas>

@endif

</div>

<div id='mydivoff' style="display:none">
    <div class="box box-primary">
    <div  id='mydivon_new' style="display:block" class="box-body table-responsive">
    <div class="box-header with-border">
            <h3 class="box-title">Loans at end of last cycle<a href="javascript:;" onmousedown="toggleLedger('mydiv');">
            <span style="font-size: 15px; padding-left: 10px;">TCC breakdown</span>
        </a></h3>
        </div>
        <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Name</th>
                    <th>Balance</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $new_out = 0;
                    ?>
@foreach($myLoans as $loan)


                <?php
 $OutIn = 0;
 $out = 0;
 $in = 0;
?>
@foreach($loan->transactions as $transaction)
<?php

if($transaction->transaction_type != 'specified_due_date_fee'){
    $out = $out + $transaction->debit;
}


// if($transaction->date <= $compareDate && $transaction->transaction_type != 'interest_waiver'){
//     $in = $in + $transaction->credit;
// }


    $in = $in + $transaction->credit;

// if($transaction->date <= $compareDate && $transaction->transaction_type == 'specified_due_date_fee'){
//     $newout = $newout + $transaction->debit;
// }

?>
@endforeach

<?php 
$OutIn = $out - $in;
//$OutIn = $OutIn - $newout;
// if($OutIn < 0){
//     $OutIn = 0;
// }

?>

<tr>


    <td><a href="{{ url('loan/'.$loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$loan->id}}</a></td>
    @if(!empty($loan->client->first_name))
    <td>{{$loan->client->first_name}} {{$loan->client->last_name}} 
        @if($loan->defaulted == 'yes')
        <span style="color: red;">(Defaulted)</span>
        @endif
    </td>
    @if($OutIn < 0)
    <td style="color: red;">{{number_format($OutIn,2)}}</td>
    @else
    <td>{{number_format($OutIn,2)}}</td>
    @endif
    @endif
</tr>
@endforeach

                </tbody>
            </table>
    </div>
    </div>
    <div class="box box-primary">
    <div id='mydivoff_new' style="display:none"  class="box-body table-responsive" >
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
                </tr>
                </thead>
                <tbody>
                @foreach($myTransactions as $transaction)
            @if($transaction->transaction_type != 'interest_waiver' && $transaction->date > $compareDate &&  $transaction->date <= $targetDate && $transaction->credit)
         <tr>
    <td>{{$transaction->loan_id}}</td>
    <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
    <td>{{$transaction->payment_apply_to}}</td>
    <td>{{number_format($transaction->credit,2)}}</td>
</tr>       
@endif
@endforeach
</tbody>
            </table>


        </div>
    </div>

</div>


<!-- //GOES HERE -->
</div>

@endif

<!--What managers see-->
@if($role->role_id == '4')

<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">
    

<a href="{{ url('loan/collections') }}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Collections</span>
</a>
   
<a href="javascript:;" onmousedown="toggleDiv('mydiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">COUA and TCC breakdown</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>

<a href="javascript:;" onmousedown="toggleMyStaff('mydiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">Staff information</span>
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
$cycle_opening_uncollected_amount_debit = 0;
$cycle_opening_uncollected_amount_credit = 0;
$disbursed_amount = 0;
$debit = 0; 
$credit = 0;
$branch_total_cycle_collected_amount = 0;
$num = 0;
$item = 0;
$count = 0;
$targetCount = 0;
$sum = 0;
$transac = 0;
$firstAmount = 0;
$collected_total = 0;
$secondAmount = 0;

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

//STAFF CALCULATIONS
$staff_count = 0;

//BRANCH TARGET CALCULATIONS
$target_monthly = 0;
$target_reloan = 0;
$target_total = 0;

if($todaysDate > $branchtargetDate){
    $branchtargetDate = date('Y-m-d',strtotime($branchtargetDate. ' + 1 months'));
}
$branchcompareDate = date('Y-m-d',strtotime($branchtargetDate. ' - 1 months'));
$branchzeroDate = date('Y-m-d', strtotime($branchtargetDate. ' - 3 months'));
$branchfirstDate = date('Y-m-d', strtotime($branchtargetDate. ' - 2 months'));
$branchsecondDate = date('Y-m-d', strtotime($branchtargetDate. ' - 1 months'));
$testcompareDate = date('Y-m-d',strtotime($branchtargetDate. ' - 1 months'));
$testtargetDate
?>

<!-- CALCULATION LOAN BALANCES FOR BRANCH CYCLE OPENING UNCOLLECTED -->
@foreach($newBranchLoans as $loan)
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

<!-- CALCULATING BRANCH CURRENT CYCLE COLLECTED USING TRANSACTIONS -->
@foreach($branchTransactions as $transaction)
<?php 
if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate){
    $full_payments = $full_payments + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate){
    $part_payments = $part_payments + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate){

    $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments = $reloan_payments + $reloan_amount + $interest; 
}


//COLLECTIONS 1 MONTH AGO
if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate){
    $full_payments_1_months = $full_payments_1_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate){
    $part_payments_1_months = $part_payments_1_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate){

    $reloan_amount_1_months = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments_1_months = $reloan_payments_1_months + $reloan_amount_1_months + $interest;  
}

//COLLECTIONS 2 MONTHS AGO
if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate){
    $full_payments_2_months = $full_payments_2_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate){
    $part_payments_2_months = $part_payments_2_months + $transaction->credit;
}

if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate){
    $reloan_amount_2_months = $transaction->credit; + ($transaction->credit/0.4);
    $interest = $transaction->credit/0.4;
    $reloan_payments_2_months = $reloan_payments_2_months + $reloan_amount_2_months + $interest;  
}

?>
@endforeach

<?php
$collected_total = $reloan_payments + $part_payments + $full_payments;
$collected_total_1_months = $reloan_payments_1_months + $part_payments_1_months + $full_payments_1_months;
$collected_total_2_months = $reloan_payments_2_months + $part_payments_2_months + $full_payments_2_months;
?>


@foreach($branchUsers as $branchUser)
<?php
$target_total = 0;
$target_monthly = 0;
$target_reloan = 0;

if($branchUser->role->role_id != 1){
    $staff_count = $staff_count + 1;
}

if($branchUser->cycle_dates != null){
    $end = $branchUser->cycle_dates->cycle_end_date;
}else{
    $end = 1;
}

$branchReferenceTargetDate = $use.'24';
$branchReferenceTargetDate = date('Y-m-d',strtotime($branchReferenceTargetDate));
$targetDate = $use.$end;
$targetDate = date('Y-m-d',strtotime($targetDate));

if($todaysDate > $branchReferenceTargetDate){
    $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
}

$compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));
?>

@foreach($newBranchLoans as $loan)
@foreach($loan->transactions as $transaction)
<?php 
//branch transactions
if($loan->loan_officer_id == $branchUser->id){
    if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate && $transaction->loan_id == $loan->id){
        $target_monthly = $target_monthly + $transaction->debit;
    }
}

if($loan->loan_officer_id == $branchUser->id){
    if($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate && $transaction->loan_id == $loan->id ){
        $principal = $transaction->debit/0.4;
        $target_reloan = $target_reloan + $principal;
    }
}
?>

@endforeach
@endforeach

<?php
$target_total = $target_monthly + $target_reloan;
if($target_total > 40000){
    $targetCount = $targetCount + 1;
}
?>
@endforeach


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Branch cycle ends on</p>
<div class="icon">
<i class="fa fa-clock-o"></i>
</div>
<h3 id='branchCycle'></h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Branch cycle opening uncollected amount</p>
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
<p style="font-weight: bold;">Branch total cycle collected amount (TCC)</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($collected_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>



<!-- 
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-purple">
<div class="inner">
<p style="font-weight: bold;"># of staff</p>
<div class="icon">
<i class="fa fa-users"></i>
</div>
<h3>{{$staff_count}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Targets met</p>
<div class="icon">
<i class="fa fa-trophy"></i>
</div>
<h3>{{$targetCount}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>


<div class="col-lg-4 col-xs-12">
<div class="small-box bg-blue">
<div class="inner">
<p style="font-weight: bold;">Branch efficiency</p>
<div class="icon">
<i class="fa fa-line-chart"></i>
</div>
<h3>{{number_format(($targetCount/$staff_count)*100)}}%</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div> -->
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

@if(($collected_total/$cycle_opening_uncollected_amount) < 0.75)
 <div class="gauge__fill" style=" position: absolute;
  top: 100%;
  left: 0;
  width: inherit;
  height: 100%;
  background: red;
  transform-origin: center top;
  transform: rotate(0.25turn);
  transition: transform 0.2s ease-out;"></div>

@elseif(($collected_total/$cycle_opening_uncollected_amount) >= 0.90)
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

<canvas id='branchgraph'></canvas>

</div>


<div id='mydivstaff' style="display:none">
@foreach($branchUsers as $branchUser)
<a href="{{url('user/'.$branchUser->id.'/staff_info')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-user-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$branchUser->first_name}} {{$branchUser->last_name}}</span>
@if($branchUser->role->role_id == '3')
<p style="font-size: 15px;">Loan Consultant</p>
@elseif($branchUser->role->role_id == '4')
<p>Branch Manager</p>
@else
<p></p>
@endif
</div>
</div>
</div>
</a>
@endforeach
</div>



<div id='mydivoff' style="display:none">
<div class="box box-primary">
<div id='mydivon_new' style="display:block" class="box-body table-responsive">
<div class="box-header with-border">
<h3 class="box-title">Loans at end of last cycle<a href="javascript:;" onmousedown="toggleLedger('mydiv');">
            <span style="font-size: 15px; padding-left: 10px;">TCC breakdown</span>
        </a></h3>
</div>
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Name</th>
    <th>Balance</th>
    </tr>
</thead>
<tbody>
@foreach($newBranchLoans as $loan)
<?php
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
?>
@foreach($loan->transactions as $transaction)
<?php
if($transaction->date <= $branchcompareDate){
    $out = $out + $transaction->debit;
}

if($transaction->date <= $branchcompareDate && $transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}

if($transaction->date <= $branchcompareDate && $transaction->transaction_type == 'specified_due_date_fee'){
    $newout = $newout + $transaction->debit;
}
?>
@endforeach
<?php
$OutIn = $out - $in;
$OutIn = $OutIn - $newout;
if($OutIn < 0){
    $OutIn = 0;
}
?>
<tr>
   @if($OutIn != 0)
    <td>{{$loan->id}}</td>
    <td>
    @if(!empty($loan->client->first_name))
        {{$loan->client->first_name}} 
    @endif
    @if(!empty($loan->client->last_name))
        {{$loan->client->last_name}}
    @endif
        @if($loan->defaulted == 'yes')
        <span style="color: red;">(Defaulted)</span>
        @endif
    </td>
    <td>{{number_format($OutIn,2)}}</td>
    @endif
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

<div class="box box-primary">
<div id='mydivoff_new' style="display:none"  class="box-body table-responsive" >
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
                </tr>
            </thead>
            <tbody>
            @foreach($branchTransactions as $transaction)
            @if($transaction->transaction_type != 'interest_waiver' && $transaction->date > $branchcompareDate &&  $transaction->date <= $branchtargetDate && $transaction->credit)
         <tr>
    <td>{{$transaction->loan_id}}</td>
    <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
    <td>{{$transaction->payment_apply_to}}</td>
    <td>{{number_format($transaction->credit,2)}}</td>
</tr>       
@endif
@endforeach
            </tbody>
        </table>
</div>
</div>
</div>


@endif


<!-- What PMs see -->
@if($role->role_id == '6')
    
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
while($bar_chart_count < 5){


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


@endif


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
$given_out_not_exp = [];
$target_dates = [];
while($bar_chart_count < 1){


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
            $reloan_amount_today = $transaction->credit; + ($transaction->credit/0.4);
            $interest_today = $transaction->credit/0.4;
            $reloan_payments_today = $reloan_payments_today + $reloan_amount_today + $interest_today; 
            
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
                @foreach($trans as $tran)
         <tr>
    <td></td>
    <td></td>
    <td></td>
    <td>{{number_format($tran->credit,2)}}</td>
    <td></td>
    <td></td>
</tr>       
@endforeach
</tbody>
            </table>
<p>{{$reloan_payments}}</p>
<p>{{$part_payments}}</p>
<p>{{$full_payments}}</p>

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
         
   var chart = AmCharts.makeChart("registered_clients_graph", {
                "type": "funnel",
                "theme": "light",
                "dataProvider": {!! \App\Helpers\GeneralHelper::client_numbers_graph() !!},
                "balloon": {
                    "fixedPosition": false
                },
                "valueField": "value",
                "titleField": "title",
                "marginRight": 130,
                "marginLeft": 0,
                "startX": 0,
                "rotate": true,
                "labelPosition": "right",
                "balloonText": "[[title]]: [[value]] [[description]]",
                "export": {
                    "enabled": true,
                    "libs": {
                        "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                    }
                }
            });
            var chart = AmCharts.makeChart("loans_status_graph", {
                "type": "pie",
                "theme": "light",
                "dataProvider": {!! \App\Helpers\GeneralHelper::loans_status_graph() !!},
                "balloon": {
                    "fixedPosition": false
                },
                "valueField": "value",
                "titleField": "title",
                "marginRight": 20,
                "marginLeft": 20,
                "radius": 60,
                "startX": 0,
                "fontSize": 10,
                "rotate": true,
                "labelPosition": "right",
                "balloonText": "[[title]]: [[value]] [[description]]",
                "export": {
                    "enabled": true,
                    "libs": {
                        "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                    }
                },
                legend: {
                    display: true,
                    labels: {
                        fontColor: 'rgb(255, 99, 132)'
                    }
                }
            });
            var chart = AmCharts.makeChart("savings_balance_graph", {
                "type": "serial",
                "theme": "light",
                "dataProvider": {!! \App\Helpers\GeneralHelper::savings_balance_graph() !!},
                "balloon": {
                    "fixedPosition": false
                },
                "startDuration": 1,
                "graphs": [{
                    "balloonText": "[[category]]: <b>[[value]]</b>",
                    "fillAlphas": 0.8,
                    "lineAlpha": 0.2,
                    "type": "column",
                    "valueField": "value"
                }],
                "chartCursor": {
                    "categoryBalloonEnabled": false,
                    "cursorAlpha": 0,
                    "zoomable": false
                },
                "categoryField": "title",
                "categoryAxis": {
                    "gridPosition": "start",
                    "gridAlpha": 0,
                    "tickPosition": "start",
                    "tickLength": 20
                },
                "labelPosition": "right",
                "balloonText": "[[title]]: [[value]] [[description]]",
                "export": {
                    "enabled": true,
                    "libs": {
                        "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                    }
                }
            });
            AmCharts.makeChart("collection_statistics_graph", {
                "type": "serial",
                "theme": "light",
                "autoMargins": true,
                "marginLeft": 30,
                "marginRight": 8,
                "marginTop": 10,
                "marginBottom": 26,
                "fontFamily": 'Open Sans',
                "color": '#888',

                "dataProvider": {!! \App\Helpers\GeneralHelper::collection_overview_graph() !!},
                "valueAxes": [{
                    "axisAlpha": 0,

                }],
                "startDuration": 1,
                "graphs": [{
                    "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                    "bullet": "round",
                    "bulletSize": 8,
                    "lineColor": "#370fc6",
                    "lineThickness": 4,
                    "negativeLineColor": "#0dd102",
                    "title": "{{trans_choice('general.actual',1)}}",
                    "type": "smoothedLine",
                    "valueField": "actual"
                }, {
                    "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                    "bullet": "round",
                    "bulletSize": 8,
                    "lineColor": "#d1655d",
                    "lineThickness": 4,
                    "negativeLineColor": "#d1cf0d",
                    "title": "{{trans_choice('general.expected',2)}}",
                    "type": "smoothedLine",
                    "valueField": "expected"
                }],
                "categoryField": "month",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "tickLength": 0,
                    "labelRotation": 30,

                }, "export": {
                    "enabled": true,
                    "libs": {
                        "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                    }
                }, "legend": {
                    "position": "bottom",
                    "marginRight": 100,
                    "autoMargins": false
                },


            });
            
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
    console.log(collections.reverse())
    console.log(given_out.reverse())

    var dates =  
    <?php echo json_encode($target_dates); ?>; 
    console.log(dates.reverse())

    const allchrt = document.getElementById('companygraph');


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

@if($role->role_id == '6')
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

  //console.log($trans)
    var given_out =  
    <?php echo json_encode($given_out); ?>; 
    console.log(collections)
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
@endif

@if($role->role_id == '4') 
<script>
function toggleDiv(divid)
  {
 
    varon = divid + 'on';
    varoff = divid + 'off';
    varoff2 = divid + 'staff';
 
    if(document.getElementById(varon).style.display == 'block')
    {
    document.getElementById(varon).style.display = 'none';
    document.getElementById(varoff2).style.display = 'none';
    document.getElementById(varoff).style.display = 'block';
    }
   
    else
    {  
    document.getElementById(varoff).style.display = 'none';
    document.getElementById(varon).style.display = 'block'
    }
} 


function toggleMyStaff(divid){
    varon = divid + 'on';
    varoff = divid + 'staff';
    varoff2 = divid + 'off'

    if(document.getElementById(varon).style.display == 'block')
    {
    document.getElementById(varon).style.display = 'none';
    document.getElementById(varoff2).style.display = 'none';
    document.getElementById(varoff).style.display = 'block';
    }
   
    else
    {  
    document.getElementById(varoff).style.display = 'none';
    document.getElementById(varon).style.display = 'block'
    }
}

function toggleLedger(divid)
  {
 
    varon = divid + 'on_new';
    varoff = divid + 'off_new';
 
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

    
const branchmonths = ["January","February","March","April","May","June","July","August","September","October","November","December"];
let branchtargetDateName = branchmonths[new Date('{{$branchtargetDate}}').getMonth()]
 var branchCycleDate = document.getElementById('branchCycle').innerHTML = '24th ' + branchtargetDateName
 let branchfirstDateName = branchmonths[new Date('{{$branchfirstDate}}').getMonth()];
 let branchsecondDateName = branchmonths[new Date('{{$branchsecondDate}}').getMonth()]

const gaugeElementBranch = document.querySelector(".gauge");

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

setGaugeValue(gaugeElementBranch, '{{($collected_total/$cycle_opening_uncollected_amount)}}');


const cty = document.getElementById('branchgraph');

new Chart(cty, {
  type: 'bar',
  data: {
    labels: ['24 ' + branchfirstDateName, '24 ' + branchsecondDateName, '24 ' + branchtargetDateName,],
    datasets: [{
      label: 'Branch collections as at end of cycle date',
      data: ['{{$collected_total_1_months}}','{{$collected_total_2_months}}','{{$collected_total}}'],
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
@endif 

@if($role->role_id == '3')
@if($end !== 'NCI')
        <script>
          //  console.log('hello')
            //Setting up the cycle count down


function toggleDiv(divid)
  {
 
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




function toggleLedger(divid)
  {
 
    varon = divid + 'on_new';
    varoff = divid + 'off_new';
 
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


                const d = new Date();
let month = d.getMonth() + 1;
let year = d.getFullYear();
let day = d.getDate();
let todayDate = year + '-' + month + '-' + day;
let countDownEndDate = year + '-' + month + '-' + '{{$end}}'
if(todayDate >= countDownEndDate){
    countDownEndDate = year + '-' + (month + 1) + '-' + '{{$end}}'
}
const NewCountDownEndDate = new Date(countDownEndDate);
var BrandNewCountDownEndDate = new Date(NewCountDownEndDate).getTime();

var x = setInterval(function(){
    var now = new Date().getTime();
    var distance = BrandNewCountDownEndDate - now;
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s";

});



// var layout = { width: 400, height: 300, margin: { t: 0, b: 0 },  paper_bgcolor: "transparent", };
// Plotly.newPlot('pdua', data, layout);
console.log('{{$disbursed_amount}}')
console.log('{{$compareDate}}')

const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
let firstDateName = months[new Date('{{$firstDate}}').getMonth()];
let secondDateName = months[new Date('{{$secondDate}}').getMonth()]
let targetDateName = months[new Date('{{$targetDate}}').getMonth()]
console.log(firstDateName)
// var dateTarget = document.getElementById("target").innerHTML = '24 ' + targetDateName;
const ctx = document.getElementById('graph');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['24 ' + firstDateName, '24 ' + secondDateName, '24 ' + targetDateName,],
    datasets: [{
      label: 'Your collections as at end of cycle date',
      data: ['{{$collected_total_1_months }}','{{$collected_total_2_months}}','{{$collected_total}}'],
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

const gaugeElement = document.querySelector(".gauge");

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

setGaugeValue(gaugeElement, '{{($collected_total/$cycle_opening_uncollected_amount)}}');

$('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });            


        </script>
        @endif
        @endif
@endsection
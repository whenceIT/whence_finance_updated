@extends('layouts.master')
@section('title')
    {{$office->name}}
@endsection

@section('content')

<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">
       
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
    },
]
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
@extends('layouts.master')
@section('title')
My collections
@endsection
@section('content')
<div>
<?php
$todaysDate = date('Y-m-d');
$thisWeekDate = date('Y-m-d',strtotime($todaysDate. ' + 7 days'));
$thisMonthDate = date('Y-m-d',strtotime($todaysDate. ' + 1 months'));
$first =  strtotime('2023-09-01');
$second =  strtotime('2023-09-20');

$result = $second - $first;
$newResult = $result/86400;

function compare($a,$b){
    return $a->first_repayment_date <=> $b->first_repayment_date;
}
usort($LoanArray,"compare");

?>
<!-- <p>{{$newResult}}</p>
@foreach($LoanArray as $loan)
<p>{{$loan->first_repayment_date}}</p>
@endforeach -->

<div style="display: flex;
    align-items: center;
    justify-content: center; padding-bottom: 10px; ">

<a href="javascript:;" onmousedown="toggleToday('todayDiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">Today</span>
</a>

<a href="javascript:;" onmousedown="toggleThisWeek();" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">This Week</span>
</a>

<a href="javascript:;" onmousedown="toggleThisMonth('thisMonthDiv');" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">This Month</span>
</a>

<!-- <a href="javascript:;" onmousedown="toggleAllDiv('allDiv');" style="margin: 10px;">
<span class="label label-default" style="font-size: 15px;">All</span>
</a> -->
</div>
<div id='todayDiv' style="display:block"  class="box-body table-responsive">
<div class="box box-primary">
<div class="box-header with-border">
<h2 class="box-title" style="font-weight: bold;">LOANS DUE TODAY</h2>
</div>
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Loan Consultant</th>
    <th>Balance</th>
    <th>Due Date</th>
    </tr>
</thead>
<tbody>
    @foreach($BranchLoans as $Loan)
    <?php
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;
?>
    @foreach($Loan->transactions as $transaction)
<?php
    $out = $out + $transaction->debit;

if($transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}

if($transaction->transaction_type == 'specified_due_date_fee'){
    $newout = $newout + $transaction->debit;
}

if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
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
    @if($Loan->first_repayment_date == $todaysDate)
    <tr>
    <td>
        @if($reloansCount > 0)
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a><span style="color: blue;">(Reloan)</span>
        @else
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a>
        @endif
    </td>
        <td>{{$Loan->client->first_name}} {{$Loan->client->last_name}}</td>
        <td>{{$Loan->loan_officer->first_name}} {{$Loan->loan_officer->last_name}}</td>
        <td>{{number_format($OutIn,2)}}</td>
        <td style="font-weight: bold;">{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
    </tr>
    @endif
    @endforeach
</tbody>
</table>
</div>
</div>

<div id='thisWeekDiv' style="display:none"  class="box-body table-responsive">
<div class="box box-primary">
<div class="box-header with-border">
<h2 class="box-title" style="font-weight: bold;">LOANS DUE THIS WEEK</h2>
</div>
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Loan Consultant</th>
    <th>Balance</th>
    <th>Due Date</th>
    </tr>
</thead>
<tbody>
    @foreach($LoanArray as $Loan)
    <?php
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;
?>
    @foreach($Loan->transactions as $transaction)
<?php
    $out = $out + $transaction->debit;

if($transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}

if($transaction->transaction_type == 'specified_due_date_fee'){
    $newout = $newout + $transaction->debit;
}

if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
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
    @if($Loan->first_repayment_date >= $todaysDate && $Loan->first_repayment_date <= $thisWeekDate)
    <tr>
    <td>
        @if($reloansCount > 0)
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a><span style="color: blue;">(Reloan)</span>
        @else
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a>
        </td>
        @endif
        <td>{{$Loan->client->first_name}} {{$Loan->client->last_name}}</td>
        <td>{{$Loan->loan_officer->first_name}} {{$Loan->loan_officer->last_name}}</td>
        <td>{{number_format($OutIn,2)}}</td>
        <td style="font-weight: bold;">{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
    </tr>
    @endif
    @endforeach
</tbody>
</table>
</div>
</div>

<div id='thisMonthDiv' style="display:none"  class="box-body table-responsive">
<div class="box box-primary">
<div class="box-header with-border">
<h2 class="box-title" style="font-weight: bold;">LOANS DUE THIS MONTH</h2>
</div>
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Loan Consultant</th>
    <th>Balance</th>
    <th>Due Date</th>
    </tr>
</thead>
<tbody>
    @foreach($LoanArray as $Loan)
    <?php
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;
?>
    @foreach($Loan->transactions as $transaction)
<?php
    $out = $out + $transaction->debit;

if($transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}

if($transaction->transaction_type == 'specified_due_date_fee'){
    $newout = $newout + $transaction->debit;
}

if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
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
    @if($Loan->first_repayment_date >= $todaysDate && $Loan->first_repayment_date <= $thisMonthDate)
    <tr>
    <td>
        @if($reloansCount > 0)
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a><span style="color: blue;">(Reloan)</span>
        @else
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a>
        </td>
        @endif
        <td>{{$Loan->client->first_name}} {{$Loan->client->last_name}}</td>
        <td>{{$Loan->loan_officer->first_name}} {{$Loan->loan_officer->last_name}}</td>
        <td>{{number_format($OutIn,2)}}</td>
        <td style="font-weight: bold;">{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
    </tr>
    @endif
    @endforeach
</tbody>
</table>
</div>
</div>

<div id='allDiv' style="display:none"  class="box-body table-responsive">

</div> 

</div>
@endsection
@section('footer-scripts')
<script>

var today = document.getElementById('todayDiv');
        var thisWeek = document.getElementById('thisWeekDiv');
        var thisMonth = document.getElementById('thisMonthDiv');
        var allDiv = document.getElementById('allDiv');

    function toggleToday(){
       

       

    if(today.style.display == 'none'){
            thisWeek.style.display = 'none'
            thisMonth.style.display = 'none'
            allDiv.style.display = 'none'
            today.style.display = 'block'
        }
    }
    function toggleThisWeek(){
    


    if(thisWeek.style.display == 'none'){
            today.style.display = 'none'
            thisMonth.style.display = 'none'
            allDiv.style.display = 'none'
            thisWeek.style.display = 'block'
        }
    }


    function toggleThisMonth(){
    
    if(thisMonth.style.display == 'none'){
            today.style.display = 'none'
            thisWeek.style.display = 'none'
            allDiv.style.display = 'none'
            thisMonth.style.display = 'block'
        }
    }

    function toggleAllDiv(){
    if(allDiv.style.display == 'none'){
            today.style.display = 'none'
            thisWeek.style.display = 'none'
            thisMonth.style.display = 'none'
            allDiv.style.display = 'block'
        }
    }
</script>
@endsection
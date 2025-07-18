<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
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
        white-space: pre-wrap;
        background-color: #339933;
    }
    .style-2 {
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
        white-space: pre-wrap;
    }
    .style-3 {
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
        white-space: pre-wrap;
    }
</style>


<?php 
function compare($a,$b){
    return $a->first_repayment_date <=> $b->first_repayment_date;
}

function compareTwo($a,$b){
    return $b->first_repayment_date <=>  $a->first_repayment_date;
}

usort($LoanArray,"compare");
usort($LoanArrayTwo,"compareTwo")
?>

<p>{{$branch_name->name}} uncollected loans report</p>
<?php 
$balance = 0;
$interest = 0;
$amount = 0;
$total = 0;
?>

<?php
 $thisWeekTotal = 0;
 $allTimeTotal = 0;
 $today = date('Y-m-d');
 $last_month = date('Y-m',strtotime($today. '- 1 month'));
 $cycle_date = $last_month.'-'.'31';
 $period_start = '2023-01-01';
 $collected_total = 0;
?>

<?php
foreach($LoanArray as $Loan){

    $OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;

foreach($Loan->transactions as $transaction){

    $collected_total = $collected_total + $transaction->credit;
$out = $out + $transaction->debit;

if($transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}


if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
 }

}

$OutIn = $out - $in;


if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate){
    $thisWeekTotal = $thisWeekTotal + $OutIn;
}
if($Loan->first_repayment_date >= $period_start && $Loan->first_repayment_date <= $cycle_date){
$allTimeTotal = $allTimeTotal + $OutIn;
}
}
?>
<div style="padding-top: 10px;">
<div class="row">
<p>Uncollected balance as at {{date("jS M, Y",strtotime($cycle_date))}}: <span style="font-weight: bold; font-size:large">K{{number_format($allTimeTotal,2)}}</span></p>
</div>
<div class="box box-primary">
<div class="box-body table-responsive" >
<table class="table  table-bordered table-hover table-striped" id="data-table-2">
    <thead>
        
    </thead>
</table>

<table class="table table-bordered table-hover table-striped" id="data-table">
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
<?php
     $collected_total = 0;
    ?>
    @foreach($LoanArray as $Loan)
    <?php
      $OutIn = 0;
      $out = 0;
      $in = 0;
    ?>
    @foreach($Loan->transactions as $transaction)
<?php
    $collected_total = $collected_total + $transaction->credit;
    $out = $out + $transaction->debit;
    
    // if($transaction->transaction_type != 'interest_waiver'){
    //     $in = $in + $transaction->credit;
    // }

    $in = $in + $transaction->credit;
    
?>
    @endforeach
    <?php
    $OutIn = $out - $in;
    ?>
    @if($OutIn != 0 && $OutIn > 0)
    <tr>
    <td>
        @if($reloansCount > 0)
        <p>{{$Loan->id}}<span style="color: blue;">(Reloan)</span></p>
        @else
        <p>{{$Loan->id}}</p>
        </td>
        @endif
        <td>
        @if(!empty($Loan->client->first_name))
            {{$Loan->client->first_name}}
        @endif      
        @if(!empty($Loan->client->last_name)) 
            {{$Loan->client->last_name}}</td>
        @endif   
        <td>
        @if(!empty($Loan->loan_officer->first_name))
            {{$Loan->loan_officer->first_name}}
        @endif  
        @if(!empty($Loan->loan_officer->last_name))   
            {{$Loan->loan_officer->last_name}}
        @endif      
        </td>
        <td>{{number_format($OutIn,2)}}</td>
        <td style="font-weight: bold;">{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
    </tr>
    @endif
    @endforeach
</tbody>
</table>
</div>
</div> 
 
</div>

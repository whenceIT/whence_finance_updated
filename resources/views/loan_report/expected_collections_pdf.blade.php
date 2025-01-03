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
<p>{{$branch_name->name}} expected collections for period {{date("jS M, Y", strtotime($compareDate))}} to {{date("jS M, Y", strtotime($targetDate))}}</p>
<?php 
$balance = 0;
$interest = 0;
$amount = 0;
$total = 0;
?>
<div style="padding-top: 10px;">

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
    <th>Type</th>
    <th>Balance</th>
    <th>Due Date</th>

    </tr>
</thead>
<tbody>
    @foreach($transactionList as $transaction)
    <tr>
@if(!empty($transaction->Loan->id))
<td>
{{$transaction->Loan->id}}
</td>
@endif
<td>
@if(!empty($transaction->Loan->client->first_name))
    {{$transaction->Loan->client->first_name}} 
@endif    
@if(!empty($transaction->Loan->client->last_name))
    {{$transaction->Loan->client->last_name}}</td>
@endif    
<td>@if(!empty($transaction->Loan->loan_officer->first_name))
    {{$transaction->Loan->loan_officer->first_name}} 
@endif
@if(!empty($transaction->Loan->loan_officer->last_name))
    {{$transaction->Loan->loan_officer->last_name}}</td>
@endif    
@if($transaction->payment_apply_to == 'reloan_payment')
<td>Reloan</td>
@elseif($transaction->transaction_type == 'disbursement')
<td>New Loan</td>
@else
<td></td>
@endif
<?php 
if($transaction->transaction_type == 'disbursement'){
    $interest = $transaction->debit * 0.4;
    $balance = $transaction->debit + $interest;
}elseif($transaction->payment_apply_to == 'reloan_payment'){
    $amount = $transaction->balance_bf - $transaction->credit;
    $interest = $amount * 0.4;
    $balance = $amount + $interest;
}
?>
<td>{{$balance}}</td>
<?php
$due_date = date('Y-m-d',strtotime($transaction->date. '+ 1 months'))
?>
<td>{{date("jS M, Y",strtotime($due_date))}}</td>
    
    </tr>
    <?php
    $total = $total + $balance;
    $interest = 0;
    $balance = 0;
    $amount = 0;
    ?>
    @endforeach
</tbody>
</table>
</div>
</div> 
 
<p style="font-weight: bold; font-size:large">EXPECTED COLLECTED TOTAL: K{{number_format($total,2)}}</p>
</div>
